# Guide de déploiement — La Bonne Occaz sur Hostinger

## Prérequis
- Hébergement Hostinger Premium ou Business (PHP 8.2+, MySQL)
- Accès hPanel Hostinger
- FileZilla ou accès FTP/SFTP
- Votre domaine configuré sur Hostinger

---

## ÉTAPE 1 — Créer la base de données MySQL sur Hostinger

1. Connectez-vous à **hPanel** → **Bases de données** → **MySQL**
2. Créez une nouvelle base de données :
   - Nom : `labonneoccaz`
   - Utilisateur : créez un utilisateur dédié
   - Notez : **hôte**, **nom BDD**, **utilisateur**, **mot de passe**
3. Dans **phpMyAdmin** (hPanel → Bases de données → phpMyAdmin) :
   - Sélectionnez votre BDD dans la liste à gauche
   - Onglet **Importer** → choisissez `database/schema_hostinger.sql` → Exécuter
   - Onglet **Importer** → choisissez `database/seeds_hostinger.sql` → Exécuter

   > ⚠️ **Important** : utilisez `schema_hostinger.sql` et `seeds_hostinger.sql`
   > (et non `schema.sql` / `seeds.sql` qui contiennent `CREATE DATABASE` et `USE`
   > — ces commandes échouent sur Hostinger car vous n'avez pas ces droits)

---

## ÉTAPE 2 — Configurer les fichiers

### A. Fichier database.php
Copiez `config/database.prod.example.php` → renommez en `config/database.php`
Remplissez avec vos infos Hostinger :
```php
private static string $host     = 'localhost';
private static string $dbname   = 'u123456789_labonneoccaz';  // votre nom BDD
private static string $user     = 'u123456789_lbouser';        // votre utilisateur
private static string $password = 'VotreMotDePasseBDD';
```

### B. Fichier index.php — adapter BASE_URL
Dans `index.php`, la ligne BASE_URL est automatique (détecte HTTP_HOST).
Vérifiez que DEBUG_MODE est à `false` :
```php
define('DEBUG_MODE', false);
```

---

## ÉTAPE 3 — Upload des fichiers via FTP

### Connexion FTP
- Hôte : votre domaine ou IP FTP (hPanel → FTP)
- Port : 21
- Utilisateur / Mot de passe : créé dans hPanel → Comptes FTP

### Structure à uploader dans public_html/

```
public_html/
├── .htaccess              ← IMPORTANT (du dossier racine du projet)
├── index.php              ← fichier racine du projet
├── config/
│   └── database.php       ← votre config BDD Hostinger (PAS database.example.php)
├── controllers/
├── models/
├── views/
├── public/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/           ← dossier vide (sera rempli par les uploads)
└── database/
    ├── schema.sql
    └── seeds.sql
```

**⚠️ NE PAS uploader :**
- `.git/` (dossier Git)
- `config/database.example.php`
- `config/database.prod.example.php`
- `index.prod.php`
- `DEPLOIEMENT_HOSTINGER.md`

---

## ÉTAPE 4 — Configurer le .htaccess

Remplacez le `.htaccess` à la racine par ce contenu :
```apache
Options -Indexes
RewriteEngine On

# Forcer HTTPS (Hostinger fournit SSL gratuit)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

---

## ÉTAPE 5 — Permissions des dossiers

Dans hPanel → Gestionnaire de fichiers, vérifiez les permissions :
- `public/uploads/` → **755**
- `public/uploads/vehicles/` → **755**
- `public/uploads/branding/` → **755**

Ou via FTP avec FileZilla : clic droit → Permissions du fichier → 755

---

## ÉTAPE 6 — Déploiement via GitHub (méthode recommandée)

Dans hPanel → **Git** (si disponible sur votre plan) :
1. Repo URL : `https://github.com/thomassongo12-bot/la-bonne-occaz`
2. Branche : `main`
3. Chemin : `/public_html`

**Ou manuellement** : téléchargez le ZIP depuis GitHub et uploadez via FTP.

---

## ÉTAPE 7 — Vérifications post-déploiement

1. **Page d'accueil** charge correctement
2. **Admin** accessible sur `https://votredomaine.com/admin`
3. **Connexion admin** : `admin` / `Admin2024` (changez immédiatement !)
4. **Upload photos** fonctionne (testez dans Admin → Véhicules)
5. **SSL** actif (hPanel → SSL → Let's Encrypt → Activer)

---

## Problèmes fréquents

| Problème | Solution |
|---|---|
| Page blanche | Vérifiez `config/database.php`, activez les logs (`DEBUG_MODE = true` temporairement) |
| 500 Internal Server Error | Vérifiez le `.htaccess`, PHP version dans hPanel |
| Images non affichées | Vérifiez permissions du dossier `uploads/` (755) |
| BDD ne se connecte pas | Vérifiez nom BDD/utilisateur dans hPanel, host = `localhost` |
| URL ne fonctionne pas | Vérifiez que `mod_rewrite` est activé (hPanel → PHP → Extensions) |

---

## Mise à jour du site

Après chaque modification en local :
```bash
git add -A
git commit -m "description"
git push
```
Puis sur Hostinger : re-téléchargez et uploadez les fichiers modifiés via FTP.
