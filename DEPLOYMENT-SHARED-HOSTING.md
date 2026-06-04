# Deploy to shared hosting (cPanel / Namecheap-style)

Server example: `ssh -p 21098 famlynva@198.54.116.91`

**Never** run `scp D:\cms\* ~/public_html/` — that breaks the site. Use the layout below.

## Folder layout on the server

```
/home/famlynva/
  cms/              ← Laravel (full project)
  cms/public/       ← document root for cms.famledger.org
  web/              ← Next.js project
  web/public/       ← uploads (PORTFOLIO_DISK_ROOT)
  public_html/      ← main domain (parking page today — reconfigure in cPanel)
```

## 1. Prepare on Windows

Edit env files first:

**`D:\web\.env.local`** (production values):

```env
NEXT_PUBLIC_SITE_URL=https://famledger.org
CMS_API_URL=https://cms.famledger.org
NEXT_REVALIDATE_SECRET=your-long-secret
CMS_REVALIDATE_SECONDS=60
```

**`D:\cms\.env`** (on server after upload, or before packaging):

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cms.famledger.org
DB_HOST=localhost
DB_DATABASE=your_cpanel_mysql_db
DB_USERNAME=your_cpanel_mysql_user
DB_PASSWORD=your_mysql_password
PORTFOLIO_DISK_ROOT=/home/famlynva/web/public
NEXT_PUBLIC_SITE_URL=https://famledger.org
NEXT_REVALIDATE_SECRET=your-long-secret
PORTFOLIO_ASSET_BASE_URL=https://famledger.org
```

Build and zip:

```powershell
cd D:\cms
powershell -File scripts/deploy-shared-hosting.ps1
```

Creates `D:\deploy-packages\cms-*.zip` and `web-*.zip`.

## 2. Upload (choose one)

### A — Folders over SSH (no zip)

```powershell
cd D:\cms
powershell -File scripts/deploy-scp-folders.ps1
```

Uses **rsync** if installed (recommended). Otherwise **scp** uploads each top-level folder (skips `vendor`, `node_modules`).

### B — Zip packages

```powershell
powershell -File scripts/deploy-shared-hosting.ps1
scp -P 21098 D:\deploy-packages\cms-*.zip famlynva@198.54.116.91:~/cms-deploy.zip
scp -P 21098 D:\deploy-packages\web-*.zip famlynva@198.54.116.91:~/web-deploy.zip
```

Then on the server: `unzip -qo ~/cms-deploy.zip -d ~/cms` and `unzip -qo ~/web-deploy.zip -d ~/web`.

## 3. Install on the server

```bash
ssh -p 21098 famlynva@198.54.116.91
bash ~/cms/scripts/server-setup-shared-hosting.sh
```

If `cms/scripts/` is not on the server yet, upload `scripts/` first or run composer/artisan steps manually (see script output).

First-time database:

```bash
cd ~/cms
php artisan db:seed --force
php scripts/reset-admin.php   # only if needed; change password after login
```

## 4. cPanel DNS and document roots

| Host | Type | Points to |
|------|------|-----------|
| `@` / `www` | A | `198.54.116.91` |
| `cms` | A | `198.54.116.91` |

In **Domains → Subdomains** (or Addon Domain):

- **cms.famledger.org** → document root: `/home/famlynva/cms/public`
- **famledger.org** → see Next.js section below

SSL: Enable AutoSSL for both hostnames.

## 5. Next.js on shared hosting

This app uses **`output: "standalone"`** and needs **Node 20+** for `npm run start`.

### If cPanel has “Setup Node.js App”

1. Application root: `/home/famlynva/web`
2. Node version: 20+
3. Application mode: Production
4. Startup: `npm run start` or `.next/standalone/server.js`
5. Set env vars from `web/.env.local` in the panel
6. Map **famledger.org** to this app (or reverse-proxy from `public_html`)

### If Node is not available

Use **split hosting** (recommended on basic plans):

- **Next.js** → [Vercel](https://vercel.com) (import `D:\web` repo)
- **CMS** → stays on `198.54.116.91` at `cms.famledger.org`
- Vercel env: `CMS_API_URL=https://cms.famledger.org`

Uploads still use `PORTFOLIO_DISK_ROOT=/home/famlynva/web/public` on the CMS server; sync or use the same server for `web/public` only.

## 6. Verify

| URL | Expected |
|-----|----------|
| `https://cms.famledger.org/up` | 200 |
| `https://cms.famledger.org/admin` | Login page |
| `https://cms.famledger.org/api/v1/site` | JSON |
| `https://famledger.org/` | Next.js homepage |

## 7. Updates later

```powershell
# Local
powershell -File D:\cms\scripts\deploy-shared-hosting.ps1
scp -P 21098 D:\deploy-packages\cms-*.zip famlynva@198.54.116.91:~/cms-deploy.zip
# repeat for web if changed
```

```bash
# Server
cd ~/cms && unzip -qo ~/cms-deploy.zip && composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
# restart Node app in cPanel if frontend changed
```
