# Deploying to OVH Web Hosting

This guide adapts the Docker-based project for OVH shared Web Hosting when the
hosting account is deployed with FTP or FTPS and does not provide SSH access.
It does not change local development, which still runs through `docker compose`
as described in [AGENTS.md](../AGENTS.md).

## Prerequisites on your OVH Web Hosting plan

- PHP 8.2+ selectable in the OVH control panel
- FTP or FTPS access enabled (FTPS is preferred)
- A MySQL database created from the OVH control panel
- Two hostnames pointed at the hosting: `http://glorious-magra.yhensel.com`
   for the frontend and
   `https://api.glorious-magra.yhensel.com.yhensel.com` for the backend
- Docker running locally for the backend production preparation

Configure the main domain's document root to contain the contents of
`frontend/dist/`. Configure the API subdomain's document root as
`backend/public/`. The rest of the backend must remain alongside `public/` so
Symfony can load `vendor/`, `config/`, and `src/`.

## 1. Prepare the backend locally

FTP only transfers files, so production dependencies, the JWT keypair, and the
Symfony production cache must be prepared locally before uploading.

1. Copy [backend/.env.prod.dist](../backend/.env.prod.dist) to
   `backend/.env.prod.local` and replace every placeholder with production
   values. Set the real frontend origin in `CORS_ALLOW_ORIGIN`. Never commit
   this file.
2. Start the local Docker services if they are not already running:
   ```bash
   make up
   ```
3. Install production PHP dependencies without running Composer's cache script
   before the production settings are ready:
   ```bash
   docker compose exec backend composer install --no-dev --optimize-autoloader --no-scripts
   ```
4. Generate a new JWT keypair. Do not reuse development keys:
   ```bash
   make console COMMAND="lexik:jwt:generate-keypair --env=prod --overwrite"
   ```
5. Build the production cache:
   ```bash
   make console COMMAND="cache:clear --env=prod"
   ```

Upload the complete `backend/` directory, including `vendor/`,
`config/jwt/private.pem`, `config/jwt/public.pem`, and `var/cache/prod/`.
Upload `backend/.env.prod.local` only through a secure FTP connection and keep
it outside the public document root when the OVH configuration allows that.
Confirm that `backend/public/.htaccess` is present; Apache needs it to route
requests through Symfony's front controller.

Do not upload local `backend/.env`, `backend/.env.dev`, test files, or any
development-only environment file. Keep `config/jwt/private.pem` private and
make sure it cannot be downloaded through the API hostname.

Messenger runs synchronously by default (see
[messenger.yaml](../backend/config/packages/messenger.yaml)), so no background
worker process is required on shared hosting.

## 2. Apply database migrations

FTP cannot execute Doctrine migrations. Apply the initial schema and every
future schema change separately using one of these methods:

- Run the migration from the local Docker backend against the OVH database if
  the database accepts connections from your local IP. Use the production
  `DATABASE_URL` only temporarily and never commit it.
- Generate and review migration SQL locally, then import it through OVH
  phpMyAdmin. Ensure the Doctrine migration table is updated consistently.
- Use an OVH plan or external deployment service that provides a command runner
  if migrations need to be automatic.

Do not upload new PHP files and assume the database schema was updated. The
application and database schema must be deployed together.

## 3. Build and upload the frontend

1. Copy [frontend/.env.production.dist](../frontend/.env.production.dist) to
   `frontend/.env.production.local` and set `VITE_API_BASE_URL` to the API
   hostname: `https://api.glorious-magra.yhensel.com.yhensel.com`.
2. Build the static site locally:
   ```bash
   cd frontend
   npm install
   npm run build
   ```
3. Upload the **contents** of `frontend/dist/` to the main domain's document
   root. Do not upload the `dist` directory as an extra path segment.

## 4. Upload checklist

- [ ] Frontend document root contains the contents of `frontend/dist/`
- [ ] API hostname document root points to `backend/public`
- [ ] Backend `vendor/` contains production dependencies
- [ ] Backend `var/cache/prod/` was built with production settings
- [ ] `backend/public/.htaccess` was uploaded
- [ ] `backend/.env.prod.local` contains real production values and is not in git
- [ ] Both JWT key files are present and the private key is not public
- [ ] Database migrations were applied separately

## 5. Security checklist before going live

- [ ] `APP_ENV=prod` and `APP_DEBUG=0`
- [ ] New `APP_SECRET`, `JWT_PASSPHRASE`, and JWT keypair (not the dev ones)
- [ ] `CORS_ALLOW_ORIGIN` restricted to the real frontend domain only
- [ ] HTTPS enabled on both hostnames (OVH provides free SSL certificates)
- [ ] Database credentials are the OVH-managed ones, not local Docker values
- [ ] Environment files containing real values are not committed
- [ ] `config/jwt/private.pem` is not publicly downloadable
