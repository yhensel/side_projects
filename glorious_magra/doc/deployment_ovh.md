# Deploying to OVH Web Hosting

This guide adapts the existing Docker-based project for OVH shared Web Hosting.
It does **not** change local development, which still runs entirely through
`docker compose` as described in [AGENTS.md](../AGENTS.md).

## Prerequisites on your OVH Web Hosting plan

- PHP 8.2+ selectable in the OVH control panel
- SSH access enabled
- Composer available (or installed manually over SSH)
- A MySQL database created from the OVH control panel
- Two hostnames pointed at the hosting: your main domain (frontend) and a
  subdomain such as `api.your-domain.example` (backend), each with its own
  document root

## 1. Backend (Symfony)

1. Upload the contents of `backend/` to the hosting account (e.g. via `git
   clone` over SSH, or SFTP), keeping the same folder structure.
2. Set the **subdomain's document root** to `backend/public`.
3. Copy [backend/.env.prod.dist](../backend/.env.prod.dist) to
   `backend/.env.local` on the server and fill in real values (database
   credentials, `APP_SECRET`, `JWT_PASSPHRASE`, `CORS_ALLOW_ORIGIN`). Never
   commit this file.
4. Install dependencies for production:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
5. Generate a fresh JWT keypair on the server (do not reuse the local dev
   keys):
   ```bash
   php bin/console lexik:jwt:generate-keypair --overwrite
   ```
6. Run database migrations:
   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```
7. Warm the production cache:
   ```bash
   php bin/console cache:clear --env=prod
   ```
8. Confirm `backend/public/.htaccess` was uploaded — it is required for
   Apache (OVH's web server) to route requests through Symfony's front
   controller.

Messenger already runs synchronously by default (see
[messenger.yaml](../backend/config/packages/messenger.yaml)), so no
background worker process is required on shared hosting.

## 2. Frontend (React PWA)

1. On your local machine (or any Docker-free build environment), copy
   [frontend/.env.production.dist](../frontend/.env.production.dist) to
   `frontend/.env.production.local` and set `VITE_API_BASE_URL` to the
   backend subdomain from step 1.
2. Build the static site:
   ```bash
   cd frontend
   npm install
   npm run build
   ```
3. Upload the contents of `frontend/dist/` to the main domain's document
   root on OVH.

## 3. Security checklist before going live

- [ ] `APP_ENV=prod` and `APP_DEBUG=0`
- [ ] New `APP_SECRET`, `JWT_PASSPHRASE`, and JWT keypair (not the dev ones)
- [ ] `CORS_ALLOW_ORIGIN` restricted to the real frontend domain only
- [ ] HTTPS enabled on both hostnames (OVH provides free SSL certificates)
- [ ] Database credentials are the OVH-managed ones, not `gloriosus_magra`/`gloriosus_magra`
- [ ] `.env.local` and `config/jwt/*.pem` are not committed to git
