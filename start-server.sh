#!/bin/bash

# Hub Server Startup Script
# This script ensures you're in the correct directory and starts the server

cd "/Users/macbookpro/Downloads/Gardner backup/projects/hub"

# Check if artisan exists
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found!"
    echo "Current directory: $(pwd)"
    echo "Please make sure you're in the hub directory."
    exit 1
fi

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "❌ Error: vendor directory not found!"
    echo "Please run: composer install"
    exit 1
fi

echo "✅ Starting Hub server..."
echo "📍 Directory: $(pwd)"
echo "🌐 Server will be available at: http://127.0.0.1:8000"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

php artisan serve --host=127.0.0.1 --port=8000

