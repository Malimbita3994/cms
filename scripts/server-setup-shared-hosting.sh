#!/usr/bin/env bash
# Run on the server (198.54.116.91) AFTER uploading cms-deploy.zip and web-deploy.zip to ~/
set -euo pipefail

HOME_DIR="${HOME:?}"
CMS_DIR="${CMS_DIR:-$HOME_DIR/cms}"
WEB_DIR="${WEB_DIR:-$HOME_DIR/web}"
CMS_ZIP="${CMS_ZIP:-$HOME_DIR/cms-deploy.zip}"
WEB_ZIP="${WEB_ZIP:-$HOME_DIR/web-deploy.zip}"

echo "==> CMS target: $CMS_DIR"
echo "==> Web target: $WEB_DIR"

command -v unzip >/dev/null || { echo "unzip required"; exit 1; }
command -v php >/dev/null || { echo "php CLI required"; exit 1; }
php -r 'exit(version_compare(PHP_VERSION,"8.3.0",">=")?0:1);' || {
    echo "PHP 8.3+ required (php -v)"
    exit 1
}

if [[ ! -f "$CMS_ZIP" || ! -f "$WEB_ZIP" ]]; then
    echo "Missing zips. Upload to:"
    echo "  $CMS_ZIP"
    echo "  $WEB_ZIP"
    exit 1
fi

echo "==> Extract CMS..."
mkdir -p "$CMS_DIR"
rm -rf "${CMS_DIR:?}/"*
unzip -qo "$CMS_ZIP" -d "$CMS_DIR"

echo "==> Extract Web..."
mkdir -p "$WEB_DIR"
rm -rf "${WEB_DIR:?}/"*
unzip -qo "$WEB_ZIP" -d "$WEB_DIR"

echo "==> Composer (CMS)..."
cd "$CMS_DIR"
if command -v composer >/dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo "WARN: composer not in PATH — install dependencies manually in $CMS_DIR"
fi

if [[ ! -f .env ]]; then
    cp .env.production.example .env
    php artisan key:generate --force
    echo "EDIT $CMS_DIR/.env (DB_*, APP_URL, NEXT_PUBLIC_SITE_URL, PORTFOLIO_DISK_ROOT)"
fi

# Uploads must live in the Next.js public folder
UPLOADS="$WEB_DIR/public/uploads"
mkdir -p "$UPLOADS/projects" "$UPLOADS/services" "$UPLOADS/insights" "$UPLOADS/uploads"
chmod -R ug+rwX "$WEB_DIR/public" "$CMS_DIR/storage" "$CMS_DIR/bootstrap/cache" 2>/dev/null || true

grep -q '^PORTFOLIO_DISK_ROOT=' .env && sed -i "s|^PORTFOLIO_DISK_ROOT=.*|PORTFOLIO_DISK_ROOT=$WEB_DIR/public|" .env \
    || echo "PORTFOLIO_DISK_ROOT=$WEB_DIR/public" >> .env

php artisan migrate --force
php artisan storage:link 2>/dev/null || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components

echo ""
echo "==> Next.js (if Node is available on this host)..."
cd "$WEB_DIR"
if command -v node >/dev/null && command -v npm >/dev/null; then
    npm ci --omit=dev
    npm run build
    echo "    Configure cPanel 'Setup Node.js App':"
    echo "      Application root: $WEB_DIR"
    echo "      Startup file: .next/standalone/server.js (or npm run start)"
else
    echo "    Node/npm not found — use cPanel Node.js selector or host Next on Vercel."
    echo "    Build locally and upload .next/standalone + static (see DEPLOYMENT-SHARED-HOSTING.md)."
fi

echo ""
echo "==> Document roots (set in cPanel Domains / Subdomains):"
echo "  Main site (famledger.org):     point to Node app OR proxy to port 3000"
echo "  CMS (cms.famledger.org):       $CMS_DIR/public"
echo ""
echo "  Do NOT copy Laravel into public_html root."
echo "  public_html should stay empty or redirect — not D:\\\\cms\\\\* files."
echo ""
echo "Done. Edit .env files, seed once: cd $CMS_DIR && php artisan db:seed --force"
