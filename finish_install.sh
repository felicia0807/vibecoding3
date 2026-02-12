#!/bin/bash

echo "----------------------------------------------------------------"
echo "  Clicker War - Manual Installation Helper"
echo "----------------------------------------------------------------"
echo "This script will:"
echo "1. Back up your custom game code."
echo "2. Attempt to install the Laravel Framework (Requires Internet)."
echo "3. Restore your game code."
echo "----------------------------------------------------------------"

# 1. Back up custom code
echo "[1/3] Backing up game code..."
mkdir -p temp_backup
cp -r app database resources routes temp_backup/
echo "Backup created in 'temp_backup/'."

# 2. Install Laravel
echo "[2/3] Installing Laravel Framework..."
# We use --force to allow installing into a non-empty directory
composer create-project laravel/laravel . --force

if [ $? -ne 0 ]; then
    echo "❌ Laravel installation FAILED."
    echo "Possible reasons:"
    echo " - No internet connection."
    echo " - Permissions issues."
    echo "Restoring your code now..."
    cp -r temp_backup/* .
    rm -rf temp_backup
    exit 1
fi

# 3. Restore custom code
echo "[3/3] Restoring game code..."
cp -r temp_backup/app/* app/
cp -r temp_backup/database/* database/
cp -r temp_backup/resources/* resources/
cp -r temp_backup/routes/* routes/

# Clean up
rm -rf temp_backup

# 4. Install other dependencies
echo "[4/4] Installing additional dependencies..."
composer require laravel/breeze --dev
npm install

echo "----------------------------------------------------------------"
echo "✅ Setup Complete!"
echo "Run 'php artisan migrate' to set up the database."
echo "Run 'npm run dev' to start the frontend."
echo "----------------------------------------------------------------"
