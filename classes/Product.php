<?php
/**
 * Product — reading products for the shop, and admin CRUD.
 */
class Product
{
    public const COLORS = ['pink', 'red', 'white', 'yellow', 'purple', 'mixed'];

    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // =====================================================
    // READ
    // =====================================================

    /**
     * All products with their category, for shop.php.
     * Member 1 loops over this and prints one product card per row,
     * keeping the data-occasion / data-color / data-price attributes so main.js filters still work:
     *
     *   foreach ((new Product())->all() as $p):
     *     <article class="product-card" data-occasion="<?= e($p['category_slug']) ?>"
     *              data-color="<?= e($p['color']) ?>" data-price="<?= e($p['price']) ?>" ...>
     */
    public function all(): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, p.id AS product_id, p.occasion_id AS category_id, c.name AS category_name, c.slug AS category_slug
             FROM products p
             JOIN occasions c ON c.id = p.occasion_id
             ORDER BY p.id'
        );
    }

    /** A few products for the homepage "featured" grid. */
    public function featured(int $limit = 4): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, p.id AS product_id, p.occasion_id AS category_id, c.name AS category_name, c.slug AS category_slug
             FROM products p
             JOIN occasions c ON c.id = p.occasion_id
             WHERE p.stock > 0
             ORDER BY (p.tag IS NULL), p.id
             LIMIT ' . max(1, $limit)
        );
    }

    public function find(int $productId): ?array
    {
        return $this->db->fetch(
            'SELECT p.*, p.id AS product_id, p.occasion_id AS category_id, c.name AS category_name, c.slug AS category_slug
             FROM products p
             JOIN occasions c ON c.id = p.occasion_id
             WHERE p.id = ?',
            [$productId]
        );
    }

    public function categories(): array
    {
        return $this->db->fetchAll('SELECT id AS category_id, name, slug FROM occasions ORDER BY id');
    }

    public function count(): int
    {
        return (int) $this->db->fetch('SELECT COUNT(*) AS n FROM products')['n'];
    }

    public function lowStock(int $threshold = 5): array
    {
        return $this->db->fetchAll(
            'SELECT id AS product_id, name, stock FROM products WHERE stock <= ? ORDER BY stock',
            [$threshold]
        );
    }

    // =====================================================
    // CREATE / UPDATE / DELETE  (admin only)
    // =====================================================

    /** Validates the admin product form. Image is required only when creating. */
    public function validate(array $data, ?array $file, bool $isNew): array
    {
        $errors = [];

        if (mb_strlen($data['name']) < 3) {
            $errors['name'] = 'Name must be at least 3 characters.';
        }
        if (mb_strlen($data['description']) < 10) {
            $errors['description'] = 'Description must be at least 10 characters.';
        }
        if (!is_numeric($data['price']) || $data['price'] <= 0) {
            $errors['price'] = 'Price must be a positive number.';
        }
        if (!ctype_digit($data['stock'])) {
            $errors['stock'] = 'Stock must be a whole number (0 or more).';
        }
        if (!in_array($data['color'], self::COLORS, true)) {
            $errors['color'] = 'Please choose a colour.';
        }
        $categoryIds = array_column($this->categories(), 'category_id');
        if (!in_array((int) $data['category_id'], array_map('intval', $categoryIds), true)) {
            $errors['category_id'] = 'Please choose an occasion.';
        }

        $hasFile = $file && $file['error'] !== UPLOAD_ERR_NO_FILE;
        if ($isNew && !$hasFile) {
            $errors['image'] = 'Please upload a product image.';
        } elseif ($hasFile) {
            $imageError = $this->checkImage($file);
            if ($imageError) {
                $errors['image'] = $imageError;
            }
        }

        return $errors;
    }

    private function checkImage(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Image upload failed. Please try again.';
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            return 'Image must be smaller than 2 MB.';
        }
        // Check the real file content, not just the extension the user typed
        $info = @getimagesize($file['tmp_name']);
        if ($info === false || !in_array($info['mime'], ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return 'Image must be a JPG, PNG or WEBP file.';
        }
        return null;
    }

    /** Saves the uploaded image into img/uploads/ and returns its path for the database. */
    private function saveImage(array $file): string
    {
        $ext = match (getimagesize($file['tmp_name'])['mime']) {
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };
        $filename = 'product-' . bin2hex(random_bytes(8)) . '.' . $ext;
        $target   = __DIR__ . '/../img/uploads/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new RuntimeException('Could not save the uploaded image.');
        }
        return 'img/uploads/' . $filename;
    }

    public function create(array $data, array $file): int
    {
        $this->db->execute(
            'INSERT INTO products (occasion_id, name, description, price, image, color, tag, stock)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                (int) $data['category_id'],
                $data['name'],
                $data['description'],
                $data['price'],
                $this->saveImage($file),
                $data['color'],
                $data['tag'] !== '' ? $data['tag'] : null,
                (int) $data['stock'],
            ]
        );
        return $this->db->lastInsertId();
    }

    /** Updates a product. If no new image was uploaded, the old one is kept. */
    public function update(int $productId, array $data, ?array $file): void
    {
        $existing = $this->find($productId);
        $image = $existing['image'];

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $image = $this->saveImage($file);
            $this->deleteUploadedImage($existing['image']);
        }

        $this->db->execute(
            'UPDATE products
             SET occasion_id = ?, name = ?, description = ?, price = ?, image = ?, color = ?, tag = ?, stock = ?
             WHERE id = ?',
            [
                (int) $data['category_id'],
                $data['name'],
                $data['description'],
                $data['price'],
                $image,
                $data['color'],
                $data['tag'] !== '' ? $data['tag'] : null,
                (int) $data['stock'],
                $productId,
            ]
        );
    }

    /**
     * Deletes a product. Returns false if the product appears in past orders —
     * the foreign key protects order history, so the admin should set stock to 0 instead.
     */
    public function delete(int $productId): bool
    {
        $product = $this->find($productId);
        if ($product === null) {
            return false;
        }

        try {
            $this->db->execute('DELETE FROM products WHERE id = ?', [$productId]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { // foreign key violation
                return false;
            }
            throw $e;
        }

        $this->deleteUploadedImage($product['image']);
        return true;
    }

    /** Only removes images the admin uploaded — never the template's own images. */
    private function deleteUploadedImage(string $path): void
    {
        if (str_starts_with($path, 'img/uploads/')) {
            $full = __DIR__ . '/../' . $path;
            if (is_file($full)) {
                unlink($full);
            }
        }
    }
}
