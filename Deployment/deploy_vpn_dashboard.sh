#!/usr/bin/env bash
set -euo pipefail

# ===== Variables =====
REPO_URL="https://github.com/JHTHAM/Web-Based-VPN.git"
APP_DIR="/var/www/vpn-dashboard"
PHP_VERSION="8.3"
SERVER_NAME="vpn.novaplus.my"
DB_CONNECTION="mysql"
DB_HOST="db.novaplus.my"
DB_PORT="3306"
DB_DATABASE="vpn_login"
DB_USERNAME="openvpn"
DB_PASSWORD="Nov@flow6889"

# OpenVPN
OVPN_STATUS_LOG="/etc/openvpn/status.log"
SCRIPTS_DIR="/opt/vpn/client-configs"
SCRIPTS=("generate_router_ovpn.sh" "generate_client_ovpn.sh" "delete_ovpn.sh")

# Colors
GREEN='\033[0;32m'; YELLOW='\033[0;33m'; RED='\033[0;31m'; NC='\033[0m'

log(){ echo -e "${GREEN}==>${NC} $*"; }
warn(){ echo -e "${YELLOW}[warn]${NC} $*"; }
err(){ echo -e "${RED}[err]${NC} $*"; }

# Ensure root
[[ $EUID -eq 0 ]] || { err "Run with sudo"; exit 1; }

log "Updating apt..."
apt-get update -y

log "Installing base tools..."
apt-get install -y curl git unzip ca-certificates lsb-release apt-transport-https

# PHP install (skip if already installed)
if ! command -v php &>/dev/null; then
  log "Installing PHP ${PHP_VERSION}..."
  if ! apt-cache policy "php${PHP_VERSION}-fpm" | grep -q "Candidate:"; then
    log "Adding PPA for PHP ${PHP_VERSION}..."
    apt-get install -y software-properties-common
    add-apt-repository -y ppa:ondrej/php
    apt-get update -y
  fi
  apt-get install -y php${PHP_VERSION} php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql \
  php${PHP_VERSION}-xml php${PHP_VERSION}-mbstring php${PHP_VERSION}-curl \
  php${PHP_VERSION}-zip php${PHP_VERSION}-bcmath
else
  log "PHP already installed: $(php -v | head -n1)"
fi

# Composer (skip if already installed)
if ! command -v composer &>/dev/null; then
  log "Installing Composer..."
  curl -sS https://getcomposer.org/installer | php
  mv composer.phar /usr/local/bin/composer
else
  log "Composer already installed: $(composer --version)"
fi

# Nginx (skip if already installed)
if ! command -v nginx &>/dev/null; then
  log "Installing Nginx..."
  apt-get install -y nginx
  systemctl enable --now nginx
fi

# Clone or update project
if [[ ! -d "$APP_DIR/.git" ]]; then
  log "Cloning project..."
  git clone "$REPO_URL" "$APP_DIR"
else
  log "Updating project..."
  cd "$APP_DIR"
  git reset --hard
  git pull
fi
chown -R www-data:www-data "$APP_DIR"

# Setup .env
cd "$APP_DIR"
cp .env.example .env 2>/dev/null || true
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=${DB_CONNECTION}/" .env
sed -i "s/^DB_HOST=.*/DB_HOST=${DB_HOST}/" .env
sed -i "s/^DB_PORT=.*/DB_PORT=${DB_PORT}/" .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/" .env
sed -i "s~^DB_PASSWORD=.*~DB_PASSWORD=${DB_PASSWORD}~" .env

# Fix APP_KEY (always ensure it exists)
if ! grep -q "APP_KEY=base64:" .env; then
  log "Generating new APP_KEY..."
  sudo -u www-data php artisan key:generate --force || true
else
  log "APP_KEY already set"
fi

# Composer deps
log "Installing Laravel dependencies..."
sudo -u www-data composer install --no-dev --optimize-autoloader

log "Running migrations..."
sudo -u www-data php artisan migrate --force || true

# Permissions
chown -R www-data:www-data "$APP_DIR"
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

# Nginx config
NGINX_CONF="/etc/nginx/sites-available/vpn-dashboard"
if [[ ! -f "$NGINX_CONF" ]]; then
  log "Configuring Nginx..."
  cat > "$NGINX_CONF" <<EOF
server {
    listen 80;
    server_name ${SERVER_NAME};
    root ${APP_DIR}/public;
    index index.php index.html;
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        fastcgi_param HTTPS on;
        fastcgi_param HTTP_X_FORWARDED_PROTO https;
        include fastcgi_params;
    }
    location ~ /\.ht { deny all; }
}
EOF
  ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/
  rm -f /etc/nginx/sites-enabled/default
  nginx -t
  systemctl restart nginx
else
  systemctl reload nginx
fi

systemctl restart "php${PHP_VERSION}-fpm"

# OpenVPN status log
if [[ -f "$OVPN_STATUS_LOG" ]]; then
  log "Fixing OpenVPN status log permissions..."
  chown www-data:www-data "$OVPN_STATUS_LOG"
  chmod 644 "$OVPN_STATUS_LOG"
else
  warn "Status log not found: $OVPN_STATUS_LOG"
fi

# Ensure scripts exist, are executable, and sudoers configured
mkdir -p "$SCRIPTS_DIR"
for s in "${SCRIPTS[@]}"; do
  SRC="${SCRIPTS_DIR}/${s}"
  if [[ -f "$SRC" ]]; then
    chmod +x "$SRC"
    LINE="www-data ALL=(ALL) NOPASSWD: ${SRC}"
    grep -Fxq "$LINE" /etc/sudoers || echo "$LINE" >> /etc/sudoers
  else
    warn "Script missing: $SRC"
  fi
done

# Laravel queue & scheduler
if [[ ! -f "/etc/systemd/system/laravel-worker.service" ]]; then
  log "Creating Laravel queue worker..."
  cat > /etc/systemd/system/laravel-worker.service <<EOF
[Unit]
Description=Laravel Queue Worker
After=network.target
[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php${PHP_VERSION} ${APP_DIR}/artisan schedule:run >> /dev/null 2>&1"
[Install]
WantedBy=multi-user.target
EOF
  systemctl daemon-reload
  systemctl enable --now laravel-worker
else
  systemctl restart laravel-worker
fi

# Scheduler cron
CRON_LINE="* * * * * php ${APP_DIR}/artisan schedule:run >> /dev/null 2>&1"
crontab -u www-data -l 2>/dev/null | grep -F "$CRON_LINE" || (crontab -u www-data -l 2>/dev/null; echo "$CRON_LINE") | crontab -u www-data -

log "Deployment complete! Access your dashboard via server IP or domain."

