# 🚀 Production Deployment Guide - Session Configuration

## ⚠️ IMPORTANT: Live Server Environment Settings

### **❌ DO NOT USE on Live Server:**
```env
SESSION_DRIVER=file    # Only for local development!
CACHE_STORE=file      # Only for local development!
APP_DEBUG=true        # Security risk in production!
SESSION_SECURE_COOKIE=false  # Must be true for HTTPS
```

### **✅ REQUIRED for Live Server:**
```env
# Production Environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://payperviews.net

# Database Sessions (REQUIRED for production)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=payperviews.net
SESSION_SECURE_COOKIE=true

# Database Cache (REQUIRED for production)
CACHE_STORE=database
CACHE_PREFIX=ppv_

# Database Configuration
DB_HOST=localhost
DB_DATABASE=payperviews
DB_USERNAME=payperviews
DB_PASSWORD=%qR1f_RWyo4n6ai

# Queue Configuration
QUEUE_CONNECTION=database
```

## 🔧 Deployment Steps

### 1. **Upload Correct .env File**
Use `.env.production` file on your live server (rename to `.env`)

### 2. **Ensure Sessions Table Exists**
```bash
php artisan session:table
php artisan migrate
```

### 3. **Clear and Cache Configuration**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. **Set Proper Permissions**
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🎯 Why Database Sessions for Production?

### **Performance Benefits:**
- ✅ Faster than file I/O
- ✅ Better for high traffic
- ✅ Supports load balancing
- ✅ Automatic cleanup
- ✅ Scalable across servers

### **Security Benefits:**
- ✅ Not accessible via web
- ✅ Encrypted storage option
- ✅ Better session management
- ✅ Audit trail capability

### **Reliability Benefits:**
- ✅ Atomic operations
- ✅ Crash recovery
- ✅ Backup-able
- ✅ Monitoring capabilities

## 📁 Environment Files Comparison

| Setting | Local (.env) | Production (.env.production) |
|---------|--------------|---------------------------|
| SESSION_DRIVER | `file` | `database` |
| CACHE_STORE | `file` | `database` |
| APP_DEBUG | `true` | `false` |
| APP_ENV | `local` | `production` |
| SESSION_SECURE_COOKIE | `false` | `true` |
| DB_HOST | `127.0.0.1` | `localhost` |
| QUEUE_CONNECTION | `sync` | `database` |

## 🚨 Common Production Issues

### Issue 1: Session Expired Immediately
**Cause:** `SESSION_SECURE_COOKIE=false` on HTTPS site
**Fix:** Set `SESSION_SECURE_COOKIE=true`

### Issue 2: Sessions Not Persisting
**Cause:** Missing sessions table
**Fix:** Run `php artisan session:table && php artisan migrate`

### Issue 3: Cross-Domain Session Issues
**Cause:** Wrong `SESSION_DOMAIN`
**Fix:** Set `SESSION_DOMAIN=payperviews.net`

### Issue 4: File Sessions on Multiple Servers
**Cause:** `SESSION_DRIVER=file` with load balancer
**Fix:** Use `SESSION_DRIVER=database` or `redis`

## 🔄 Quick Environment Switch Commands

```bash
# For Local Development
cp .env.local .env

# For Production Deployment
cp .env.production .env
php artisan config:clear
php artisan config:cache
```

## 📊 Performance Comparison

| Driver | Read Speed | Write Speed | Scalability | Memory Usage |
|--------|------------|-------------|-------------|--------------|
| File | ⭐⭐ | ⭐⭐ | ❌ | Low |
| Database | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ✅ | Medium |
| Redis | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ✅ | High |

**Recommendation:** Use `database` for production unless you have Redis available.
