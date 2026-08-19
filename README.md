# FNV Heerenveen Website

Official website for FNV Heerenveen (Vakbond voor werkenden - local union branch).

## About

This is a PHP-based content management system built for FNV Heerenveen to manage:
- **News articles** with categories and publication dates
- **Event calendar** for spreekuur (office hours), meetings, and training
- **Pages** for information about services, membership, and union info
- **Contact form** for member inquiries

Built with:
- **Backend**: PHP 8.3 + MySQL 8.4
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Deployment**: Docker & Docker Compose

## Quick Start

### Development (Local)

**Requirements:**
- Docker & Docker Compose installed
- Git

**Setup:**

```bash
# Clone the repository
git clone https://github.com/yourusername/fnv-heerenveen-rebuild.git
cd fnv-heerenveen-rebuild

# Copy environment file
cp .env.example .env

# Start containers
docker-compose up -d

# Wait for database initialization (about 10 seconds)
sleep 10

# Seed test data
docker-compose exec app php scripts/seed-database.php

# Access the site
# Website: http://localhost:8080
# Admin: http://localhost:8080/admin/login.php
# Credentials: admin@fnvheerenveen.nl / Admin@FNV2024!
# PHPMyAdmin (optional): http://localhost:8081
```

To start phpMyAdmin tools:

```bash
docker-compose --profile tools up -d phpmyadmin
```

**Stop containers:**

```bash
docker-compose down
```

### Production Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for complete production setup instructions.

**Quick overview:**
1. Prepare VPS with Docker installed
2. Clone repo and configure `.env` with production values
3. Set up Nginx reverse proxy with SSL
4. Initialize database
5. Set strong admin password
6. Configure backups
7. Monitor health

## Project Structure

```
fnv-heerenveen-rebuild/
├── .env.example              # Example environment variables
├── .env.production          # Production environment template
├── .htaccess                # Apache security & rewrite rules
├── docker-compose.yml       # Multi-container orchestration
├── Dockerfile               # PHP application image
├── database.sql             # Initial database schema + test data
├── DEPLOYMENT.md            # Production deployment guide
├── README.md                # This file
│
├── admin/                   # Administration panel
│   ├── dashboard.php        # Admin dashboard
│   ├── nieuws.php           # News management
│   ├── nieuws-form.php      # News editor
│   ├── agenda.php           # Event management
│   ├── paginas.php          # Page editor
│   ├── berichten.php        # Contact messages
│   ├── login.php            # Admin login
│   ├── uitloggen.php        # Admin logout
│   └── includes/            # Admin templates
│
├── includes/                # Shared includes
│   ├── config.php           # Configuration & database connection
│   ├── header.php           # Page header template
│   └── footer.php           # Page footer template
│
├── assets/                  # Static files
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript
│   └── img/                 # Images
│
├── scripts/                 # Utility scripts
│   ├── seed-database.php    # Database seeding for testing
│   └── README.md            # Script documentation
│
├── docker/                  # Docker configuration
│   └── apache/
│       └── 000-default.conf # Apache configuration
│
└── deploy/                  # Deployment utilities (if needed)
```

## Features

### Public Website
- 📰 **News Section** - Latest union news and updates
- 📅 **Event Calendar** - Spreekuur (office hours), meetings, training dates
- 📄 **CMS Pages** - Information about union, services, membership
- 📋 **Contact Form** - Direct messaging to union staff
- 📱 **Responsive Design** - Works on desktop, tablet, and mobile
- ♿ **Accessibility** - WCAG compliant markup

### Admin Panel
- ✏️ **Content Management** - Create, edit, delete articles and pages
- 🗓️ **Event Management** - Add and manage calendar events
- 💬 **Message Inbox** - Read and manage contact form submissions
- 📊 **Dashboard** - Overview of content and recent messages
- 🔐 **Secure Login** - Password-protected administration

## Admin Credentials

After setup, login with:
- **Email**: `admin@fnvheerenveen.nl`
- **Password**: `Admin@FNV2024!`

⚠️ **IMPORTANT**: Change this password immediately after first login in production!

## Database

### Tables

| Table | Purpose |
|-------|---------|
| `users` | Admin user accounts |
| `news` | News articles |
| `agenda` | Calendar events |
| `pages` | CMS pages (About, Services, etc.) |
| `contact_messages` | Contact form submissions |

