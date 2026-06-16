<?php
// ── Database Configuration ─────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'nexttix');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');

class DB {
    private static ?PDO $pdo = null;

    public static function conn(): PDO {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT
                 . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }

    // Ambil banyak baris
    public static function rows(string $sql, array $params = []): array {
        $stmt = self::conn()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Ambil satu baris
    public static function row(string $sql, array $params = []): ?array {
        $stmt = self::conn()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Ambil satu kolom satu baris
    public static function val(string $sql, array $params = []): mixed {
        $stmt = self::conn()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    // INSERT / UPDATE / DELETE
    public static function run(string $sql, array $params = []): PDOStatement {
        $stmt = self::conn()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Insert dan return last insert id
    public static function insert(string $sql, array $params = []): int {
        self::run($sql, $params);
        return (int)self::conn()->lastInsertId();
    }
}
