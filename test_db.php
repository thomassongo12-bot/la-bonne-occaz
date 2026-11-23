<?php
/**
 * ⚠️ DIAGNOSTIC CONNEXION BDD — HOSTINGER
 * 
 * IMPORTANT : Supprimez ce fichier IMMÉDIATEMENT après utilisation !
 * Ne laissez jamais ce fichier sur le serveur en production.
 * 
 * Usage : https://votredomaine.com/test_db.php
 */

// Remplacez ces valeurs par celles de votre hPanel Hostinger
$host     = 'localhost';          // toujours localhost sur Hostinger mutualisé
$dbname   = 'VOTRE_NOM_BDD';     // ex: u123456789_labonneoccaz
$user     = 'VOTRE_UTILISATEUR';  // ex: u123456789_lbouser
$password = 'VOTRE_MOT_DE_PASSE';

echo '<h2>Diagnostic connexion BDD</h2>';
echo '<p>PHP version : ' . phpversion() . '</p>';
echo '<p>PDO disponible : ' . (class_exists('PDO') ? '<b style="color:green">OUI</b>' : '<b style="color:red">NON</b>') . '</p>';
echo '<p>Extension pdo_mysql : ' . (in_array('mysql', PDO::getAvailableDrivers()) ? '<b style="color:green">OUI</b>' : '<b style="color:red">NON</b>') . '</p>';

try {
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo '<p style="color:green"><b>✅ Connexion réussie !</b></p>';
    echo '<p>Tables trouvées : ';
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo count($tables) > 0 ? implode(', ', $tables) : '<b style="color:orange">Aucune table — importez schema_hostinger.sql</b>';
    echo '</p>';
} catch (PDOException $e) {
    echo '<p style="color:red"><b>❌ Erreur : ' . htmlspecialchars($e->getMessage()) . '</b></p>';
    echo '<p>Solutions :</p><ul>';
    echo '<li>Vérifiez le nom exact de la BDD dans hPanel → Bases de données</li>';
    echo '<li>Vérifiez le nom exact de l\'utilisateur</li>';
    echo '<li>Vérifiez le mot de passe (sensible à la casse)</li>';
    echo '<li>Host = "localhost" sur Hostinger mutualisé</li>';
    echo '</ul>';
}
echo '<p style="color:red"><b>⚠️ SUPPRIMEZ CE FICHIER IMMÉDIATEMENT APRÈS !</b></p>';
