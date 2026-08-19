# FNV Heerenveen Production Deployment Checklist

Complete this checklist before going live with the website.

## Pre-Deployment

### Code & Configuration
- [ ] All security fixes applied (no hardcoded passwords, SQL injection fixed)
- [ ] `.env.production` created with strong credentials
- [ ] `.env` file NOT committed to Git (check .gitignore)
- [ ] APP_ENV set to `production`
- [ ] SITE_URL set to production domain (https://)
- [ ] All debug/error pages disabled
- [ ] Test data removed from production database
- [ ] Admin password changed from default

### Infrastructure
- [ ] VPS provisioned with Docker installed
- [ ] Domain DNS records pointing to VPS IP
- [ ] SSL certificate obtained (Let's Encrypt recommended)
- [ ] Firewall configured (port 80, 443 open)
- [ ] SSH key pair set up for secure access

## Deployment

### Docker Setup
- [ ] Repository cloned to `/opt/fnv-heerenveen-rebuild`
- [ ] `.env` file created with production values
- [ ] `docker-compose build` completed without errors
- [ ] `docker-compose up -d` started all services
- [ ] `docker-compose ps` shows all containers running
- [ ] Database initialization logs checked
- [ ] Health check passes: `curl http://localhost:8080/health.php`

### Reverse Proxy (Nginx)
- [ ] Nginx installed on VPS
- [ ] Site configuration created in `/etc/nginx/sites-available/`
- [ ] SSL certificate paths configured
- [ ] Nginx config tested: `nginx -t`
- [ ] Nginx restarted: `systemctl restart nginx`
- [ ] Site accessible at `https://your-domain.nl`

### Database
- [ ] Database initialized with `database.sql`
- [ ] Users table populated with admin account
- [ ] Initial pages and news created
- [ ] Test data added for staging
- [ ] Database backups configured

## Post-Deployment Testing

### Website Functionality
- [ ] Homepage loads without errors
- [ ] Navigation works (Home, Nieuws, Agenda, etc.)
- [ ] All pages display correctly
- [ ] Images load properly
- [ ] Mobile/tablet responsive verified
- [ ] Form validation works on contact page
- [ ] Contact form submits successfully
- [ ] Email notifications received

### Admin Panel
- [ ] Admin login page accessible at `/admin/login.php`
- [ ] Can login with admin credentials
- [ ] Dashboard displays stats
- [ ] Can create new news article
- [ ] Can add agenda event
- [ ] Can edit pages
- [ ] Can view contact messages
- [ ] Can logout

### Security
- [ ] HTTPS enforced (redirect from http)
- [ ] Security headers present (X-Content-Type-Options, etc.)
- [ ] No error messages expose system info
- [ ] Directory listing disabled
- [ ] Sensitive files not accessible (database.sql, .env, etc.)
- [ ] Session handling secure
- [ ] CSRF tokens working (if applicable)

### Performance
- [ ] Homepage loads in < 2 seconds
- [ ] Database queries optimized (no N+1)
- [ ] Assets compressed (gzip enabled)
- [ ] Cache headers configured
- [ ] No memory leaks in logs

### Monitoring
- [ ] Health check endpoint working
- [ ] Application logs accessible
- [ ] Database logs accessible
- [ ] Error logging configured
- [ ] Daily backup script running
- [ ] Disk space monitoring set up

## Security Hardening

### Access Control
- [ ] SSH key authentication only (no password)
- [ ] Firewall rules restrict access
- [ ] Only necessary ports open (80, 443, 22)
- [ ] Fail2ban or similar configured for SSH
- [ ] Admin IP whitelist considered

### Data Protection
- [ ] Backups encrypted and stored off-site
- [ ] Database passwords strong (20+ chars)
- [ ] Root database password changed
- [ ] Only application user can access database
- [ ] .env file permissions restricted (600)

### Updates & Maintenance
- [ ] Docker image versions pinned (not latest)
- [ ] PHP security extensions enabled
- [ ] MySQL/MariaDB security hardening done
- [ ] SSL/TLS version enforced (1.2+)
- [ ] Update schedule planned

## Backup & Recovery

### Backup System
- [ ] Daily database backups configured
- [ ] Backups tested and recoverable
- [ ] Backup location on separate storage
- [ ] Retention policy set (30 days minimum)
- [ ] Backup script logs monitored

### Disaster Recovery
- [ ] Recovery procedure documented
- [ ] Database restore tested
- [ ] Application restore tested
- [ ] Recovery time objective (RTO) defined
- [ ] Recovery point objective (RPO) defined

## Documentation

### Team Knowledge
- [ ] Deployment procedure documented
- [ ] Admin credentials securely stored (password manager)
- [ ] Emergency contact information recorded
- [ ] Database schema documented
- [ ] Custom code documented
- [ ] API/integration documentation complete

### User Documentation
- [ ] Admin guide created for content managers
- [ ] FAQ page created for users
- [ ] Contact procedures documented
- [ ] Support email/phone updated

## Go-Live

### Pre-Launch
- [ ] Announcement prepared
- [ ] Social media updated with new URL
- [ ] Email sent to members (if applicable)
- [ ] Partners notified of launch
- [ ] Support staff trained on admin panel

### Launch Day
- [ ] Team available for monitoring
- [ ] Monitoring dashboard open
- [ ] Error logs being watched
- [ ] Support team ready for questions
- [ ] Backup taken before launch

### Post-Launch
- [ ] Monitor error logs continuously
- [ ] Check performance metrics
- [ ] Verify backups working
- [ ] Collect user feedback
- [ ] Plan post-launch improvements

## Post-Deployment Tasks (First Week)

- [ ] Monitor application stability
- [ ] Check backup success
- [ ] Review access logs for issues
- [ ] Verify email notifications working
- [ ] Optimize slow queries (if any)
- [ ] Fine-tune cache settings
- [ ] Train admin users fully
- [ ] Document any custom changes
- [ ] Plan next features/improvements

## Monthly Tasks

- [ ] Review and update security patches
- [ ] Audit database backups
- [ ] Check disk space usage
- [ ] Review error logs
- [ ] Update SSL certificate (if nearing expiry)
- [ ] Performance optimization review
- [ ] Security audit

## Emergency Contacts

| Role | Name | Email | Phone |
|------|------|-------|-------|
| Site Admin | [Name] | [Email] | [Phone] |
| Dev/Tech | [Name] | [Email] | [Phone] |
| Hosting Provider | | | |

## Sign-Off

- [ ] Project Lead: _________________ Date: _______
- [ ] Tech Lead: _________________ Date: _______
- [ ] Approver: _________________ Date: _______

---

**Notes & Issues Found:**

[Space for notes during deployment]

---

**Deployment Date**: __________
**Deployed By**: __________
**Status**: ☐ Successful ☐ With Issues ☐ Incomplete
