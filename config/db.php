<?php
/**
 * Number Gear — Database Connection (XAMPP / MySQL)
 * ---------------------------------------------------
 * Default XAMPP settings: host=localhost, user=root, password=''
 * Change the constants below if your setup is different.
 *
 * Before first use:
 *   1. Start Apache + MySQL in the XAMPP control panel.
 *   2. Open phpMyAdmin (http://localhost/phpmyadmin).
 *   3. Import database/schema.sql to create the `number_gear` database
 *      and its tables.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'number_gear');
define('DB_USER', 'root');
define('DB_PASS', '');        // default XAMPP root password is empty
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a shared PDO instance (created once per request).
 */
function ng_db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Don't leak credentials/connection details to the browser.
            error_log('Number Gear DB connection failed: ' . $e->getMessage());
            http_response_code(500);
            die('Database connection failed. Please make sure MySQL is running in XAMPP '
              . 'and that the "number_gear" database has been imported (see database/schema.sql).');
        }
    }

    return $pdo;
}
