# La Bonne Occaz — Documentation d'installation et d'administration

## Présentation

**La Bonne Occaz** est un site web professionnel de vente de véhicules d'occasion.  
Stack : PHP 8.2+ · MySQL · PDO · Bootstrap 5 · Vanilla JS · Architecture MVC.

---

## Prérequis

- **XAMPP** (ou WAMP/LAMP) avec Apache + PHP 8.2+ + MySQL
- Module Apache **mod_rewrite** activé
- Extension PHP : `pdo`, `pdo_mysql`, `fileinfo`, `gd`

---

## Installation

### 1. Placer les fichiers

Copiez le dossier dans le répertoire web d'Apache :

```
C:\xampp\htdocs\bon-occaz\
```

### 2. Créer la base de données

Ouvrez **phpMyAdmin** (`http://localhost/phpmyadmin`) et :

1. Exécutez le fichier `database/schema.sql` pour créer la structure
2. Exécutez le fichier `database/seeds.sql` pour insérer les données initiales

Ou via la ligne de commande :
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seeds.sql
```

### 3. Configurer la connexion DB

Éditez `config/database.php` et adaptez si besoin :
```php
private static string $host     = 'localhost';
private static string $dbname   = 'labonneoccaz';
private static string $user     = 'root';
private static string $password = '';
```

### 4. Activer mod_rewrite

Dans `C:\xampp\apache\conf\httpd.conf`, vérifiez :
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

Dans `C:\xampp\apache\conf\extra\httpd-vhosts.conf` (ou httpd.conf), assurez-vous que `AllowOverride All` est activé pour le répertoire htdocs.

### 5. Vérifier les permissions

Les dossiers suivants doivent être accessibles en écriture par Apache :
- `public/uploads/vehicles/`
- `public/uploads/selling/`

Sous Windows avec XAMPP, c'est généralement le cas par défaut.

---

## Accès au site

| URL | Description |
|-----|-------------|
| `http://localhost/bon-occaz/` | Site public - Accueil |
| `http://localhost/bon-occaz/vehicules` | Catalogue véhicules |
| `http://localhost/bon-occaz/vendre` | Formulaire dépôt-vente |
| `http://localhost/bon-occaz/contact` | Page contact |
| `http://localhost/bon-occaz/favoris` | Mes favoris |
| `http://localhost/bon-occaz/admin` | Administration |

---

## Connexion à l'administration

**URL :** `http://localhost/bon-occaz/admin`

| Champ | Valeur |
|-------|--------|
| Identifiant | `admin` |
| Mot de passe | `password` |

> ⚠️ **IMPORTANT :** Changez immédiatement le mot de passe après la première connexion via  
> Administration → Paramètres → Changer le mot de passe

---

## Guide d'administration

### Ajouter un véhicule

1. Connectez-vous à l'administration
2. Menu **Véhicules** → **Ajouter un véhicule**
3. Remplissez tous les champs (marque, modèle, prix, etc.)
4. Importez les photos (glisser-déposer ou sélection)
5. Cochez **Mettre en vedette** pour qu'il apparaisse sur l'accueil
6. Sélectionnez le statut **Disponible** pour le publier
7. Cliquez **Publier le véhicule**

### Gérer les demandes

- Menu **Demandes** → voir toutes les demandes de contact et de dépôt-vente
- Cliquez sur **Voir** pour accéder au détail et changer le statut
- Les nouvelles demandes sont signalées par un badge rouge dans le menu

### Modifier les paramètres du site

- Menu **Paramètres** → modifier coordonnées, textes, réseaux sociaux
- Enregistrer en bas de page

---

## Structure des fichiers

```
bon-occaz/
├── config/           → Configuration (DB, app)
├── controllers/      → Contrôleurs MVC
│   └── admin/        → Contrôleurs administration
├── models/           → Modèles (accès BDD)
├── views/            → Templates PHP
│   ├── layouts/      → Header / Footer public
│   ├── home/         → Page d'accueil
│   ├── vehicles/     → Catalogue + fiches
│   ├── contact/      → Page contact
│   ├── selling/      → Formulaire vente
│   ├── favorites/    → Page favoris
│   ├── errors/       → Pages d'erreur
│   └── admin/        → Interface administration
├── public/
│   ├── css/          → Feuilles de style
│   ├── js/           → Scripts JavaScript
│   ├── images/       → Images statiques du site
│   └── uploads/      → Fichiers uploadés (véhicules, etc.)
├── database/
│   ├── schema.sql    → Structure de la base
│   └── seeds.sql     → Données initiales
├── index.php         → Routeur principal
└── .htaccess         → Réécriture d'URL
```

---

## Sécurité

- Protection CSRF sur tous les formulaires
- Mots de passe hashés en bcrypt (coût 12)
- Requêtes préparées PDO (protection injection SQL)
- Validation serveur + client sur tous les formulaires
- Protection brute-force sur la page de connexion admin
- Routes admin protégées par vérification de session
- `noindex, nofollow` sur toutes les pages d'administration

---

## Personnalisation

### Changer les couleurs

Éditez les variables CSS dans `public/css/style.css` :
```css
:root {
  --primary:      #e63946;   /* Rouge principal */
  --primary-dark: #c1121f;   /* Rouge foncé (hover) */
  --dark:         #1d1d1b;   /* Fond sombre */
}
```

### Ajouter une image hero

Placez une image nommée `hero-bg.jpg` dans `public/images/`.  
Format recommandé : JPEG 1920×1080px, optimisé (< 500 Ko).

### Image placeholder véhicule

Placez une image nommée `no-vehicle.jpg` dans `public/images/`  
(affiché quand aucune photo n'est disponible).

---

## Support & maintenance

- Sauvegardez régulièrement la base de données
- Videz le cache navigateur après les mises à jour CSS/JS
- Vérifiez les logs Apache (`C:\xampp\apache\logs\error.log`) en cas d'erreur
- En mode production : passez `DEBUG_MODE` à `false` dans `index.php`
