#!/bin/bash

# Clear application cache
php artisan cache:clear
echo "Application cache cleared"

# Clear configuration cache
php artisan config:clear
echo "Configuration cache cleared"

# Clear route cache
php artisan route:clear
echo "Route cache cleared"

# Clear view cache
php artisan view:clear
echo "View cache cleared"

# Clear event cache
php artisan event:clear
echo "Event cache cleared"

rm -rf node_modules/.vite
echo "Vite cache geleert!"

# Optional: Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "Caches rebuilt (config, route, view)"

npm run dev
