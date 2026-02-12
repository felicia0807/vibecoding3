# Clicker War - Setup Instructions

Due to network connectivity issues, the Laravel framework and its dependencies could not be installed automatically. However, the **application code** (Models, Controllers, Views, Migrations) has been generated and is ready for use.

Once you have internet access, follow these steps to get the game running:

## 1. Install Laravel Framework
You need to install the core framework structure. Since this directory already contains the game code, you might need to move it temporarily.

```bash
# 1. Create a temporary folder for the fresh install
composer create-project laravel/laravel temp_project

# 2. Move the fresh install files into this directory (skipping your custom files)
cp -n -r temp_project/. .

# 3. Clean up
rm -rf temp_project
```

## 2. Install Dependencies
```bash
composer install
npm install
```

## 3. Install Laravel Breeze (Authentication)
The game relies on authentication.
```bash
composer require laravel/breeze --dev
php artisan breeze:install vue
# Choose 'Vue' when prompted
```

## 4. Install Reverb (Real-time)
```bash
php artisan install:broadcasting
```

## 5. Database Setup
1. Open `.env` and configure your database (MySQL):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=clicker_war
   DB_USERNAME=root
   DB_PASSWORD=
   ```
2. Run migrations:
   ```bash
   php artisan migrate
   ```

## 6. Run the Application
1. Start the backend:
   ```bash
   php artisan serve
   ```
2. Start the frontend (in a new terminal):
   ```bash
   npm run dev
   ```
3. Start the websocket server (in a new terminal):
   ```bash
   php artisan reverb:start
   ```

## 7. Restore Game Code
If the installation processes (like Breeze) overwrote any of the game files (like `routes/web.php` or `resources/js/app.js`), verify them against the code generated in `app/`, `resources/`, and `database/`.

**Key Files Generated:**
- `app/Models/User.php`
- `app/Models/UserUpgrade.php`
- `app/Models/Battle.php`
- `app/Http/Controllers/GameController.php`
- `database/migrations/` (User, Upgrades, Battles tables)
- `resources/js/Pages/Dashboard.vue`
- `resources/js/Components/` (ClickZone, Shop, etc.)
- `routes/api.php`
