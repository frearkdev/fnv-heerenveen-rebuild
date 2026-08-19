# FNV Heerenveen – Complete Deployment Guide

Complete step-by-step guide for deploying FNV Heerenveen website to production.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Local Preparation](#1-local-preparation)
3. [VPS Setup](#2-vps-setup)
4. [Docker Deployment](#3-docker-deployment)
5. [Nginx Reverse Proxy](#4-nginx-reverse-proxy)
6. [SSL/TLS Security](#5-ssltls-security)
   - [Let's Encrypt Setup](#51-lets-encrypt-setup)
   - [Cloudflare Setup](#52-cloudflare-setup)
7. [Admin & Database](#6-admin--database)
8. [Backups](#7-backup-strategy)
9. [Monitoring](#8-monitoring)
10. [Maintenance](#9-updates--maintenance)
11. [Troubleshooting](#10-troubleshooting)
12. [Emergency Recovery](#11-emergency-restore)
13. [Production Checklist](#12-production-checklist)

---

## Prerequisites

Before starting deployment, ensure you have:

- ✅ VPS with 2GB+ RAM, 20GB+ disk space
- ✅ Docker & Docker Compose installed on VPS
- ✅ Domain name registered
- ✅ SSH access to VPS
- ✅ Domain admin access (to change nameservers)
- ✅ Email for SSL certificate notifications

### VPS Requirements

**Minimum Specs:**
- 2GB RAM
- 20GB SSD storage
- Ubuntu 20.04 LTS or newer
- 1Gbps network

**Recommended:**
- 4GB+ RAM
- 50GB+ SSD
- 10+ Mbps bandwidth

### Install Docker on VPS

```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Verify installation
docker --version
docker-compose --version
```

---

## 1. Local Preparation

### Step 1.1: Prepare Environment File

```bash
cd fnv-heerenveen-rebuild

# Copy production template
cp .env.production .env

# Edit with your production values
nano .env
```

**Update these values:**

```env
APP_ENV=production                    # Important!
SITE_URL=https://your-domain.nl      # Must be HTTPS
SITE_NAME=FNV Heerenveen
ADMIN_EMAIL=your-email@your-domain.nl

# Strong passwords (use: openssl rand -base64 32)
DB_NAME=fnv_heerenveen
DB_USER=fnv_prod
DB_PASS=GENERATE_STRONG_PASSWORD_HERE

MYSQL_ROOT_PASSWORD=GENERATE_STRONG_PASSWORD_HERE
MYSQL_DATABASE=fnv_heerenveen
MYSQL_USER=fnv_prod
MYSQL_PASSWORD=GENERATE_STRONG_PASSWORD_HERE
```

**Generate strong passwords:**

```bash
openssl rand -base64 32
# Run 2 times, use for DB_PASS and MYSQL_ROOT_PASSWORD
```

### Step 1.2: Verify Configuration

```bash
# Check .env syntax
grep "^[^#]" .env

# Ensure .env is NOT committed
git status | grep ".env"
# Should NOT show .env file
```

---

## 2. VPS Setup

### Step 2.1: Connect to VPS

```bash
ssh -i your-key.pem ubuntu@your-vps-ip

# Or with password
ssh ubuntu@your-vps-ip
```

### Step 2.2: Update System

```bash
sudo apt-get update
sudo apt-get upgrade -y
sudo apt-get install -y curl wget git htop
```

### Step 2.3: Create Application Directory

```bash
sudo mkdir -p /opt/fnv-heerenveen-rebuild
sudo chown $USER:$USER /opt/fnv-heerenveen-rebuild

cd /opt/fnv-heerenveen-rebuild
```

### Step 2.4: Clone Repository

```bash
git clone https://github.com/yourusername/fnv-heerenveen-rebuild.git .

# Or if using SSH key
git clone git@github.com:yourusername/fnv-heerenveen-rebuild.git .
```

### Step 2.5: Copy Environment File

```bash
# Copy your local .env to VPS
scp .env ubuntu@your-vps-ip:/opt/fnv-heerenveen-rebuild/

# Or manually create it
nano .env
# Paste your configuration
```

---

## 3. Docker Deployment

### Step 3.1: Start Docker Services

```bash
cd /opt/fnv-heerenveen-rebuild

# Build and start containers
docker-compose up -d

# Verify all containers are running
docker-compose ps
```

**Expected output:**
```
NAME                COMMAND                  STATE
fnv_app             "/bin/bash -c '/usr/…"  Up 10s (healthy)
fnv_db              "docker-entrypoint.s…"  Up 15s (healthy)
```

### Step 3.2: Wait for Database

```bash
# Wait for database to be ready
sleep 15

# Verify database initialization
docker-compose logs db | grep "ready for connections"

# Should see: [Server] ready for connections
```

### Step 3.3: Verify Application Health

```bash
# Test application is responding
curl http://localhost:8080/health.php

# Expected output: {"status":"ok"}
```

### Step 3.4: Check Logs for Errors

```bash
# View application logs
docker-compose logs app | tail -20

# View database logs
docker-compose logs db | tail -20

# View all logs
docker-compose logs
```

---

## 4. Nginx Reverse Proxy

The Docker app runs on port 8080. Nginx will sit in front and handle HTTPS/SSL.

### Step 4.1: Install Nginx & Certbot

```bash
sudo apt-get install -y nginx certbot python3-certbot-nginx
```

### Step 4.2: Create Nginx Configuration

Create `/etc/nginx/sites-available/fnv-heerenveen`:

```bash
sudo nano /etc/nginx/sites-available/fnv-heerenveen
```

**Paste this config (update domain names):**

```nginx
# HTTP to HTTPS redirect
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.nl www.your-domain.nl;
    
    # Allow Let's Encrypt validation
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
    
    # Redirect all other traffic to HTTPS
    location / {
        return 301 https://$server_name$request_uri;
    }
}

# HTTPS server
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.nl www.your-domain.nl;

    # SSL certificate paths (will be filled in after certbot)
    ssl_certificate /etc/letsencrypt/live/your-domain.nl/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.nl/privkey.pem;

    # SSL configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers 'ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256';
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Proxy to Docker container
    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # Timeouts
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
    }

    # Logging
    access_log /var/log/nginx/fnv-access.log;
    error_log /var/log/nginx/fnv-error.log;
}
```

### Step 4.3: Enable Nginx Site

```bash
# Create symlink
sudo ln -s /etc/nginx/sites-available/fnv-heerenveen /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default 2>/dev/null

# Test Nginx configuration
sudo nginx -t

# Should output:
# nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
# nginx: configuration file /etc/nginx/nginx.conf test is successful
```

### Step 4.4: Start Nginx

```bash
sudo systemctl start nginx
sudo systemctl enable nginx

# Verify
sudo systemctl status nginx
```

---

## 5. SSL/TLS Security

### 5.1 Let's Encrypt Setup

**What is Let's Encrypt?**
- Free SSL/TLS certificates
- Automatic renewal every 90 days
- Industry standard security

#### Step 5.1.1: Get SSL Certificate

```bash
# Create certbot directory
sudo mkdir -p /var/www/certbot

# Get certificate (choose one method)

# Method A: Standalone (simple, works if port 80 is free)
sudo certbot certonly --standalone \
  -d your-domain.nl \
  -d www.your-domain.nl

# Method B: Nginx plugin (recommended)
sudo certbot certonly --nginx \
  -d your-domain.nl \
  -d www.your-domain.nl

# Method C: Webroot (works with running Nginx)
sudo certbot certonly --webroot \
  -w /var/www/certbot \
  -d your-domain.nl \
  -d www.your-domain.nl
```

**Follow certbot prompts:**
1. Enter email address (for renewals)
2. Agree to terms of service
3. Certificate will be issued

**Certificate location:**
```
/etc/letsencrypt/live/your-domain.nl/fullchain.pem
/etc/letsencrypt/live/your-domain.nl/privkey.pem
```

#### Step 5.1.2: Verify Certificate

```bash
# Check certificate details
sudo certbot certificates

# Output:
# - Domains: your-domain.nl, www.your-domain.nl
#   Expiration Date: 2025-11-15
#   Certificate Path: /etc/letsencrypt/live/your-domain.nl/fullchain.pem
#   Private Key Path: /etc/letsencrypt/live/your-domain.nl/privkey.pem

# View certificate expiry
openssl s_client -connect your-domain.nl:443 -servername your-domain.nl | grep notAfter
```

#### Step 5.1.3: Reload Nginx

```bash
# Test configuration
sudo nginx -t

# Reload Nginx with new certificate
sudo systemctl reload nginx

# Verify HTTPS works
curl -I https://your-domain.nl
# Should show 200 OK
```

#### Step 5.1.4: Auto-Renewal Setup

Certbot automatically creates renewal service:

```bash
# Check renewal service
sudo systemctl status certbot.timer

# Output should show: Active: active (running)

# Test dry-run (doesn't actually renew)
sudo certbot renew --dry-run

# View renewal logs
sudo journalctl -u certbot.timer -n 20

# Renewal happens automatically at night
```

### 5.2 Cloudflare Setup (Optional but Recommended)

Cloudflare adds:
- ✅ Free DDoS protection
- ✅ DNS management
- ✅ Caching/CDN
- ✅ WAF (Web Application Firewall)
- ✅ Email forwarding (paid)

#### Step 5.2.1: Create Cloudflare Account

1. Go to https://www.cloudflare.com
2. Click "Sign Up"
3. Enter email and password
4. Verify email

#### Step 5.2.2: Add Domain to Cloudflare

1. Login to dashboard
2. Click "Add a site"
3. Enter: `your-domain.nl`
4. Select **Free** plan
5. Click "Continue"

Cloudflare will scan for existing DNS records.

#### Step 5.2.3: Update Nameservers

Cloudflare shows 2 nameservers like:
```
ns1.cloudflare.com
ns2.cloudflare.com
```

Update at your domain registrar (GoDaddy, Namecheap, 1&1, etc.):

```bash
# Example for GoDaddy:
1. Login to GoDaddy.com
2. Go to Domains → Manage DNS
3. Find "Nameservers"
4. Click "Change"
5. Replace with Cloudflare nameservers
6. Save

# Wait 24-48 hours for propagation
```

**Verify nameserver update:**

```bash
# Check if propagated
dig your-domain.nl NS

# Should show Cloudflare nameservers:
# your-domain.nl. IN NS ns1.cloudflare.com.
# your-domain.nl. IN NS ns2.cloudflare.com.
```

#### Step 5.2.4: Configure DNS Records

In Cloudflare dashboard → DNS:

Add these records:

```
Type: A
Name: @ (or blank)
IPv4: YOUR-VPS-IP-ADDRESS
TTL: Auto
Proxy Status: Proxied (orange cloud)

Type: A
Name: www
IPv4: YOUR-VPS-IP-ADDRESS
TTL: Auto
Proxy Status: Proxied (orange cloud)
```

Click "Save"

#### Step 5.2.5: Configure SSL/TLS

In Cloudflare dashboard → SSL/TLS:

**Overview:**
- Encryption mode: **Full (strict)**
  - ⚠️ Requires valid certificate on origin (Let's Encrypt)

**Edge Certificates:**
- Always Use HTTPS: **ON**
- HSTS (HTTP Strict Transport Security): **Enable**
  - Max-Age: 12 months
  - Include subdomains: **Yes**
  - Preload: **Yes**
- Minimum TLS Version: **TLS 1.2**

#### Step 5.2.6: Configure Caching

In Cloudflare dashboard → Caching:

- **Cache Level:** Standard
- **Browser Cache TTL:** 1 month
- **Cache on Browser:** 30 minutes
- **Purge Cache:** Can manually clear if needed

#### Step 5.2.7: Enable Performance Features

In Cloudflare dashboard → Speed:

- **Auto Minify:** Enable CSS, JavaScript, HTML
- **Polish:** Enable Lossy
- **Rocket Loader:** Disable (test first if enabled)

#### Step 5.2.8: Security Settings

In Cloudflare dashboard → Security:

- **DDoS Protection:** On (Managed)
- **Bot Fight Mode:** Enable (Free tier)
- **Security Level:** Medium (adjust if too strict)
- **Challenge:** JavaScript (recommended)

#### Step 5.2.9: Update Nginx for Cloudflare

When using Cloudflare, update Nginx config to trust Cloudflare's IP headers:

Edit `/etc/nginx/sites-available/fnv-heerenveen`:

Add this inside the `server` block:

```nginx
# Trust Cloudflare's IP headers
set_real_ip_from 103.21.244.0/22;
set_real_ip_from 103.22.200.0/22;
set_real_ip_from 103.31.4.0/22;
set_real_ip_from 104.16.0.0/13;
set_real_ip_from 104.24.0.0/14;
set_real_ip_from 108.162.192.0/18;
set_real_ip_from 131.0.72.0/22;
set_real_ip_from 141.98.251.0/24;
set_real_ip_from 162.158.0.0/15;
set_real_ip_from 172.64.0.0/13;
set_real_ip_from 173.245.48.0/20;
set_real_ip_from 188.114.96.0/20;
set_real_ip_from 190.93.240.0/20;
set_real_ip_from 197.234.240.0/22;
set_real_ip_from 198.41.128.0/17;
set_real_ip_from 2400:cb00::/32;
set_real_ip_from 2606:4700::/32;
set_real_ip_from 2803:f800::/32;
set_real_ip_from 2405:b500::/32;
set_real_ip_from 2405:8100::/32;
set_real_ip_from 2a06:98c0::/29;
set_real_ip_from 2c0f:f248::/32;
real_ip_header CF-Connecting-IP;
```

Reload Nginx:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

#### Step 5.2.10: Verify Cloudflare Setup

```bash
# Check DNS resolves to Cloudflare
dig your-domain.nl

# Check cache status
curl -I https://your-domain.nl | grep -i "cf-cache-status"
# Should show: HIT, MISS, or BYPASS

# Check if getting Cloudflare certificate
curl -I https://your-domain.nl | grep -i "cf-ray"
# Should show a cf-ray ID
```

---

## 6. Admin & Database

### Step 6.1: Access Admin Panel

Navigate to:
```
https://your-domain.nl/admin/login.php
```

**Default credentials:**
- Email: `admin@fnvheerenveen.nl`
- Password: `Admin@FNV2024!`

⚠️ **Change password immediately!**

### Step 6.2: Change Admin Password

#### Option A: Via Database

```bash
# Generate password hash
docker-compose exec app php -r "echo password_hash('YourNewPassword123!', PASSWORD_BCRYPT) . PHP_EOL;"

# Copy the output (looks like: $2y$10$...)

# Login to MySQL
docker-compose exec db mysql -u root -p$MYSQL_ROOT_PASSWORD fnv_heerenveen

# Inside MySQL prompt:
UPDATE users SET password = '$2y$10$PASTE_HERE' WHERE email = 'admin@fnvheerenveen.nl';
EXIT;
```

#### Option B: Via Admin Panel

1. Login to admin panel
2. Change password in user settings (if available)
3. Logout and login again

### Step 6.3: Verify Database

```bash
# Check if database is initialized
docker-compose exec db mysql -u root -p$MYSQL_ROOT_PASSWORD fnv_heerenveen -e "SHOW TABLES;"

# Expected output:
# Tables_in_fnv_heerenveen
# agenda
# contact_messages
# news
# pages
# users
```

### Step 6.4: Seed Test Data (Optional)

```bash
# Add sample news, events, pages
docker-compose exec app php scripts/seed-database.php
```

---

## 7. Backup Strategy

### Step 7.1: Create Backup Directory

```bash
sudo mkdir -p /opt/fnv-backups
sudo chown $USER:$USER /opt/fnv-backups
```

### Step 7.2: Create Backup Script

Create `/opt/backup-db.sh`:

```bash
sudo nano /opt/backup-db.sh
```

**Paste this:**

```bash
#!/bin/bash
set -e

BACKUP_DIR="/opt/fnv-backups"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

echo "[$(date)] Starting database backup..."

# Backup database
cd /opt/fnv-heerenveen-rebuild
docker-compose exec -T db mysqldump \
  -u root \
  -p${MYSQL_ROOT_PASSWORD} \
  fnv_heerenveen > "$BACKUP_DIR/fnv_heerenveen_$DATE.sql"

echo "[$(date)] Backup saved to $BACKUP_DIR/fnv_heerenveen_$DATE.sql"

# Compress backup
gzip "$BACKUP_DIR/fnv_heerenveen_$DATE.sql"

# Keep only last 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "[$(date)] Backup completed. Old backups cleaned."
```

### Step 7.3: Make Executable

```bash
sudo chmod +x /opt/backup-db.sh

# Test backup
sudo /opt/backup-db.sh
```

### Step 7.4: Schedule Daily Backups

```bash
# Edit crontab
sudo crontab -e

# Add this line (backup at 2 AM daily)
0 2 * * * /opt/backup-db.sh >> /var/log/fnv-backup.log 2>&1
```

### Step 7.5: Verify Backups

```bash
# List backups
ls -lh /opt/fnv-backups/

# Test restore (on test system)
gzip -dc /opt/fnv-backups/fnv_heerenveen_*.sql.gz | mysql -u root -p fnv_heerenveen
```

---

## 8. Monitoring

### Step 8.1: Application Health Check

```bash
# Check if application is responsive
curl https://your-domain.nl/health.php

# Expected: {"status":"ok"}

# Add to monitoring (Uptime Robot, Pingdom, etc.)
# URL: https://your-domain.nl/health.php
# Type: HTTP(S)
# Interval: Every 5 minutes
```

### Step 8.2: View Logs

```bash
# Application logs (recent)
docker-compose logs -f app

# Database logs (recent)
docker-compose logs -f db

# All logs
docker-compose logs

# Nginx logs
sudo tail -f /var/log/nginx/fnv-access.log
sudo tail -f /var/log/nginx/fnv-error.log

# System logs
sudo journalctl -u nginx -f
sudo journalctl -u certbot -f
```

### Step 8.3: Monitor Disk Space

```bash
# Check overall disk usage
df -h

# Check application size
du -sh /opt/fnv-heerenveen-rebuild

# Check database size
docker-compose exec db du -sh /var/lib/mysql

# Set alert if > 80%
if [ $(df / | awk 'NR==2 {print $5}' | sed 's/%//') -gt 80 ]; then
  echo "Disk space warning!" | mail -s "Disk Alert" admin@your-domain.nl
fi
```

### Step 8.4: Monitor SSL Certificate

```bash
# Check expiration
sudo certbot certificates

# Days remaining
echo $(( ($(date -d "$(openssl s_client -connect your-domain.nl:443 -servername your-domain.nl 2>/dev/null | grep notAfter | cut -d= -f2)" +%s) - $(date +%s)) / 86400 )) days remaining
```

### Step 8.5: Set Up Monitoring Alerts

```bash
# Email alert if certificate expires in 14 days
(sudo crontab -l 2>/dev/null; echo "0 0 * * * if [ \$(( (date -d \"\$(openssl s_client -connect your-domain.nl:443 2>/dev/null | grep notAfter | cut -d= -f2)\" +%s) - \$(date +%s)) / 86400 )) -lt 14 ]; then echo 'SSL expires soon!' | mail -s 'Certificate Alert' admin@your-domain.nl; fi") | sudo crontab -
```

---

## 9. Updates & Maintenance

### Step 9.1: Update Docker Images

```bash
cd /opt/fnv-heerenveen-rebuild

# Check for new images
docker-compose pull

# Rebuild and restart
docker-compose up -d --build
```

### Step 9.2: Update Application Code

```bash
cd /opt/fnv-heerenveen-rebuild

# Pull latest changes
git pull origin main

# Restart services
docker-compose up -d --build
```

### Step 9.3: Renew SSL Certificate

Automatic renewal happens daily, but manual renewal:

```bash
# Test renewal (no actual changes)
sudo certbot renew --dry-run

# Force renewal if needed
sudo certbot renew --force-renewal

# Reload Nginx with new cert
sudo systemctl reload nginx
```

### Step 9.4: Update System Packages

```bash
# Update system
sudo apt-get update
sudo apt-get upgrade -y

# Reboot if needed (kernel updates)
sudo reboot
```

### Step 9.5: Database Maintenance

```bash
# Optimize database
docker-compose exec db mysql -u root -p$MYSQL_ROOT_PASSWORD fnv_heerenveen -e "OPTIMIZE TABLE news, agenda, pages, contact_messages, users;"

# Check database integrity
docker-compose exec db mysqlcheck -u root -p$MYSQL_ROOT_PASSWORD fnv_heerenveen
```

---

## 10. Troubleshooting

### Problem: "Connection refused" / Cannot reach site

```bash
# Check if Docker services are running
docker-compose ps

# Restart services
docker-compose down
docker-compose up -d

# Check if port 8080 is listening
lsof -i :8080
netstat -tuln | grep 8080

# Check Nginx
sudo systemctl status nginx
sudo nginx -t
```

### Problem: "Blank page" / 500 Error

```bash
# Check application logs
docker-compose logs app | tail -50

# Check database connection
docker-compose logs db | tail -20

# Verify database is initialized
docker-compose exec db mysql -u root -p$MYSQL_ROOT_PASSWORD fnv_heerenveen -e "SELECT COUNT(*) FROM users;"

# Check file permissions
docker-compose exec app ls -la /var/www/html
```

### Problem: SSL Certificate Error

```bash
# Check certificate exists
ls -la /etc/letsencrypt/live/your-domain.nl/

# Check certificate validity
openssl s_client -connect your-domain.nl:443 -servername your-domain.nl

# Check Nginx config
sudo nginx -t
sudo cat /etc/nginx/sites-available/fnv-heerenveen | grep ssl_certificate

# Renewal issues
sudo certbot renew --verbose
journalctl -u certbot.timer -n 50
```

### Problem: "Too many redirects" (with Cloudflare)

```bash
# Verify Cloudflare SSL mode is "Full (strict)"
# In Cloudflare Dashboard → SSL/TLS → Overview
# Should show: "Full (strict)"

# Check origin certificate
openssl s_client -connect localhost:443 -servername your-domain.nl
```

### Problem: DNS not resolving

```bash
# Check nameservers
dig your-domain.nl NS

# Should show Cloudflare (if using Cloudflare)
# ns1.cloudflare.com
# ns2.cloudflare.com

# Check DNS propagation
nslookup your-domain.nl
nslookup your-domain.nl 8.8.8.8  # Google DNS

# Wait 24-48 hours if just changed
```

### Problem: High CPU/Memory Usage

```bash
# Check resource usage
docker stats

# View problematic logs
docker-compose logs app | grep -i "error\|exception"

# Restart services
docker-compose restart

# Check disk space
df -h
du -sh /opt/fnv-heerenveen-rebuild
```

### Problem: Email not sending

```bash
# Verify mail service
sudo systemctl status postfix
# or
sudo systemctl status sendmail

# Test mail
echo "Test" | mail -s "Test Subject" admin@your-domain.nl

# Check mail logs
sudo tail -f /var/log/mail.log

# Configure mail if needed
sudo apt-get install postfix
# Choose: Internet Site
```

---

## 11. Emergency Restore

If something breaks, restore from backup:

### Step 11.1: Stop Services

```bash
docker-compose down

# Wait 10 seconds
sleep 10
```

### Step 11.2: Restore Database

```bash
# Start database only
docker-compose up -d db

# Wait for database to be ready
sleep 20

# Restore from backup
BACKUP_FILE="/opt/fnv-backups/fnv_heerenveen_YYYYMMDD_HHMMSS.sql.gz"

# Decompress if needed
gunzip -c $BACKUP_FILE | docker-compose exec -T db mysql -u root -p$MYSQL_ROOT_PASSWORD fnv_heerenveen
```

### Step 11.3: Restart All Services

```bash
docker-compose up -d

# Verify
docker-compose ps

# Test application
curl https://your-domain.nl/health.php
```

---

## 12. Production Checklist

### Pre-Deployment (Local)
- [ ] `.env.production` created
- [ ] Strong passwords generated
- [ ] `.env` NOT committed to Git
- [ ] All code reviewed
- [ ] No hardcoded credentials
- [ ] Database schema validated

### Deployment Day
- [ ] VPS created with 2GB+ RAM, 20GB+ disk
- [ ] Docker & Docker Compose installed
- [ ] Repository cloned
- [ ] `.env` copied to VPS
- [ ] Docker services started
- [ ] Database initialized
- [ ] Health check passing
- [ ] Nginx installed and configured
- [ ] SSL certificate obtained (Let's Encrypt)
- [ ] Nginx reloaded with SSL
- [ ] Site accessible at https://your-domain.nl

### SSL/DNS
- [ ] Let's Encrypt certificate installed
- [ ] Auto-renewal enabled
- [ ] Certificate monitoring set up
- [ ] Cloudflare setup (optional)
- [ ] Nameservers updated (if Cloudflare)
- [ ] DNS propagation verified (24-48 hours)
- [ ] SSL certificate validity tested
- [ ] HTTPS redirect working
- [ ] Security headers present

### Admin & Security
- [ ] Admin password changed from default
- [ ] Database credentials strong (20+ chars)
- [ ] Only necessary ports open (80, 443, 22)
- [ ] SSH key authentication enabled
- [ ] Firewall rules configured
- [ ] `.env` file permissions restricted (600)
- [ ] No debug/error exposure

### Database & Backups
- [ ] Database initialized
- [ ] All tables created
- [ ] Test data loaded
- [ ] Backup script created
- [ ] Cron job added for backups
- [ ] First backup successful
- [ ] Restore tested

### Monitoring
- [ ] Health check endpoint working
- [ ] Uptime monitoring configured
- [ ] Log monitoring set up
- [ ] Disk space alerts configured
- [ ] SSL expiry alerts configured
- [ ] Email notifications working

### Testing & Verification
- [ ] Homepage loads without errors
- [ ] All pages accessible
- [ ] Images displaying correctly
- [ ] Contact form submissions working
- [ ] Email notifications sent
- [ ] Admin panel accessible
- [ ] Mobile responsive verified
- [ ] SSL certificate valid (green lock)
- [ ] Performance acceptable
- [ ] No console errors

### Post-Deployment (Week 1)
- [ ] Monitor error logs continuously
- [ ] Verify daily backups running
- [ ] Check Cloudflare analytics
- [ ] Review access logs
- [ ] Document any customizations
- [ ] Set up team communication
- [ ] Plan next features

### Post-Deployment (Monthly)
- [ ] Check certificate expiry
- [ ] Review security logs
- [ ] Update Docker images
- [ ] Verify backups
- [ ] Test restore procedure
- [ ] Check disk space
- [ ] Review performance metrics

---

## Quick Reference Commands

### Docker

```bash
cd /opt/fnv-heerenveen-rebuild

# Start/Stop
docker-compose up -d
docker-compose down

# Logs
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs

# Status
docker-compose ps
docker ps

# Restart
docker-compose restart
docker-compose restart app

# Execute command
docker-compose exec app php -r "phpinfo();"
docker-compose exec db mysql -u root -p$MYSQL_ROOT_PASSWORD
```

### Nginx

```bash
# Test config
sudo nginx -t

# Start/Stop/Reload
sudo systemctl start nginx
sudo systemctl stop nginx
sudo systemctl reload nginx
sudo systemctl restart nginx

# Status
sudo systemctl status nginx

# View logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# View config
sudo cat /etc/nginx/sites-available/fnv-heerenveen
```

### SSL Certificate

```bash
# Check status
sudo certbot certificates

# Manual renewal
sudo certbot renew

# Test renewal
sudo certbot renew --dry-run

# View certificate
openssl s_client -connect your-domain.nl:443 -servername your-domain.nl

# Days remaining
echo $(( ($(date -d "$(openssl s_client -connect your-domain.nl:443 -servername your-domain.nl 2>/dev/null | grep notAfter | cut -d= -f2)" +%s) - $(date +%s)) / 86400 )) days
```

### Database

```bash
# Backup
docker-compose exec -T db mysqldump -u root -p$MYSQL_ROOT_PASSWORD fnv_heerenveen > backup.sql

# Restore
docker-compose exec -T db mysql -u root -p$MYSQL_ROOT_PASSWORD fnv_heerenveen < backup.sql

# Login
docker-compose exec db mysql -u root -p$MYSQL_ROOT_PASSWORD fnv_heerenveen

# Inside MySQL:
SELECT * FROM users;
SELECT COUNT(*) FROM news;
SHOW TABLES;
EXIT;
```

### Monitoring

```bash
# Resource usage
docker stats

# Disk space
df -h
du -sh /opt/fnv-heerenveen-rebuild

# Network
netstat -tuln | grep LISTEN
lsof -i :80
lsof -i :443

# Processes
ps aux | grep docker
ps aux | grep nginx
```

---

## Support & Resources

### Useful Links

- **Let's Encrypt:** https://letsencrypt.org
- **Certbot:** https://certbot.eff.org/
- **Cloudflare:** https://www.cloudflare.com
- **Docker:** https://docs.docker.com/
- **Nginx:** https://nginx.org/en/docs/
- **Ubuntu Docs:** https://help.ubuntu.com

### Test Tools

- **SSL Checker:** https://www.ssllabs.com/ssltest/
- **Domain Checker:** https://mxtoolbox.com
- **Uptime Monitor:** https://uptimerobot.com
- **DNS Checker:** https://dnschecker.org

### Getting Help

1. Check application logs: `docker-compose logs app`
2. Check database logs: `docker-compose logs db`
3. Check Nginx logs: `sudo tail -f /var/log/nginx/error.log`
4. Search documentation
5. Contact hosting provider

---

## Timeline

**Estimated deployment time:**

| Task | Time |
|------|------|
| VPS Setup | 5 min |
| Docker Setup | 10 min |
| Nginx Setup | 10 min |
| SSL Certificate | 10 min |
| Cloudflare (optional) | 30 min |
| Testing & Verification | 15 min |
| **Total** | **~80 min (with Cloudflare)** |

---

**Last Updated:** August 2024  
**Version:** 1.0  
**Status:** Production Ready
