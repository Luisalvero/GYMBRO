# GymBro MVP

A dark, intense social platform for finding workout partners with matching preferences and locations.

## Quick Start

```bash
# Clone the repo
git clone <your-repo-url>
cd GYMBRO

# Start DDEV
ddev start

# Access the app
ddev launch
```

The app will be available at `https://gymbro.ddev.site`

## Features

- **Sign Up / Sign In** with comprehensive profile fields
- **Password Policy**: 8+ chars, 1 uppercase, 2 digits, 2 symbols (@#!?)
- **Profile Management**: Edit bio, home gym, city
- **Partner Queue**: Tinder-like interface to find workout partners
- **Matching System**: Like/Pass functionality with mutual match detection
- **Feed Placeholder**: Ready for future post functionality
- **Dark Theme**: Minimalistic, modern, and intense design

## Tech Stack

- PHP 8.2 (vanilla)
- MySQL 8.0
- Bootstrap 5 (custom dark theme)
- HTMX (CDN)
- DDEV for local development

## Default Test User

After signup, you can create test users to see the matching system work.

## Architecture

- `public/` - Web root with front controller
- `views/` - HTML templates with partials
- `db/` - Database schema
- `src/` - PHP business logic

## Security

- CSRF protection on all forms
- Password hashing with bcrypt
- PDO prepared statements
- Session-based authentication
