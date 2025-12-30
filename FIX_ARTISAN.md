# Fix: "Could not open input file: artisan"

## Quick Fix

If you're getting this error even though you're in the `hub` directory, try these solutions:

### Solution 1: Use the startup script (Easiest)

```bash
cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub"
./start-server.sh
```

### Solution 2: Use absolute path

```bash
php "/Users/macbookpro/Downloads/Gardner backup/projects/hub/artisan" serve
```

### Solution 3: Verify and run

```bash
# Step 1: Go to hub directory
cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub"

# Step 2: Verify you're in the right place
pwd
# Should show: /Users/macbookpro/Downloads/Gardner backup/projects/hub

# Step 3: List files to confirm artisan exists
ls -la artisan

# Step 4: Run with explicit path
php ./artisan serve
```

### Solution 4: Check for hidden characters or issues

```bash
cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub"
file artisan
head -1 artisan
php artisan --version
```

### Solution 5: Use PHP built-in server (Alternative)

If `php artisan serve` still doesn't work:

```bash
cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub"
php -S 127.0.0.1:8000 -t public
```

## Common Causes

1. **Wrong directory**: Make sure you're in `/Users/macbookpro/Downloads/Gardner backup/projects/hub`
2. **Terminal session issue**: Try opening a new terminal window
3. **Path with spaces**: The path has spaces, make sure you're using quotes or escaping
4. **Symlink issue**: If hub is a symlink, try the actual path

## Verify Setup

Run this to check everything:

```bash
cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub" && \
echo "Directory: $(pwd)" && \
echo "Artisan exists: $([ -f artisan ] && echo 'YES' || echo 'NO')" && \
echo "Vendor exists: $([ -d vendor ] && echo 'YES' || echo 'NO')" && \
php artisan --version
```

