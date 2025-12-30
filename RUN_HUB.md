# How to Run the Hub Project

## Quick Start

1. **Navigate to the hub directory:**
   ```bash
   cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub"
   ```

2. **Verify you're in the correct directory:**
   ```bash
   ls -la artisan
   ```
   You should see the `artisan` file listed.

3. **Run the server:**
   ```bash
   php artisan serve
   ```
   
   Or specify host and port:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```

## Troubleshooting

### If you get "Could not open input file: artisan"

**Problem:** You're not in the hub directory.

**Solution:**
```bash
# Make sure you're in the hub directory
cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub"

# Verify you're in the right place
pwd
# Should output: /Users/macbookpro/Downloads/Gardner backup/projects/hub

# Then run
php artisan serve
```

### If the server starts but shows 500 error

**Problem:** Database not configured or missing.

**Solution:**
1. Check `.env` file exists:
   ```bash
   ls -la .env
   ```

2. Configure database in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=hub_main
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

3. Create the database:
   ```bash
   mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS hub_main;"
   ```

4. Run migrations:
   ```bash
   php artisan migrate
   php artisan db:seed --class=CategorySeeder
   ```

## Alternative: Use PHP Built-in Server

If `php artisan serve` doesn't work, you can use PHP's built-in server:

```bash
cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub"
php -S localhost:8000 -t public
```

## Verify Server is Running

After starting the server, open your browser and visit:
- http://localhost:8000
- http://127.0.0.1:8000

You should see the Laravel welcome page or your application.

