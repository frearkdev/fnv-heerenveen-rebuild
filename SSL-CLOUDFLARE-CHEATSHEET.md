# SSL & Cloudflare Quick Cheatsheet

Fast reference for common SSL and Cloudflare tasks.

## Let's Encrypt Quick Commands

### Get SSL Certificate (Pick One)

```bash
# Standalone (stops Nginx)
systemctl stop nginx
certbot certonly --standalone -d your-domain.nl -d www.your-domain.nl
systemctl start nginx

# Or with Nginx plugin (no downtime)
certbot certonly --nginx -d your-domain.nl -d www.your-domain.nl
```

### Check Certificate Status

```bash
certbot certificates
```

### Renew Certificate Now

```bash
certbot renew
```

### Force Renewal (if needed)

```bash
certbot renew --force-renewal
```

### View Auto-Renewal Status

```bash
systemctl status certbot.timer
```

---

## Nginx SSL Configuration

### Minimal Config

```nginx
server {
    listen 80;
    server_name your-domain.nl www.your-domain.nl;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.nl www.your-domain.nl;

    ssl_certificate /etc/letsencrypt/live/your-domain.nl/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.nl/privkey.pem;

    ssl_protocols TLSv1.2 TLSv1.3;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Complete (Hardened) Config

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.nl www.your-domain.nl;

    ssl_certificate /etc/letsencrypt/live/your-domain.nl/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.nl/privkey.pem;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers 'ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:ECDHE-ECDSA-AES128-GCM-SHA256';
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

---

## Cloudflare Quick Setup

### Step 1: Create Account & Add Domain
1. https://www.cloudflare.com → Sign up
2. Add site → your-domain.nl
3. Choose Free plan

### Step 2: Update Nameservers (at Registrar)

Your registrar (GoDaddy, Namecheap, etc.):
```
Old:         New:
??? →        ns1.cloudflare.com
??? →        ns2.cloudflare.com
```

### Step 3: Add DNS Records in Cloudflare

| Type | Name | Value | Proxy |
|------|------|-------|-------|
| A | @ | YOUR-VPS-IP | Proxied 🟠 |
| A | www | YOUR-VPS-IP | Proxied 🟠 |

### Step 4: Set SSL to "Full (strict)"
Cloudflare Dashboard → SSL/TLS → Full (strict)

### Step 5: Enable HTTPS
Cloudflare Dashboard → SSL/TLS → Edge Certificates → Always Use HTTPS

---

## Cloudflare Nginx Config (with Cloudflare)

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.nl www.your-domain.nl;

    ssl_certificate /etc/letsencrypt/live/your-domain.nl/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.nl/privkey.pem;

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

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header CF-Connecting-IP $http_cf_connecting_ip;
    }
}
```

---

## Common Cloudflare Issues & Fixes

| Problem | Solution |
|---------|----------|
| "Too Many Redirects" | Set SSL to "Full (strict)" |
| "SSL Certificate Error" | Install Let's Encrypt cert on origin |
| DNS not resolving | Wait 24-48h for nameserver change |
| Cache not working | Check "Cache Level" > set to Standard+ |
| Site slow | Enable Polish & Auto Minify |
| Real IP wrong in logs | Add Cloudflare IPs to Nginx (see above) |

---

## DNS Verification

### Check if Cloudflare is working

```bash
# Should show Cloudflare IPs
dig your-domain.nl

# Should return Cloudflare nameservers
dig your-domain.nl NS

# Get Cloudflare ray ID (in response headers)
curl -I https://your-domain.nl | grep cf-ray
```

### Check if SSL is working

```bash
# Should show certificate details
openssl s_client -connect your-domain.nl:443 -servername your-domain.nl

# Check certificate expiry
openssl s_client -connect your-domain.nl:443 -servername your-domain.nl | grep notAfter

# Full SSL report
nmap --script ssl-enum-ciphers -p 443 your-domain.nl
```

---

## Certbot Troubleshooting

### Certificate won't renew

```bash
# Check certbot logs
journalctl -u certbot -n 50

# Check if port 80/443 is open
sudo ufw status

# Try manual renewal with verbose
certbot renew --verbose

# Or specific certificate
certbot renew --cert-name your-domain.nl --verbose
```

### Port 80 already in use

```bash
# Find what's using port 80
lsof -i :80

# Or with netstat
netstat -tuln | grep :80

# Kill the process (if needed)
kill -9 <PID>
```

### Renewal socket missing

```bash
# Restart certbot service
systemctl restart certbot.timer
systemctl status certbot.timer
```

---

## Cloudflare Troubleshooting

### DNS not propagating

```bash
# Check propagation status
nslookup your-domain.nl 1.1.1.1

# Wait and check again (24-48 hours)
# In the meantime, DNS might still resolve to old IP
```

### Cloudflare cache not working

```bash
# Check cache status in response headers
curl -I https://your-domain.nl | grep -i "cf-cache-status"

# HIT = cached (good)
# MISS = not cached (first request)
# EXPIRED = was cached but expired

# Force update cache
curl -I https://your-domain.nl

# If stuck on BYPASS:
# - Check file extensions (some not cached by default)
# - Check Cache-Control headers on origin
# - Manually purge: Cloudflare Dashboard → Caching → Purge
```

### Origin certificate issues

```bash
# Verify origin has valid SSL
openssl s_client -connect localhost:8080 -servername your-domain.nl

# If origin uses self-signed, that's OK with Cloudflare
# As long as Cloudflare's "Full (strict)" is set
```

---

## Monitoring & Alerts

### Set up certificate expiry alert

```bash
# Add to crontab
crontab -e

# Add this line (runs 1st of month)
0 0 1 * * certbot certificates | grep -q "14 days\|7 days\|3 days" && echo "SSL expiring!" | mail -s "SSL ALERT" admin@your-domain.nl
```

### Monitor Cloudflare traffic

```bash
# Check analytics
# Cloudflare Dashboard → Analytics

# View threats blocked
# Dashboard → Security → Events

# Check performance
# Dashboard → Analytics → Performance
```

---

## Maintenance Checklist

### Monthly
- [ ] Check certificate expiry: `certbot certificates`
- [ ] Review Cloudflare analytics
- [ ] Check error logs: `journalctl -xe`

### Quarterly
- [ ] Test SSL certificate renewal: `certbot renew --dry-run`
- [ ] Review Cloudflare security settings
- [ ] Update Nginx config if needed

### Annually
- [ ] Full security audit with: https://ssllabs.com
- [ ] Review and update ciphers/protocols
- [ ] Backup certificates: `tar -czf certs-backup.tar.gz /etc/letsencrypt`

---

## Cost Breakdown

### Let's Encrypt Only
- Let's Encrypt: FREE
- Domain: $10-15/year
- VPS: $5-20/month
- **Total: $75-255/year**

### Let's Encrypt + Cloudflare
- Let's Encrypt: FREE
- Cloudflare: FREE (or $20+ for paid tiers)
- Domain: $10-15/year
- VPS: $5-20/month
- **Total: $75-255/year**

Cloudflare adds no extra cost to Free plan!

---

## Quick Restart Commands

```bash
# Restart services
systemctl restart nginx
systemctl restart certbot

# Reload (graceful)
systemctl reload nginx

# Check status
systemctl status nginx
systemctl status certbot.timer

# View logs
journalctl -u nginx -n 20
journalctl -u certbot -n 20
```

---

For detailed information, see: **SSL-AND-DNS-GUIDE.md** or **DEPLOYMENT.md**
