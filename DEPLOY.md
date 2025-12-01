# GymBro Production Deployment Guide

## Quick Deploy Options

### Option 1: Railway (Easiest - Free Tier Available)
1. Push your code to GitHub
2. Go to [railway.app](https://railway.app)
3. Create new project → Deploy from GitHub repo
4. Add MySQL database service
5. Set environment variables (copy from .env.production)
6. Railway auto-deploys on every push

### Option 2: DigitalOcean App Platform
1. Create account at [digitalocean.com](https://digitalocean.com)
2. Create App → Connect GitHub repo
3. Add MySQL database ($7/mo)
4. Configure environment variables
5. Deploy

### Option 3: VPS (Most Control) - $5-10/month
Providers: DigitalOcean, Vultr, Linode, Hetzner

---

## VPS Setup Guide (Ubuntu 22.04)

### 1. Initial Server Setup

```bash
# Connect to your server
ssh root@your-server-ip

# Update system
apt update && apt upgrade -y

# Create non-root user
adduser gymbro
usermod -aG sudo gymbro

# Setup SSH key for new user
mkdir -p /home/gymbro/.ssh
cp ~/.ssh/authorized_keys /home/gymbro/.ssh/
chown -R gymbro:gymbro /home/gymbro/.ssh

# Disable root SSH login
sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
systemctl restart sshd
```

### 2. Install Required Software

```bash
# Login as gymbro user
su - gymbro

# Install PHP 8.2 and extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd

# Install MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Nginx
sudo apt install -y nginx

# Install Certbot for SSL
sudo apt install -y certbot python3-certbot-nginx

# Install Git
sudo apt install -y git
```

### 3. Configure MySQL

```bash
sudo mysql

# In MySQL shell:
CREATE DATABASE gymbro_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'gymbro_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON gymbro_prod.* TO 'gymbro_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Deploy Application

```bash
# Create web directory
sudo mkdir -p /var/www/gymbro
sudo chown gymbro:www-data /var/www/gymbro

# Clone repository
cd /var/www/gymbro
git clone https://github.com/yourusername/gymbro.git .

# Set permissions
sudo chown -R gymbro:www-data /var/www/gymbro
sudo chmod -R 755 /var/www/gymbro
sudo chmod -R 775 /var/www/gymbro/storage
sudo chmod -R 775 /var/www/gymbro/public/uploads

# Create .env file
cp .env.production .env
nano .env  # Edit with your production values

# Run migrations
php db/migrate.php
```

### 5. Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/gymbro
```

Add this configuration:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/gymbro/public;
    index index.php;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    # Upload size
    client_max_body_size 64M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(ht|git|env) {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2?)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/gymbro /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

### 6. Setup SSL (HTTPS)

```bash
# Get SSL certificate (replace with your domain)
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal is set up automatically
sudo systemctl enable certbot.timer
```

### 7. Configure Firewall

```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### 8. Setup Auto-Deploy (Optional)

Create deploy script:
```bash
nano /var/www/gymbro/deploy.sh
```

```bash
#!/bin/bash
cd /var/www/gymbro
git pull origin main
php db/migrate.php
sudo systemctl reload php8.2-fpm
echo "Deployed at $(date)"
```

```bash
chmod +x /var/www/gymbro/deploy.sh
```

---

## Testing on Phone

### Local Network Testing (Before Production)
1. Find your computer's local IP: `hostname -I` or `ip addr`
2. Configure DDEV for network access:
   ```bash
   ddev config --additional-hostnames="$(hostname -I | awk '{print $1}')"
   ddev restart
   ```
3. Access from phone: `http://YOUR-LOCAL-IP:PORT`

### Production Testing
Once deployed, simply visit your domain on your phone's browser.

---

## Domain Setup

1. Buy domain from Namecheap, Cloudflare, or GoDaddy
2. Point DNS A record to your server IP
3. Wait for DNS propagation (5min - 48hrs)
4. Run certbot for SSL

---

## Maintenance Commands

```bash
# View logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/www/gymbro/storage/logs/error.log

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart mysql

# Database backup
mysqldump -u gymbro_user -p gymbro_prod > backup_$(date +%Y%m%d).sql

# Update application
cd /var/www/gymbro && ./deploy.sh
```

---

## Security Checklist

- [ ] Non-root SSH user
- [ ] SSH key authentication only
- [ ] Firewall enabled (UFW)
- [ ] SSL/HTTPS enabled
- [ ] APP_DEBUG=false in production
- [ ] Secure database password
- [ ] Regular backups configured
- [ ] .env file not in git
- [ ] Upload directory secured
