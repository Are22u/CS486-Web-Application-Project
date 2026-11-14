<?php
/**
 * init.php — include this at the very top of EVERY PHP page:
 *
 *   require_once __DIR__ . '/includes/init.php';        // pages in the root
 *   require_once __DIR__ . '/../includes/init.php';     // pages in admin/ or ajax/
 *
 * It starts the session, loads the classes, and defines small helper functions.
 */

// Saudi time for every date in PHP (XAMPP defaults to another zone, which made
// "tomorrow" in PHP differ from "tomorrow" in the browser around midnight).
date_default_timezone_set('Asia/Riyadh');

session_start();

// ---------------------------------------------------------
// Autoload classes: "new User()" loads classes/User.php automatically
// ---------------------------------------------------------
spl_autoload_register(function (string $class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/form-helpers.php';

// ---------------------------------------------------------
// BASE_URL — the project folder as seen in the browser, e.g. "/Web-project".
// Lets admin/ pages link back to the root without "../../" guessing.
// ---------------------------------------------------------
$projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$docRoot     = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '');
define('BASE_URL', ($docRoot !== '' && str_starts_with($projectRoot, $docRoot))
    ? rtrim(substr($projectRoot, strlen($docRoot)), '/')
    : '');

// Where customers land after login/logout.
// TODO (Member 1): change to 'index.php' once index.php is converted.
define('HOME_PAGE', 'index.php');

/** Builds a link relative to the project root: url('login.php') */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/** Escapes text before printing it in HTML (prevents XSS). Use on ALL user data. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Sends the browser to another page and stops the script. */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/** Formats a price consistently everywhere: money(68) -> "$68.00" */
function money($amount): string
{
    return '$' . number_format((float) $amount, 2);
}

// ---------------------------------------------------------
// Flash messages: a one-time message shown on the next page
//   setFlash('success', 'Order placed!');  ...  redirect('my-orders.php');
// ---------------------------------------------------------
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// ---------------------------------------------------------
// CSRF protection: every POST form prints csrfField() inside the <form>,
// and the page calls verifyCsrf() before processing.
// Stops other websites from submitting forms on the user's behalf.
// ---------------------------------------------------------
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function verifyCsrf(): bool
{
    $sent = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return is_string($sent) && hash_equals(csrfToken(), $sent);
}

/** Reads a trimmed POST value (empty string if missing). */
function post(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}
