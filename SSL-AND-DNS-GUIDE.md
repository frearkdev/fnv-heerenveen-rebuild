# SSL & DNS Setup Guide

Comprehensive guide for setting up SSL certificates and DNS with Let's Encrypt and Cloudflare.

## Quick Comparison

| Feature | Let's Encrypt | Cloudflare |
|---------|---------------|-----------|
| **Cost** | Free | Free (+ paid tiers) |
| **SSL/TLS** | Yes | Yes + Full Page SSL |
| **DNS** | No | Yes |
| **DDoS Protection** | No | Yes |
| **Caching** | No | Yes |
| **Email Forwarding** | No | Yes (paid) |
| **Setup Time** | 10 minutes | 30 minutes |
| **Auto-Renewal** | Yes | N/A |
| **Best For** | Simple sites | More features needed |

## Let's Encrypt Setup

### What is Let's Encrypt?

Let's Encrypt is a free Certificate Authority (CA) that issues SSL/TLS certificates. Perfect for securing websites with HTTPS.

**Pros:**
- ✅ Completely free
- ✅ Automatic renewal every 90 days
- ✅ Fast setup with Certbot
- ✅ Works everywhere

**Cons:**
- ❌ 90-day certificate lifespan
- ❌ No DNS management
- ❌ No additional security features

### Installation

#### Step 1: Install Certbot

```bash
apt-get update
apt-get install -y certbot python3-certbot-nginx
```

#### Step 2: Get Certificate

**Method A: Standalone (simple, stops Nginx temporarily)**

```bash
# Stop Nginx first
systemctl stop nginx

# Get certificate
certbot certonly --standalone \
  -d your-domain.nl \
  -d www.your-domain.nl

# Start Nginx again
systemctl start nginx
```

**Method B: Nginx Plugin (no downtime)**

```bash
certbot certonly --nginx \
  -d your-domain.nl \
  -d www.your-domain.nl
```

**Method C: Webroot (works with running Nginx)**

```bash
certbot certonly --webroot \
  -w /var/www/html \
  -d your-domain.nl \
  -d www.your-domain.nl
```

#### Step 3: Configure Nginx

Update `/etc/nginx/sites-available/fnv-heerenveen`:

```nginx
server {
    listen 80;
    server_name your-domain.nl www.your-domain.nl;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.nl www.your-domain.nl;

    # Let's Encrypt certificate paths
    ssl_certificate /etc/letsencrypt/live/your-domain.nl/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.nl/privkey.pem;

    # SSL settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Test and reload:

```bash
nginx -t
systemctl reload nginx
```

### Automatic Renewal

Certbot automatically creates renewal jobs:

```bash
# Check status
systemctl status certbot.timer

# View logs
journalctl -u certbot.timer -n 20

# Test renewal (doesn't actually renew)
certbot renew --dry-run
```

### Certificate Management

#### View All Certificates

```bash
certbot certificates

# Output:
# - Domains: your-domain.nl, www.your-domain.nl
#   Expiration Date: 2025-11-15
```

#### Renew Specific Certificate

```bash
certbot renew --cert-name your-domain.nl
```

#### Force Renewal (if needed)

```bash
certbot renew --force-renewal
```

#### Delete Certificate

```bash
certbot delete --cert-name your-domain.nl
```

### Troubleshooting Let's Encrypt

**"Connection refused" error**
- Port 80 (or 443) must be open and accessible
- Firewall blocking? Check: `ufw allow 80/tcp`

**"DNS validation failed"**
- DNS not propagated yet (wait up to 48 hours)
- Check: `dig your-domain.nl` should return your VPS IP

**"Certificate already exists"**
- Use: `certbot renew` instead of `certonly`

**Renewal script failing**
- Check logs: `journalctl -u certbot.timer -n 50`
- Verify Nginx is running: `systemctl status nginx`

---

## Cloudflare Setup

### What is Cloudflare?

Cloudflare is a Content Delivery Network (CDN) and DNS provider that also offers SSL/TLS, DDoS protection, caching, and more.

**Pros:**
- ✅ Free DNS with excellent uptime
- ✅ Free DDoS protection
- ✅ Free SSL/TLS certificate
- ✅ Smart caching
- ✅ Additional security features
- ✅ Easy to set up

**Cons:**
- ❌ Slightly more latency (minimal)
- ❌ Must change nameservers
- ❌ Some third-party integrations may conflict

### Installation & Setup

#### Step 1: Create Cloudflare Account

1. Go to https://www.cloudflare.com
2. Click "Sign Up"
3. Enter email and password
4. Verify email
5. Create free account

#### Step 2: Add Your Domain

1. Login to Cloudflare dashboard
2. Click "Add a site" (or "Add domain")
3. Enter: `your-domain.nl`
4. Select **Free** plan
5. Click "Continue"

Cloudflare will scan for existing DNS records (usually finds your current setup).

#### Step 3: Update Nameservers

Cloudflare shows 2 nameservers like:
```
ns1.cloudflare.com
ns2.cloudflare.com
```

Update at your domain registrar (GoDaddy, Namecheap, 1&1, etc.):

1. Login to registrar
2. Find "Nameservers" or "DNS settings"
3. Replace old nameservers with Cloudflare's
4. Save changes

**Wait for DNS propagation (24-48 hours):**

```bash
# Check if propagated
dig your-domain.nl NS

