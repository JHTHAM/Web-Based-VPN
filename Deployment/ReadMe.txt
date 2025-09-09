// Deploy scripts
ssh youruser@your-server-ip
mkdir -p ~/deploy
nano ~/deploy/deploy_vpn_dashboard.sh   # paste above
chmod +x ~/deploy/deploy_vpn_dashboard.sh
sudo ~/deploy/deploy_vpn_dashboard.sh  <- deploy command

// allow permission for vendor/
sudo chown -R $USER:$USER /var/www/vpn-dashboard
sudo chmod -R 775 /var/www/vpn-dashboard

// install dependencies
cd /var/www/vpn-dashboard
composer install --no-dev --optimize-autoloader

// after install dependencies
sudo chown -R www-data:www-data /var/www/vpn-dashboard

// git safe
git config --global --add safe.directory /var/www/vpn-dashboard

// allow port
sudo ufw allow 80
sudo ufw allow 443

// check permission
ls -ld /var/www
sudo chown -R utar:utar /var/www/vpn-dashboard

// check final deployment
cd /var/www/vpn-dashboard
git status

sudo chown -R utar:www-data /var/www/vpn-dashboard
sudo chmod -R 775 /var/www/vpn-dashboard

ls -ld /var/www/vpn-dashboard

cd /var/www/vpn-dashboard
composer install --no-dev --optimize-autoloader

ls -l .env

php artisan key:generate
php artisan migrate --seed

sudo chown -R utar:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

// clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

http://your-server-ip  <- 219.92.5.164

// check web server - laravel /public
sudo nano /etc/nginx/sites-available/vpn-dashboard

// when necessary before the above check
sudo ln -s /etc/nginx/sites-available/vpn-dashboard /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

// check PHP running
sudo systemctl status php8.2-fpm


// check current user
whoami
id

// check file and permission
ls -ld /opt/shared_vpn
-- > (ex:) drwxrws--x 6 www-data www-data 4096 Jul  4 07:26 shared_vpn

// see all files
ls -lR /opt/shared_vpn

// check privilleges
sudo -l -U www-data
sudo -l -U utar

// run scripts without sudo
sudo chgrp -R www-data /opt/shared_vpn
sudo chmod -R 2775 /opt/shared_vpn
 
// give permission (run before execure generate_ovpn.sh)
sudo chown -R www-data:www-data /opt/shared_vpn /etc/openvpn/client /etc/openvpn/ccd
sudo chmod -R 775 /opt/shared_vpn /etc/openvpn/client /etc/openvpn/ccd

// include permission (sudo visudo)
www-data ALL=(utar) NOPASSWD: /opt/shared_vpn/client-configs/generate_ovpn.sh, /opt/shared_vpn/client-configs/delete_ovpn.sh

// test manually generate
sudo -u www-data /opt/shared_vpn/client-configs/generate_ovpn.sh TESTUSER

// if key problems
cd /var/www/vpn-dashboard
sudo chown -R www-data:www-data storage bootstrap/cache .env
sudo chmod -R 775 storage bootstrap/cache
sudo chown utar:www-data .env
sudo chmod 664 .env

# Clear Laravel caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Rebuild config cache cleanly
php artisan config:cache

php artisan key:generate
