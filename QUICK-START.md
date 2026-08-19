# FNV Heerenveen – Quick Start Guide

Get the website running in 5 minutes.

## Option 1: Docker (Recommended)

### Start

```bash
cd fnv-heerenveen-rebuild
docker-compose up -d
sleep 10
docker-compose exec app php scripts/seed-database.php
```

### Access

- **Website**: http://localhost:8080
- **Admin**: http://localhost:8080/admin/login.php
- **Database**: PHPMyAdmin at http://localhost:8081 (after `docker-compose --profile tools up`)

### Stop

```bash
docker-compose down
```

### View Logs

```bash
docker-compose logs -f app    # Website logs
docker-compose logs -f db     # Database logs
```

---

## Option 2: Traditional Hosting

### Requirements
- PHP 8.3+
- MySQL 8.0+
- Web server (Apache/Nginx)

### Setup
1. Extract files to web root
2. Create MySQL database: `fnv_heerenveen`
3. Import `database.sql`
4. Create `.env` file (copy `.env.example`)
5. Update `.env` with database credentials
6. Access via http://your-domain

---

## Admin Credentials

```
Email:    admin@fnvheerenveen.nl
Password: Admin@FNV2024!
```

⚠️ **Change password immediately after login!**

---

## Common Tasks

### Add News Article
1. Login → Dashboard
2. Click "Nieuw artikel" or go to Nieuws → Add
3. Fill in title, excerpt, content
4. Check "Publiceren"
5. Save

### Add Event
1. Go to Agenda
2. Click "Toevoegen"
3. Fill date, time, location, type
4. Save

### Edit Page
1. Go to Pagina's
2. Click page to edit
3. Update content
4. Save

### View Messages
1. Go to Berichten
2. Click message to read
3. Mark as read/unread

### Change Admin Password
1. Login to MySQL
2. Generate hash: `php -r "echo password_hash('newpass', PASSWORD_BCRYPT);"`
3. Update: `UPDATE users SET password='$2y$10$...' WHERE id=1;`

---

## Environment Variables

```env
APP_ENV=development                    # or 'production'
SITE_URL=http://localhost:8080         # Your domain
DB_NAME=fnv_heerenveen                 # Database name
DB_USER=fnv                            # Database user
DB_PASS=fnv_dev_password               # Database password
MYSQL_ROOT_PASSWORD=root_password      # MySQL root password
```

Copy `.env.example` to `.env` and update values.

---

## Database Info

**Tables:**
- `users` - Admin accounts
- `news` - Articles
- `agenda` - Events
- `pages` - CMS pages
- `contact_messages` - Form submissions

**Useful Queries:**

```sql
-- View all articles
SELECT * FROM news ORDER BY published_at DESC;

-- View upcoming events
SELECT * FROM agenda WHERE datum >= CURDATE() ORDER BY datum;

-- View contact messages
SELECT * FROM contact_messages ORDER BY created_at DESC;

-- View pages
SELECT * FROM pages WHERE active = 1;
```

---

## Troubleshooting

### Docker Issue: "Cannot connect to database"

```bash
docker-compose ps                  # Check status
docker-compose restart db          # Restart database
docker-compose logs db | tail -20  # See error
```

### Blank Page / 500 Error

```bash
docker-compose logs app | tail -50  # Check PHP errors
```

### Admin Login Not Working

1. Clear browser cookies
2. Check user exists: `SELECT * FROM users;`
3. Verify database password in `.env`

---

## File Permissions (Production)

```bash
chmod 644 -R .                      # Default: readable
chmod 755 -R . -type d              # Directories: executable
chmod 770 admin                     # Admin: readable by www-data
chmod 600 .env                      # .env: only readable by owner
```

---

## SSL Certificate (Production)

```bash
# Get certificate (Nginx)
certbot certonly --standalone -d your-domain.nl

# Update Nginx config with paths:
# /etc/letsencrypt/live/your-domain.nl/fullchain.pem
# /etc/letsencrypt/live/your-domain.nl/privkey.pem

# Renew automatically
certbot renew --dry-run
```

---

## Monitoring

Check if site is working:

```bash
curl http://localhost:8080/health.php
# Should return: {"status":"ok"}
```

---

## Full Documentation

- **Deployment**: See `DEPLOYMENT.md`
- **Production Checklist**: See `PRODUCTION-CHECKLIST.md`
- **Scripts**: See `scripts/README.md`
- **Full README**: See `README.md`

---

**Need Help?** Check error logs or contact FNV Heerenveen.