# Should show:
# your-domain.nl. IN NS ns1.cloudflare.com.
# your-domain.nl. IN NS ns2.cloudflare.com.
```

#### Step 4: Add DNS Records

In Cloudflare Dashboard → DNS Records:

**Required records:**

```
Type: A
Name: @ (or blank)
IPv4: YOUR-VPS-IP-ADDRESS
TTL: Auto
Proxy: Proxied (orange cloud icon)

Type: A
Name: www
IPv4: YOUR-VPS-IP-ADDRESS
TTL: Auto
Proxy: Proxied (orange cloud icon)
```

**Save records.**

#### Step 5: Configure SSL/TLS

In Cloudflare Dashboard → SSL/TLS:

1. **Overview tab:**
   - Encryption mode: **Full (strict)**
   - This requires a certificate on your origin (use Let's Encrypt)

2. **Edge Certificates tab:**
   - Always Use HTTPS: **On**
   - HSTS: **Enable**
     - Max Age: 12 months
     - Include Subdomains: **Yes**
     - Preload: **Yes**
   - Minimum TLS Version: **TLS 1.2**

3. **Certificate Transparency Monitoring:**
   - Enable to get alerts

#### Step 6: Configure Caching

In Cloudflare Dashboard → Caching:

1. **Cache Level:** Standard
2. **Browser Cache TTL:** 1 month
3. **Cache on Browser:** 30 minutes
4. **Rules:** Add exceptions if needed

#### Step 7: Optimize Performance

In Cloudflare Dashboard → Speed:

1. **Auto Minify:**
   - ✓ CSS
   - ✓ JavaScript
   - ✓ HTML

2. **Rocket Loader:** Consider disabling initially (test if it breaks anything)

3. **Polish:** 
   - ✓ Lossy (smaller images)

4. **HTTP/2:** Already enabled

#### Step 8: Enable Security Features

In Cloudflare Dashboard → Security:

1. **DDoS Protection:** Enabled
2. **Bot Fight Mode:** Enable (free tier)
3. **Security Level:** Medium (adjust if needed)
4. **Challenge:** JavaScript (for protection)

#### Step 9: Update Your Nginx Config

Nginx should trust Cloudflare IPs to get real client IP:

```nginx
# Cloudflare IP ranges (add these)
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

### Testing Cloudflare Setup

```bash
# Check SSL certificate
curl -I https://your-domain.nl
# Should show:
# HTTP/2 200
# cf-ray: 123456789...
# cf-cache-status: HIT

# Check caching
curl -I https://your-domain.nl | grep -i cf-cache
# Should show: cf-cache-status: HIT (or MISS on first request)

# Check DNS
dig your-domain.nl
# Should show Cloudflare's IP
```

### Troubleshooting Cloudflare

**"Too Many Redirects" Error**
- Cause: SSL mode not set to "Full (strict)"
- Fix: Go to SSL/TLS → Overview → Set to "Full (strict)"

**"SSL certificate mismatch"**
- Cause: No certificate on origin server
- Fix: Install Let's Encrypt certificate first

**DNS Not Resolving**
- Cause: Nameservers not updated or not propagated
- Fix: Wait 24-48 hours, verify with `dig your-domain.nl NS`

**Always Getting MISS on Cache**
- Cause: Cache-Control headers say not to cache
- Fix: Check response headers: `curl -I your-domain.nl | grep -i cache`
- Or set Cache Level to "Cache Everything" (may cause issues)

**Cloudflare SSL but origin has self-signed cert**
- This is fine! Cloudflare validates, visitors see Cloudflare's cert

### Cloudflare Advanced Features

#### Page Rules (for more control)

