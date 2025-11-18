# Production Deployment Quick Start Guide

## Prerequisites Checklist

Before deploying to production, ensure you have:

- [ ] Server access (SSH credentials)
- [ ] Domain name configured
- [ ] SSL certificate ready
- [ ] Database created
- [ ] Redis installed
- [ ] Docker and Docker Compose installed

---

## Quick Setup Commands

### 1. Run Production Setup Script

**Linux/Mac:**
```bash
chmod +x scripts/production-setup.sh
./scripts/production-setup.sh
```

**Windows:**
```bash
bash scripts/production-setup.sh
# Or use PowerShell with WSL
```

This script will:
- Create `.env` from `.env.example`
- Generate `APP_KEY`
- Run security audits
- Test database connection
- Test Redis connection
- Set file permissions
- Optimize application

---

### 2. Configure Environment Variables

Edit `.env` file with production values:

```bash
# Required changes:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_DATABASE=your_production_database
DB_USERNAME=your_database_user
DB_PASSWORD=your_secure_password

REDIS_PASSWORD=your_redis_password
```

---

### 3. Run Security Audit

**Linux/Mac:**
```bash
chmod +x scripts/security-audit.sh
./scripts/security-audit.sh
```

**Windows:**
```bash
scripts\security-audit.bat
```

Address any issues found before proceeding.

---

### 4. Generate PWA Icons

1. Start the development server:
   ```bash
   npm run dev
   php artisan serve
   ```

2. Visit: `http://localhost:8000/pwa/generator.html`

3. Download icons and extract:
   ```bash
   unzip pwa-icons.zip -d public/pwa/
   ```

4. Verify all 8 icon sizes are present

---

### 5. Set Up GitHub Secrets

Follow the guide in `scripts/github-secrets-setup.md`:

1. Go to: `https://github.com/YOUR_USERNAME/YOUR_REPO/settings/secrets/actions`

2. Add these secrets:
   - `STAGING_HOST`
   - `STAGING_USER`
   - `STAGING_SSH_KEY`
   - `STAGING_URL`
   - `PRODUCTION_HOST`
   - `PRODUCTION_USER`
   - `PRODUCTION_SSH_KEY`
   - `PRODUCTION_URL`
   - `SLACK_WEBHOOK`

---

### 6. Deploy with Docker

**Production deployment:**
```bash
# Build and start containers
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Optimize application
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Verify deployment
curl http://localhost:8000/health
```

---

### 7. Verify Deployment

Run these checks:

```bash
# Check containers are running
docker-compose ps

# Check application health
curl http://localhost:8000/health

# Check logs
docker-compose logs -f app

# Test database connection
docker-compose exec app php artisan migrate:status

# Test Redis connection
docker-compose exec app php artisan tinker --execute="Redis::ping();"
```

---

## CI/CD Deployment

Once GitHub secrets are configured:

1. **Deploy to Staging:**
   ```bash
   git checkout staging
   git merge develop
   git push origin staging
   ```
   - GitHub Actions will automatically deploy
   - Check Actions tab for progress
   - Verify via Slack notification

2. **Deploy to Production:**
   ```bash
   git checkout main
   git merge staging
   git push origin main
   ```
   - Requires manual approval in GitHub
   - Zero-downtime deployment
   - Health check runs automatically

---

## Troubleshooting

### Issue: Database connection failed
**Solution:**
```bash
# Check database credentials in .env
# Verify database is running
docker-compose ps postgres

# Check logs
docker-compose logs postgres
```

### Issue: Redis connection failed
**Solution:**
```bash
# Check Redis password in .env
# Verify Redis is running
docker-compose ps redis

# Test connection manually
docker-compose exec redis redis-cli -a YOUR_PASSWORD ping
```

### Issue: Permission denied errors
**Solution:**
```bash
# Fix storage permissions
docker-compose exec app chmod -R 755 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Issue: 502 Bad Gateway
**Solution:**
```bash
# Restart PHP-FPM
docker-compose restart app

# Check logs
docker-compose logs app
```

---

## Post-Deployment Checklist

- [ ] Application is accessible via domain
- [ ] SSL certificate is installed (HTTPS working)
- [ ] Health check endpoint responds (`/health`)
- [ ] Database migrations completed
- [ ] Redis cache is working
- [ ] Queue workers are running
- [ ] Scheduled tasks are running
- [ ] PWA is installable on mobile devices
- [ ] Email sending works
- [ ] File uploads work
- [ ] All pages load correctly
- [ ] Admin panel is accessible
- [ ] Security headers are present (check with securityheaders.com)
- [ ] GDPR compliance features work
- [ ] Monitoring/logging is configured

---

## Rollback Procedure

If deployment fails:

```bash
# Docker deployment
docker-compose down
docker-compose up -d --force-recreate

# Or rollback to previous image
docker-compose pull
docker tag previous-image:tag current-image:latest
docker-compose up -d
```

---

## Support & Documentation

- **Security Checklist:** `SECURITY_CHECKLIST.md`
- **GitHub Secrets Setup:** `scripts/github-secrets-setup.md`
- **PWA Icons Guide:** `scripts/pwa-icons-guide.md`
- **Docker Configuration:** `docker/` directory
- **CI/CD Pipeline:** `.github/workflows/ci-cd.yml`

---

## Next Steps

1. **Schedule penetration testing** using `SECURITY_CHECKLIST.md`
2. **Set up monitoring** (Sentry, New Relic, etc.)
3. **Configure backups** (database, uploads)
4. **Set up alerts** (Slack, email)
5. **Load testing** (see performance benchmarks)
6. **CDN configuration** for static assets
7. **SSL/TLS hardening** (test with ssllabs.com)

**Deployment complete!** 🎉
