<?php
/**
 * La Bonne Occaz - Connexion BDD
 * Copiez ce fichier en database.php et adaptez les valeurs
 */
class Database {
    private static ?PDO $instance = null;
    private static string $host     = 'localhost';
    private static string $dbname   = 'labonneoccaz';
    private static string $user     = 'root';
    private static string $password = '';   // ← à modifier
    private static string $charset  = 'utf8mb4';

    private function __construct() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host='.self::$host.';dbname='.self::$dbname.';charset='.self::$charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, self::$user, self::$password, $options);
            } catch (PDOException $e) {
                error_log('DB Error: ' . $e->getMessage());
                die('Erreur de connexion à la base de données.');
            }
        }
        return self::$instance;
    }
}
