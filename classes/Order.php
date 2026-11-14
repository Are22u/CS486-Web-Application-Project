<?php
/**
 * Order — placing orders at checkout, customer order history, admin order management.
 */
class Order
{
    public const STATUSES = ['pending', 'confirmed', 'preparing', 'out for delivery', 'delivered', 'cancelled'];

    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Validates the checkout form. Returns errors keyed by field name. */
    public function validateCheckout(array $data): array
    {
        $errors = [];

        if (mb_strlen($data['recipient_name']) < 2) {
            $errors['recipient_name'] = 'Please enter the recipient\'s name.';
        }
        if (!preg_match('/^05\d{8}$/', $data['phone'])) {
            $errors['phone'] = 'Phone must be 10 digits starting with 05.';
        }
        if (mb_strlen($data['delivery_address']) < 5) {
            $errors['delivery_address'] = 'Please enter the full delivery address.';
        }

        $date = DateTime::createFromFormat('Y-m-d', $data['delivery_date']);
        $tomorrow = new DateTime('tomorrow');
        if (!$date || $date->format('Y-m-d') !== $data['delivery_date']) {
            $errors['delivery_date'] = 'Please choose a delivery date.';
        } elseif ($date < $tomorrow) {
            $errors['delivery_date'] = 'Delivery date must be tomorrow or later.';
        }

        if (mb_strlen($data['notes']) > 255) {
            $errors['notes'] = 'Notes must be 255 characters or fewer.';
        }

        return $errors;
    }

    /**
     * Turns the cart into an order. Everything happens inside one transaction:
     * if any step fails (e.g. a product sold out a second ago), nothing is saved.
     *
     * Returns the new order_id. Throws RuntimeException with a user-friendly message on failure.
     */
    public function create(int $userId, array $cartItems, array $data): int
    {
        if (empty($cartItems)) {
            throw new RuntimeException('Your cart is empty.');
        }

        $this->db->beginTransaction();
        try {
            $total = 0;
            $lines = [];

            foreach ($cartItems as $item) {
                // FOR UPDATE locks the row so two customers can't buy the last bouquet at once
                $product = $this->db->fetch(
                    'SELECT id AS product_id, name, price, stock FROM products WHERE id = ? FOR UPDATE',
                    [$item['id']]
                );
                if ($product === null) {
                    throw new RuntimeException('A product in your cart is no longer available.');
                }
                if ($item['qty'] > $product['stock']) {
                    throw new RuntimeException("Sorry, only {$product['stock']} of \"{$product['name']}\" left in stock.");
                }
                $total  += $product['price'] * $item['qty'];
                $lines[] = ['product_id' => $product['product_id'], 'qty' => $item['qty'], 'price' => $product['price']];
            }

            $this->db->execute(
                'INSERT INTO orders (user_id, total, recipient_name, phone, delivery_address, delivery_date, notes)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [
                    $userId,
                    round($total, 2),
                    $data['recipient_name'],
                    $data['phone'],
                    $data['delivery_address'],
                    $data['delivery_date'],
                    $data['notes'] !== '' ? $data['notes'] : null,
                ]
            );
            $orderId = $this->db->lastInsertId();

            foreach ($lines as $line) {
                $this->db->execute(
                    'INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)',
                    [$orderId, $line['product_id'], $line['qty'], $line['price']]
                );
                $this->db->execute(
                    'UPDATE products SET stock = stock - ? WHERE id = ?',
                    [$line['qty'], $line['product_id']]
                );
            }

            $this->db->commit();
            return $orderId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** A customer's own orders, newest first (my-orders.php). */
    public function forUser(int $userId): array
    {
        return $this->db->fetchAll(
            'SELECT o.*, o.id AS order_id, o.created_at AS order_date, o.total AS total_amount,
                    COALESCE(SUM(i.quantity), 0) AS item_count
             FROM orders o
             LEFT JOIN order_items i ON i.order_id = o.id
             WHERE o.user_id = ?
             GROUP BY o.id
             ORDER BY o.created_at DESC',
            [$userId]
        );
    }

    /**
     * One order with customer info. Pass $userId to make sure a customer
     * can only open THEIR OWN order (changing ?id= in the URL won't work).
     */
    public function find(int $orderId, ?int $userId = null): ?array
    {
        $sql = 'SELECT o.*, o.id AS order_id, o.created_at AS order_date, o.total AS total_amount,
                       u.full_name AS customer_name, u.email AS customer_email
                FROM orders o
                JOIN users u ON u.id = o.user_id
                WHERE o.id = ?';
        $params = [$orderId];

        if ($userId !== null) {
            $sql .= ' AND o.user_id = ?';
            $params[] = $userId;
        }
        return $this->db->fetch($sql, $params);
    }

    public function items(int $orderId): array
    {
        return $this->db->fetchAll(
            'SELECT i.*, i.unit_price AS price, p.name, p.image, (i.quantity * i.unit_price) AS line_total
             FROM order_items i
             JOIN products p ON p.id = i.product_id
             WHERE i.order_id = ?',
            [$orderId]
        );
    }

    /** Every order for the admin, optionally filtered by status. */
    public function all(?string $status = null): array
    {
        $sql = 'SELECT o.*, o.id AS order_id, o.created_at AS order_date, o.total AS total_amount,
                       u.full_name AS customer_name
                FROM orders o
                JOIN users u ON u.id = o.user_id';
        $params = [];

        if ($status !== null && in_array($status, self::STATUSES, true)) {
            $sql .= ' WHERE o.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY o.created_at DESC';

        return $this->db->fetchAll($sql, $params);
    }

    public function recent(int $limit = 5): array
    {
        return $this->db->fetchAll(
            'SELECT o.id AS order_id, o.created_at AS order_date, o.total AS total_amount, o.status, u.full_name AS customer_name
             FROM orders o JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC LIMIT ' . max(1, $limit)
        );
    }

    /** Admin changes an order's status. Cancelling puts the stock back. */
    public function updateStatus(int $orderId, string $status): bool
    {
        if (!in_array($status, self::STATUSES, true)) {
            return false;
        }
        $order = $this->find($orderId);
        if ($order === null) {
            return false;
        }

        $this->db->beginTransaction();
        try {
            $wasCancelled = $order['status'] === 'cancelled';
            $nowCancelled = $status === 'cancelled';

            if ($wasCancelled !== $nowCancelled) {
                // cancelling returns stock (+), un-cancelling takes it again (-)
                $sign = $nowCancelled ? '+' : '-';
                foreach ($this->items($orderId) as $item) {
                    $this->db->execute(
                        "UPDATE products SET stock = stock $sign ? WHERE id = ?",
                        [$item['quantity'], $item['product_id']]
                    );
                }
            }

            $this->db->execute('UPDATE orders SET status = ? WHERE id = ?', [$status, $orderId]);
            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Numbers for the admin dashboard. */
    public function stats(): array
    {
        $row = $this->db->fetch(
            "SELECT COUNT(*) AS total_orders,
                    SUM(status = 'pending') AS pending_orders,
                    COALESCE(SUM(CASE WHEN status <> 'cancelled' THEN total END), 0) AS revenue
             FROM orders"
        );
        return [
            'total_orders'   => (int) $row['total_orders'],
            'pending_orders' => (int) $row['pending_orders'],
            'revenue'        => (float) $row['revenue'],
        ];
    }
}