1. Go to Rules → Page Rules
2. Add rule: `your-domain.nl/*`
3. Settings:
   - Cache Level: Cache Everything
   - Browser Cache TTL: 1 hour
   - Security Level: Medium

#### Firewall Rules (block bad traffic)

1. Go to Security → Firewall Rules
2. Create rule to block countries (if needed):
   - `cf.country not in {"NL"}` (block all except Netherlands)
   - Action: Block

#### Analytics

Monitor traffic and performance:
1. Dashboard → Analytics
2. See traffic, cache hits, threats blocked
3. Real-time monitoring

---

## Comparison: Let's Encrypt Only vs. Cloudflare + Let's Encrypt

### Setup 1: Let's Encrypt Only

```
Domain Registrar → VPS (Nginx + Let's Encrypt)
User → VPS
```

- ✅ Simple
- ✅ Fast
- ✅ Full control
- ❌ No CDN
- ❌ No DDoS protection

**Use case:** Development, testing, simple sites

### Setup 2: Cloudflare + Let's Encrypt (Recommended)

```
Domain Registrar → Cloudflare DNS → VPS (Nginx + Let's Encrypt)
User → Cloudflare → VPS
```

- ✅ Better performance (CDN caching)
- ✅ DDoS protection
- ✅ DNS management
- ✅ Additional security
- ✅ Still free
- ⚠️ Slightly more complex

**Use case:** Production sites, want extra security/performance

---

## Certificate Renewal Strategy

### Let's Encrypt Certificate Lifecycle

```
Day 1-74:     Normal operation
Day 75:       Certbot auto-renewal starts checking
Day 75-90:    Certificate automatically renewed (if renewal succeeds)
Day 90:       Certificate expires (if renewal never happened)
```

### Monitoring Expiration

```bash
# Check expiration monthly
certbot certificates

# Add to crontab for alerts
0 0 1 * * certbot certificates | grep -q "30 days\|14 days\|7 days" && echo "SSL expiring soon!" | mail -s "SSL Alert" admin@your-domain.nl
```

### What Happens If Certificate Expires?

1. Browsers show security warning
2. Users may get "Not Secure" alert
3. Some browsers block the site
4. HTTPS connections fail

⚠️ **Keep auto-renewal enabled!**

---

## Security Best Practices

### 1. Force HTTPS

```nginx
# In server block
if ($scheme != "https") {
    return 301 https://$server_name$request_uri;
}
```

### 2. HSTS Header (Strict Transport Security)

```nginx
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
```

This tells browsers: always use HTTPS for this domain (for 1 year).

### 3. Certificate Transparency

Enable in:
- **Let's Encrypt:** Automatic (no action needed)
- **Cloudflare:** SSL/TLS → Edge Certificates → Enable

### 4. Regular Security Audits

```bash
# Check SSL score
# Go to: https://www.ssllabs.com/ssltest/analyze.html?d=your-domain.nl
# Target: A or A+ grade

# Or use command-line:
nmap --script ssl-enum-ciphers -p 443 your-domain.nl
```

---

## Migration Path

**If switching from Let's Encrypt to Cloudflare:**

1. Create Cloudflare account
2. Add domain to Cloudflare
3. Keep Let's Encrypt certificate on origin
4. Set Cloudflare SSL to "Full (strict)"
5. Update nameservers to Cloudflare
6. Keep auto-renewal on Let's Encrypt
7. Verify everything works

No downtime needed!

---

## FAQ

**Q: Can I use both?**
A: Yes! Cloudflare provides edge SSL, Let's Encrypt secures origin. Recommended setup.

**Q: Which is faster?**
A: Cloudflare (has CDN). Let's Encrypt alone is instant locally.

**Q: What if Let's Encrypt renewal fails?**
A: Certificate stays valid for 90 days. Renewal attempts daily. Check logs if fails repeatedly.

**Q: Can I use Cloudflare for email?**
A: Free tier: not included. Paid plans (Pro+): email forwarding available.

**Q: Is Cloudflare safe?**
A: Yes, trusted by millions. Owned by established company. Open source components.

**Q: How to move to another provider?**
A: Export cert, update nameservers, point to new provider. Simple!

---

## Support & Resources

- **Let's Encrypt:** https://letsencrypt.org/docs/
- **Certbot:** https://certbot.eff.org/
- **Cloudflare:** https://developers.cloudflare.com/
- **Nginx SSL:** https://nginx.org/en/docs/http/ngx_http_ssl_module.html
- **Test SSL:** https://www.ssllabs.com/ssltest/

---

**Last Updated:** August 2024
