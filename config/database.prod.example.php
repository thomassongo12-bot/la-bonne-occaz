<?php
/**
 * La Bonne Occaz — Connexion BDD PRODUCTION (Hostinger)
 * Renommez ce fichier en database.php et remplissez vos infos Hostinger
 */
class Database {
    private static ?PDO $instance = null;

    // ⚠️ Remplacez par vos vraies valeurs Hostinger
    // Trouvez-les dans : hPanel > Bases de données > MySQL
    private static string $host     = 'localhost';          // toujours localhost sur Hostinger
    private static string $dbname   = 'u123456789_lbo';     // ex: u123456789_labonneoccaz
    private static string $user     = 'u123456789_lbo';     // ex: u123456789_lbouser
    private static string $password = 'VotreMotDePasse';    // mot de passe BDD Hostinger
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
                die('Erreur de connexion. Veuillez réessayer plus tard.');
            }
        }
        return self::$instance;
    }
}
