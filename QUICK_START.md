# Quick Start - Hub Project

## To Run the Hub Server:

```bash
# 1. Navigate to hub directory (IMPORTANT!)
cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub"

# 2. Verify you're in the right place
pwd
# Should show: /Users/macbookpro/Downloads/Gardner backup/projects/hub

# 3. Run the server
php artisan serve
```

## If you get "Could not open input file: artisan"

**You're not in the hub directory!**

Make sure you run:
```bash
cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub"
```

Then try again:
```bash
php artisan serve
```

## Alternative: Use full path

If you're having trouble with the directory, you can use:
```bash
cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub" && php artisan serve
```

## Server will start on:
- http://localhost:8000
- http://127.0.0.1:8000

## To stop the server:
Press `Ctrl+C` in the terminal where it's running.

