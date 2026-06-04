# FNV Heerenveen Website

Production-ready PHP/MySQL website with Docker, GitHub Actions CI/CD, and optional automated server deployment.

## What is included

- Dockerized app runtime (`Dockerfile` + `docker-compose.yml`)
- MySQL service with automatic schema/data bootstrap from `database.sql`
- Health endpoint at `/health.php`
- Environment-based config in `includes/config.php`
- GitHub Actions CI workflow (PHP lint + Docker build)
- GitHub Actions CD workflow (publish image to GHCR + optional SSH deploy)
- Dependabot for Docker and GitHub Actions updates
- GitHub issue templates and pull request template

## Requirements

- Docker Desktop (or Docker Engine + Compose v2)
- GitHub repository with Actions enabled

## Local development (Docker)

1. Copy environment file:

```bash
cp .env.example .env
```

2. Start services:

```bash
docker compose up -d --build
```

3. Open the app:

- Website: `http://localhost:8080`
- Admin: `http://localhost:8080/admin/login.php`
- PhpMyAdmin (optional):

```bash
docker compose --profile tools up -d
```

Then open `http://localhost:8081`.

## Default admin login

- Email: `admin@fnvheerenveen.nl`
- Password: `Admin@FNV2024!`

## Environment variables

The app now reads config from environment variables first.

Core app variables:

- `APP_ENV` (`development` or `production`)
- `SITE_URL`
- `SITE_NAME`
- `ADMIN_EMAIL`
- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

Database container variables (local/prod compose):

- `MYSQL_ROOT_PASSWORD`
- `MYSQL_DATABASE`
- `MYSQL_USER`
- `MYSQL_PASSWORD`

Use `.env.example` as baseline.

## CI pipeline

Workflow file: `.github/workflows/ci.yml`

Runs on push and PR:

1. Checkout source
2. Setup PHP 8.3
3. Lint all PHP files (`php -l`)
4. Build Docker image (no push)

## CD pipeline

Workflow file: `.github/workflows/cd.yml`

On push to `main`:

1. Build Docker image
2. Push image to GitHub Container Registry (`ghcr.io/<owner>/<repo>:latest`)
3. Optionally deploy to a server over SSH if secrets are configured

### Required GitHub secrets for deployment

If any of these are missing, deploy is skipped automatically and only image publish runs.

- `DEPLOY_HOST`
- `DEPLOY_USER`
- `DEPLOY_SSH_KEY`
- `DEPLOY_PORT` (optional, defaults to `22`)
- `DEPLOY_PATH` (absolute path on server, for example `/opt/fnv-php`)
- `DEPLOY_GHCR_USER`
- `DEPLOY_GHCR_TOKEN` (token with read access to GHCR package)

## Production server setup

1. Install Docker + Docker Compose on the server.
2. Create deployment directory:

```bash
mkdir -p /opt/fnv-php
```

3. Create `/opt/fnv-php/.env` with production values. Minimal example:

```env
APP_PORT=8080
SITE_URL=https://your-domain.example
SITE_NAME=FNV Heerenveen
ADMIN_EMAIL=info@fnvheerenveen.nl

DB_NAME=fnv_heerenveen
DB_USER=fnv
DB_PASS=change_me

MYSQL_ROOT_PASSWORD=change_me_root
MYSQL_DATABASE=fnv_heerenveen
MYSQL_USER=fnv
MYSQL_PASSWORD=change_me
```

4. Push to `main` (or trigger `CD` manually with workflow dispatch).

The workflow copies `deploy/docker-compose.prod.yml` to server and runs:

- `docker compose pull`
- `docker compose up -d`

## GitHub repository hardening checklist

Recommended repo settings:

- Protect `main` branch (require PR + CI pass)
- Require review approvals
- Enable secret scanning and Dependabot alerts
- Restrict direct pushes to `main`

## Useful commands

```bash
# Rebuild local stack
docker compose up -d --build

# View logs
docker compose logs -f app

# Stop stack
docker compose down

# Stop and remove DB volume (fresh reset)
docker compose down -v
```

## File overview (DevOps)

- `Dockerfile`
- `docker-compose.yml`
- `deploy/docker-compose.prod.yml`
- `.env.example`
- `.github/workflows/ci.yml`
- `.github/workflows/cd.yml`
- `.github/dependabot.yml`
- `.github/ISSUE_TEMPLATE/*`
- `.github/pull_request_template.md`

## Notes

- `database.sql` is auto-imported only when MySQL volume is initialized for the first time.
- The production compose file currently includes both app and database in one stack. For managed DB services, update `DB_HOST` and remove the `db` service.
