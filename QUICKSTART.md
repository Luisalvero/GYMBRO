# 🚀 GymBuddy - Startup Checklist

## Prerequisites Check
- [ ] Docker is installed and running
- [ ] DDEV is installed (https://ddev.readthedocs.io/)
- [ ] Git is installed (for cloning)

## Initial Setup (2 minutes)

### Step 1: Navigate to Project
```bash
cd /home/luisalvero/workspace/GYMBRO
```

### Step 2: Start DDEV
```bash
ddev start
```

**Expected output:**
```
Starting gymbro...
Container ddev-gymbro-db ... done
Container ddev-gymbro-web ... done
Successfully started gymbro
Project can be reached at https://gymbro.ddev.site
```

### Step 3: Verify Database
```bash
ddev mysql -e "SHOW TABLES;"
```

**Expected output:**
```
+------------------+
| Tables_in_gymbro |
+------------------+
| likes            |
| matches          |
| users            |
+------------------+
```

### Step 4: Open Application
```bash
ddev launch
```

Or manually visit: **https://gymbro.ddev.site**

## First-Time Usage

### Create Your First User
1. Click **Sign Up**
2. Fill in the form:
   - **Name**: Your Name
   - **Age**: 25
   - **Pronouns**: Select one
   - **Gender**: Select one
   - **Activity Level**: Select one (optional)
   - **Workout Styles**: Check at least one
   - **Email**: youremail@example.com
   - **Password**: `MyPass123@#` (or create your own)
   - **Confirm Password**: Same as above
3. Click **Sign Up**
4. You'll be redirected to edit profile

### Complete Your Profile
1. Add a **bio** (optional)
2. Enter your **home gym**
3. Enter your **city**
4. Select **gender preferences** for matching
5. Click **Save Changes**

### Create Test Users for Matching
To test the matching feature, create at least 2 users:

#### User 1
- **Name**: John Doe
- **City**: San Francisco
- **Home Gym**: Gold's Gym
- **Workout Styles**: Weightlifting, Cardio
- **Email**: john@test.com
- **Password**: Test123@#

#### User 2
- **Name**: Jane Smith
- **City**: San Francisco
- **Home Gym**: Planet Fitness
- **Workout Styles**: Weightlifting, Calisthenics
- **Email**: jane@test.com
- **Password**: Test123@#

### Test the Matching
1. Login as **John**
2. Go to **Find Partners**
3. You should see **Jane** (same city + overlapping workout style)
4. Click **Like**
5. Logout
6. Login as **Jane**
7. Go to **Find Partners**
8. You should see **John**
9. Click **Like**
10. You'll see **"🎉 It's a match!"**
11. Check **Matches** page to see the match!

## Verification Checklist

### ✅ Application Working
- [ ] Homepage loads (redirects to /login)
- [ ] Sign up page loads
- [ ] Can create a user account
- [ ] Can login with credentials
- [ ] Dashboard/feed loads after login
- [ ] Can view profile
- [ ] Can edit profile
- [ ] Can access partner queue
- [ ] Can access matches page

### ✅ Database Working
- [ ] Users table exists
- [ ] Likes table exists
- [ ] Matches table exists
- [ ] User data is saved correctly
- [ ] Passwords are hashed (not plain text)

### ✅ Security Working
- [ ] Cannot access protected pages without login
- [ ] CSRF tokens present in forms
- [ ] Passwords meet requirements
- [ ] Sessions persist across page loads

### ✅ Features Working
- [ ] Sign up with all required fields
- [ ] Login/logout works
- [ ] Profile view shows all data
- [ ] Profile edit saves changes
- [ ] Queue shows potential matches
- [ ] Like button creates likes
- [ ] Mutual likes create matches
- [ ] Matches page shows all matches

### ✅ UI/UX Working
- [ ] Bootstrap styling applied
- [ ] Navigation bar works
- [ ] Forms are styled nicely
- [ ] Buttons have hover effects
- [ ] Icons display properly
- [ ] Responsive on mobile
- [ ] Flash messages appear
- [ ] Alerts are dismissible

## Troubleshooting

### Issue: DDEV won't start
```bash
ddev poweroff
docker system prune -f
ddev start
```

### Issue: Database not created
```bash
ddev ssh
mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS gymbro;"
mysql -uroot -proot gymbro < /var/www/html/db/schema.sql
exit
```

### Issue: Can't access site
```bash
# Check DDEV status
ddev describe

# Check if ports are in use
ddev stop
ddev start
```

### Issue: Session errors
```bash
ddev ssh
rm -rf /tmp/sessions/*
exit
```

### Issue: Need to reset everything
```bash
# Drop and recreate database
ddev ssh
mysql -uroot -proot -e "DROP DATABASE IF EXISTS gymbro; CREATE DATABASE gymbro;"
mysql -uroot -proot gymbro < /var/www/html/db/schema.sql
exit
```

## Quick Commands Reference

```bash
# Start/Stop
ddev start              # Start project
ddev stop               # Stop project
ddev restart            # Restart project

# Access
ddev launch             # Open in browser
ddev describe           # Show project info

# Database
ddev mysql              # Open MySQL CLI
ddev mysql -e "QUERY"   # Run SQL query

# Debugging
ddev logs               # View all logs
ddev logs -f            # Follow logs
ddev ssh                # SSH into web container

# Maintenance
ddev poweroff           # Stop all DDEV projects
```

## Project URLs

- **Application**: https://gymbro.ddev.site
- **PHPMyAdmin** (if enabled): https://gymbro.ddev.site:8037
- **MailHog** (if needed): https://gymbro.ddev.site:8026

## Database Credentials

- **Host**: `db` (from container) or `127.0.0.1` (from host)
- **Database**: `gymbro`
- **Username**: `db`
- **Password**: `db`
- **Root Username**: `root`
- **Root Password**: `root`

## Next Steps

Once everything is working:
1. ✅ Read `TESTING.md` for comprehensive test plan
2. ✅ Review `PROJECT_SUMMARY.md` for full feature list
3. ✅ Check `docs/password-validation.md` for password requirements
4. ✅ Start building additional features!

## Support & Documentation

- **DDEV Docs**: https://ddev.readthedocs.io/
- **PHP Manual**: https://www.php.net/manual/
- **Bootstrap 5**: https://getbootstrap.com/docs/5.3/
- **HTMX**: https://htmx.org/docs/

---

**You're all set!** 🎉 Happy coding! 💪
