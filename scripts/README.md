# Scripts Directory

## Database Seeding

### seed-database.php

Populates the database with test/demo data for development and testing purposes.

**Usage:**

```bash
# Using Docker
docker-compose exec app php scripts/seed-database.php

# Or locally (if PHP installed)
php scripts/seed-database.php
```

**What it seeds:**
- 5 sample news articles with different categories
- 6 sample agenda items (spreekuur, vergaderingen, trainingen)
- 2 sample contact form submissions

**Important Notes:**
- Only works in **development environment** (APP_ENV != production)
- Does NOT clear existing data - it only adds new records
- To clear and reseed, uncomment the truncate section in the script
- Default admin account is: `admin@fnvheerenveen.nl` / `Admin@FNV2024!`

**Using with Docker:**

```bash
# Start services
docker-compose up -d

# Wait for database to be ready
sleep 10

# Seed the database
docker-compose exec app php scripts/seed-database.php

# Access the site at http://localhost:8080
```

## Other Scripts

More scripts may be added here as needed (backups, exports, etc.)
