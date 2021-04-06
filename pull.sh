git checkout -- public/js/* public/js/react/* public/css/* package-lock.json
git pull
yarn
composer install --optimize-autoloader
php artisan clear-compiled
php artisan migrate --force
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan acl:permissions
npm run production
