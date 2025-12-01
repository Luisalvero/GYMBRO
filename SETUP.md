# GymBuddy - Quick Start Guide

## Prerequisites
- Docker & Docker Compose
- DDEV installed ([https://ddev.readthedocs.io/](https://ddev.readthedocs.io/))

## Installation & Setup

### 1. Navigate to the project directory
```bash
cd /home/luisalvero/workspace/GYMBRO
```

### 2. Start DDEV
```bash
ddev start
```

This will:
- Start the containers (nginx, PHP 8.2, MySQL 8.0)
- Create the `gymbro` database
- Import the schema automatically
- Set up the web server

### 3. Access the application
```bash
ddev launch
```

Or visit: `https://gymbro.ddev.site`

## Testing the Application

### Create Your First Account
1. Go to the Sign Up page
2. Fill in all required fields:
   - Name, Age (13-100)
   - Pronouns & Gender
   - At least one workout style
   - Email (must be unique)
   - Password: Min 8 chars, 1 uppercase, 2 digits, 2 symbols from @#!?
   - Example password: `MyPass123@#`

### Complete Your Profile
After signup, you'll be redirected to edit your profile:
- Add a bio
- Set your home gym
- Set your city
- (Optional) Set gender preferences for partner matching

### Test the Partner Queue
1. Create 2-3 test accounts with:
   - Same city or gym
   - Overlapping workout styles
2. Log in as one user and visit "Find Partners"
3. Like or Pass on suggested partners
4. When two users like each other, a match is created!

### View Your Matches
- Click "Matches" in the navigation
- See all mutual matches with their details

## DDEV Commands

```bash
# Start the project
ddev start

# Stop the project
ddev stop

# Restart the project
ddev restart

# Access MySQL
ddev mysql

# View logs
ddev logs

# SSH into web container
ddev ssh

# Launch in browser
ddev launch

# Get project info
ddev describe
```

## Database Access

### Via CLI
```bash
ddev mysql
```

### Connection Details
- **Host**: `db` (from within container) or `127.0.0.1` (from host)
- **Port**: Run `ddev describe` to see the host port (usually 32768+)
- **Database**: `gymbro`
- **Username**: `db`
- **Password**: `db`
- **Root Password**: `root`

## Project Structure

```
GYMBRO/
├── .ddev/
│   └── config.yaml          # DDEV configuration
├── controllers/             # PHP controllers
│   ├── auth/               # Login, signup, logout
│   ├── profile/            # Profile view & edit
│   ├── queue/              # Partner matching
│   ├── matches/            # Matches list
│   └── feed/               # Main feed
├── db/
│   └── schema.sql          # Database schema
├── public/
│   ├── index.php           # Front controller/router
│   └── .htaccess           # URL rewriting
├── src/
│   └── helpers.php         # Helper functions
├── views/                  # HTML templates
│   ├── partials/           # Header & footer
│   ├── auth/               # Auth pages
│   ├── profile/            # Profile pages
│   ├── queue/              # Partner queue
│   ├── matches/            # Matches list
│   ├── feed/               # Feed page
│   └── errors/             # Error pages
└── README.md
```

## Features Implemented

✅ **Authentication**
- Sign up with comprehensive profile fields
- Password validation (regex-based)
- Secure login with password hashing
- Session management
- CSRF protection

✅ **Profile Management**
- View profile with all details
- Edit bio, home gym, city
- Set gender preferences for matching

✅ **Partner Matching (Tinder-like)**
- Smart filtering by location & workout styles
- Like/Pass functionality with HTMX
- Mutual match detection
- Match notifications

✅ **Security**
- Password hashing (bcrypt)
- CSRF tokens on all forms
- PDO prepared statements
- Session security settings
- Input validation (server & client)

✅ **Tech Stack**
- Vanilla PHP 8.2
- MySQL 8.0 with PDO
- Bootstrap 5 for styling
- HTMX for dynamic forms
- Clean URL routing
- Responsive design

## Troubleshooting

### Database not created?
```bash
ddev ssh
mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS gymbro;"
mysql -uroot -proot gymbro < /var/www/html/db/schema.sql
```

### Clear cache/sessions?
```bash
ddev ssh
rm -rf /tmp/sessions/*
```

### Reset database?
```bash
ddev ssh
mysql -uroot -proot -e "DROP DATABASE IF EXISTS gymbro; CREATE DATABASE gymbro;"
mysql -uroot -proot gymbro < /var/www/html/db/schema.sql
```

## Next Steps for Production

- [ ] Add email verification
- [ ] Implement password reset
- [ ] Add profile photos/avatars
- [ ] Build messaging system
- [ ] Create workout posts for feed
- [ ] Add search functionality
- [ ] Implement reporting/blocking
- [ ] Add workout tracking
- [ ] Build notification system
- [ ] Add API endpoints

## Support

For DDEV issues: [https://ddev.readthedocs.io/](https://ddev.readthedocs.io/)

Enjoy finding your perfect workout partner! 💪
