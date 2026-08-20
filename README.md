# Gestion de Stock — Backend

API REST développée avec Laravel pour l'application de gestion de stock (catégories, articles, mouvements d'entrée/sortie, authentification, génération de rapports).

## Stack technique

- **Framework** : Laravel 12
- **Langage** : PHP 8.2+
- **Base de données** : MySQL
- **Authentification** : Laravel Sanctum (tokens Bearer)
- **Génération PDF** : barryvdh/laravel-dompdf

## Prérequis

Avant de commencer, assure-toi d'avoir installé :

- PHP >= 8.2
- Composer
- MySQL (ou MariaDB)

Vérifie tes versions :
```bash
php -v
composer -v
mysql --version
```

## Installation — étapes dans l'ordre

### 1. Cloner le repo

```bash
git clone https://github.com/Tossougbo-mariel/gestion-de-stock-Back.git
cd gestion-de-stock-Back
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

Copie le fichier d'exemple et adapte-le à ta configuration locale :

```bash
cp .env.example .env
```

Ouvre `.env` et renseigne au minimum :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestions_stock
DB_USERNAME=root
DB_PASSWORD=

FRONTEND_URL=http://localhost:5175
```

### 4. Générer la clé d'application

```bash
php artisan key:generate
```

### 5. Créer la base de données

Connecte-toi à MySQL et crée la base (le nom doit correspondre à `DB_DATABASE` dans ton `.env`) :

```sql
CREATE DATABASE gestions_stock;
```

### 6. Lancer les migrations

```bash
php artisan migrate
```

Ceci crée les tables dans l'ordre suivant (gestion des dépendances entre elles) :
- `users` (authentification, fournie par Laravel)
- `categories`
- `articles` (dépend de `categories`)
- `type_mouvements`
- `mouvement_stocks` (dépend de `articles`, `users`, `type_mouvements`)
- `personal_access_tokens` (Sanctum)

### 7. Lancer les seeders

```bash
php artisan db:seed
```

Ceci remplit automatiquement :
- Un utilisateur de test (`test@example.com`)
- La table `type_mouvements` avec les 2 lignes de référence :
  - `id: 1` — libellé `ENTREE`, code `IN`
  - `id: 2` — libellé `SORTIE`, code `OUT`

### 8. Installer la génération PDF (rapports)

```bash
composer require barryvdh/laravel-dompdf
```

### 9. Lancer le serveur

```bash
php artisan serve
```

L'API est maintenant accessible sur `http://127.0.0.1:8000`.

## Authentification

L'API utilise des tokens Bearer via Laravel Sanctum.

1. **Connexion** : `POST /api/login` avec `{ "email": "...", "password": "..." }` → renvoie un `token`
2. **Requêtes protégées** : ajouter le header `Authorization: Bearer <token>` à chaque appel
3. **Déconnexion** : `POST /api/logout` (invalide le token courant)

## Endpoints principaux

| Méthode | URL | Description | Protégée |
|---|---|---|---|
| POST | `/api/login` | Connexion | Non |
| POST | `/api/logout` | Déconnexion | Oui |
| GET | `/api/me` | Utilisateur connecté | Oui |
| GET | `/api/categories` | Liste des catégories (`?search=`) | Oui |
| POST | `/api/categories` | Créer une catégorie | Oui |
| PUT | `/api/categories/{id}` | Modifier une catégorie | Oui |
| DELETE | `/api/categories/{id}` | Supprimer (bloqué si articles liés) | Oui |
| GET | `/api/articles` | Liste des articles (`?search=`, `?categorie_id=`, `?rupture=1`) | Oui |
| POST | `/api/articles` | Créer un article (référence auto-générée) | Oui |
| PUT | `/api/articles/{id}` | Modifier un article (référence non modifiable) | Oui |
| DELETE | `/api/articles/{id}` | Supprimer un article | Oui |
| GET | `/api/articles/next-reference` | Prochaine référence disponible (ex: ART-004) | Oui |
| GET | `/api/type-mouvements` | Liste des types (ENTREE/SORTIE) | Oui |
| GET | `/api/mouvements` | Liste des mouvements (`?type=entree\|sortie`, `?article_id=`) | Oui |
| POST | `/api/mouvements` | Enregistrer un mouvement de stock | Oui |
| GET | `/api/mouvements/{id}` | Détail d'un mouvement | Oui |

## Règles métier importantes

- **Référence article** : générée automatiquement au format `ART-XXX`, jamais modifiable après création.
- **Catégorie obligatoire** : un article doit toujours avoir une catégorie (`categorie_id` non nullable).
- **Suppression de catégorie** : bloquée si des articles y sont encore rattachés (message d'erreur clair + contrainte SQL `onDelete('restrict')`).
- **Mouvement de sortie** : refusé si la quantité demandée dépasse le stock actuel disponible (`422` avec message).
- **Mise à jour du stock** : automatique et transactionnelle à chaque mouvement (`stock_actuel` incrémenté pour une entrée, décrémenté pour une sortie).
- **Traçabilité** : chaque mouvement de stock est historisé avec l'utilisateur connecté, jamais modifié ni supprimé après création.

## Configuration CORS

Le frontend (React) doit être autorisé à appeler l'API. Vérifie que `config/cors.php` contient bien :

```php
'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5175')],
```

Adapte `FRONTEND_URL` dans ton `.env` selon le port réel utilisé par le frontend (Vite = 5173 par défaut).

## Commandes utiles

```bash
# Voir toutes les routes de l'API
php artisan route:list

# Réinitialiser la base et rejouer migrations + seeders
php artisan migrate:fresh --seed

# Vider le cache de configuration (après un changement dans .env ou config/)
php artisan config:clear

# Ouvrir une console interactive pour tester des requêtes Eloquent
php artisan tinker
```

## Dépendances principales (composer.json)

| Package | Rôle |
|---|---|
| `laravel/framework` | Framework principal |
| `laravel/sanctum` | Authentification par token |
| `barryvdh/laravel-dompdf` | Génération de rapports PDF |

## Dépôt frontend associé

Le frontend React de ce projet se trouve dans un dépôt séparé, avec ses propres dépendances (dont `xlsx` pour l'export Excel des rapports).