### Backups

For production, automated daily backups are recommended. See DEPLOYMENT.md for backup setup.

To manually backup:

```bash
docker-compose exec -T db mysqldump -u root -p$MYSQL_ROOT_PASSWORD fnv_heerenveen > backup.sql
```

To restore:

```bash
docker-compose exec -T db mysql -u root -p$MYSQL_ROOT_PASSWORD fnv_heerenveen < backup.sql
```

## Security

- ✅ SQL Injection prevention (prepared statements)
- ✅ XSS protection (HTML escaping)
- ✅ CSRF protection (session-based)
- ✅ Password hashing (bcrypt)
- ✅ Security headers (.htaccess)
- ✅ SSL/TLS enforcement (production)

### Security Checklist

- [ ] Change admin password on first login
- [ ] Set strong database passwords in `.env`
- [ ] Use HTTPS in production (SSL certificate)
- [ ] Regular backups enabled
- [ ] Monitor access logs
- [ ] Keep Docker images updated
- [ ] Disable directory listing (.htaccess)
- [ ] Hide sensitive files from web

## Email Configuration

The contact form sends emails via the system's mail server. In production:

1. Ensure mail service is running on the VPS
2. Or configure postfix/sendmail
3. Or integrate with external mail service (SendGrid, etc.)

## Performance

- Static assets cached in browser
- Gzip compression enabled
- Database indexes on common queries
- Prepared statements to prevent SQL injection

## Monitoring

Check application health:

```bash
curl https://your-domain.nl/health.php
# Returns: {"status":"ok"}
```

View logs:

```bash
docker-compose logs -f app  # PHP/Apache
docker-compose logs -f db   # MySQL
```

## Development Tips

### Adding a News Article

1. Go to Admin → Nieuws
2. Click "Nieuw artikel"
3. Fill in title, excerpt, content
4. Add category and image (optional)
5. Check "Publiceren" to publish
6. Click "Opslaan"

### Managing Events

1. Go to Admin → Agenda
2. Click "Toevoegen" to create event
3. Set date, time, location, type
4. Optionally add registration URL
5. Save

### Editing Pages

1. Go to Admin → Pagina's
2. Click a page to edit
3. Update content
4. Save

### Adding a New Page

The main pages (Over ons, Diensten, Lid worden) are in the database. To add new pages:

1. Add record to `pages` table
2. Link in navigation/footer
3. Create slug-based URL like `/pagina.php?slug=new-page`

## Browser Support

- Chrome/Edge (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Mobile browsers (iOS Safari, Chrome Mobile)

## API Endpoints

This is a traditional server-rendered PHP application, not a REST API. All interactions are through HTML forms.

## Known Limitations

- Single admin user role (no multi-user permissions yet)
- No image upload validation (size/type)
- Email sending relies on system mail (not integrated SMS/notifications)
- No multilingual support (Dutch only)

## Future Enhancements

- [ ] Multiple admin roles (editor, moderator)
- [ ] Image upload with validation
- [ ] Email notification templates
- [ ] Newsletter subscription
- [ ] Search functionality
- [ ] Multi-language support
- [ ] REST API for mobile app

## Troubleshooting

### Database Connection Error

```
Error: Can't connect to MySQL server
```

**Solution:**
```bash
docker-compose ps                    # Check if db is running
docker-compose logs db               # See database logs
docker-compose restart db            # Restart database
```

### Admin Panel Not Accessible

1. Verify database is initialized
2. Check browser cookies are enabled
3. Clear browser cache
4. Check user exists: `SELECT * FROM users;`

### Forms Not Sending

For contact form emails:
```bash
docker-compose exec app php -r "mail('test@example.com', 'Test', 'Test', 'From: noreply@fnvheerenveen.nl');"
```

### High Server Load

- Check error logs: `docker-compose logs app`
- Monitor disk space: `docker-compose exec app df -h`
- Verify database has indices
- Consider caching strategy

## Support & Feedback

For issues or suggestions:
1. Check existing GitHub issues
2. Create new issue with description
3. Contact: info@fnvheerenveen.nl

## License

© 2024 FNV Heerenveen. All rights reserved.

---

**Last Updated**: August 2024
**Version**: 1.0
**Maintained by**: FNV Heerenveen
