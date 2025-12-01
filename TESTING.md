# GymBuddy Testing Guide

## Manual Testing Checklist

### 1. Initial Setup
- [ ] Run `ddev start` successfully
- [ ] Database `gymbro` is created
- [ ] Schema is imported (users, likes, matches tables exist)
- [ ] App accessible at https://gymbro.ddev.site
- [ ] Homepage redirects to /login

### 2. Sign Up Flow
#### Test Case 1: Valid Signup
- [ ] Navigate to /signup
- [ ] Fill in all required fields:
  - Name: "John Doe"
  - Age: 25
  - Pronouns: he/him
  - Gender: male
  - Activity Level: super_gymbro
  - Workout Styles: Check "weightlifting" and "cardio"
  - Email: john@example.com
  - Password: MyPass123@#
  - Confirm Password: MyPass123@#
- [ ] Submit form
- [ ] Redirects to /profile/edit
- [ ] Flash message: "Welcome to GymBuddy, John Doe!"
- [ ] User is logged in (navbar shows profile link)

#### Test Case 2: Password Validation
Try these passwords (should fail):
- [ ] "short1@#" (too short, < 8 chars)
- [ ] "nouppercase123@#" (no uppercase)
- [ ] "NoDigits@#" (less than 2 digits)
- [ ] "NoSymbols123" (less than 2 symbols)
- [ ] "WrongSymbol123$%" (wrong symbols, not @#!?)
- [ ] "ValidPass123@#" (should work!)

#### Test Case 3: Duplicate Email
- [ ] Create user with john@example.com
- [ ] Try to create another with same email
- [ ] Should show error: "Email address is already registered"

#### Test Case 4: Missing Required Fields
- [ ] Try submitting without name → should show error
- [ ] Try submitting without workout styles → should show error
- [ ] Try submitting with age < 13 → should show error
- [ ] Try submitting with age > 100 → should show error

### 3. Login Flow
#### Test Case 5: Valid Login
- [ ] Navigate to /login
- [ ] Enter valid credentials
- [ ] Redirects to /feed
- [ ] Shows welcome back message

#### Test Case 6: Invalid Login
- [ ] Enter wrong email → shows "Invalid email or password"
- [ ] Enter wrong password → shows "Invalid email or password"
- [ ] Enter empty fields → shows validation errors

### 4. Profile Management
#### Test Case 7: View Profile
- [ ] Click "Profile" in navbar
- [ ] See all user details (name, age, pronouns, etc.)
- [ ] See workout styles as badges
- [ ] See "Edit Profile" button

#### Test Case 8: Edit Profile
- [ ] Click "Edit Profile"
- [ ] Fill in optional fields:
  - Bio: "Love lifting heavy! Looking for a gym buddy."
  - Home Gym: "Gold's Gym Downtown"
  - City: "San Francisco, CA"
  - Gender Preferences: Check "female" and "nonbinary"
- [ ] Submit form
- [ ] Redirects to /profile
- [ ] Shows success message
- [ ] All fields are displayed correctly

#### Test Case 9: Update Profile Multiple Times
- [ ] Edit profile again with different values
- [ ] Verify changes are saved
- [ ] Try leaving fields empty (should clear them)

### 5. Partner Queue (Tinder-like Matching)
#### Test Case 10: Setup Test Users
Create 3 test users with matching criteria:

**User 1 (John)**
- City: "San Francisco"
- Home Gym: "Gold's Gym"
- Workout Styles: weightlifting, cardio

**User 2 (Jane)**
- City: "San Francisco"
- Home Gym: "Planet Fitness"
- Workout Styles: weightlifting, calisthenics

**User 3 (Alex)**
- City: "Los Angeles"
- Home Gym: "Gold's Gym"
- Workout Styles: cardio, athletic

#### Test Case 11: Queue Filtering
- [ ] Login as John
- [ ] Go to /queue
- [ ] Should see Jane (same city + overlapping workout: weightlifting)
- [ ] Should NOT see Alex (different city, different gym, no workout overlap with location)

#### Test Case 12: Like/Pass Functionality
- [ ] As John, click "Like" on Jane
- [ ] Should reload and show next candidate (or "no more matches")
- [ ] No match notification (Jane hasn't liked back yet)

#### Test Case 13: Mutual Match
- [ ] Logout John
- [ ] Login as Jane
- [ ] Go to /queue
- [ ] Should see John
- [ ] Click "Like" on John
- [ ] Should show alert: "🎉 It's a match!"
- [ ] Page reloads

#### Test Case 14: View Matches
- [ ] As Jane, click "Matches" in navbar
- [ ] Should see John in matches list
- [ ] Shows John's details (name, age, workout styles, etc.)
- [ ] Shows match date

- [ ] Logout Jane
- [ ] Login as John
- [ ] Click "Matches"
- [ ] Should also see Jane in matches list

#### Test Case 15: No Candidates
- [ ] Create a user with unique criteria that don't match anyone
- [ ] Go to /queue
- [ ] Should show "No More Matches Right Now" message
- [ ] Shows buttons to edit profile or view matches

### 6. Feed
#### Test Case 16: Feed Placeholder
- [ ] Login as any user
- [ ] Go to /feed (or click "Feed" in navbar)
- [ ] Should see welcome message
- [ ] Shows "Coming Soon" alert
- [ ] Has buttons to find partners and view matches

### 7. Security Tests
#### Test Case 17: CSRF Protection
- [ ] Open browser dev tools
- [ ] Try to submit a form without csrf_token
- [ ] Should be rejected with "Invalid request" message

#### Test Case 18: Authentication Required
- [ ] Logout
- [ ] Try to access /profile directly → redirects to /login
- [ ] Try to access /queue directly → redirects to /login
- [ ] Try to access /feed directly → redirects to /login

#### Test Case 19: Password Storage
- [ ] Access database: `ddev mysql`
- [ ] Run: `SELECT id, email, password_hash FROM users LIMIT 1;`
- [ ] Verify password_hash is a bcrypt hash (starts with $2y$)
- [ ] Verify it's NOT plain text

### 8. Session Management
#### Test Case 20: Logout
- [ ] Click "Logout" in navbar
- [ ] Redirects to /login
- [ ] Shows "You have been logged out successfully" message
- [ ] Cannot access protected pages without logging in again

#### Test Case 21: Session Persistence
- [ ] Login as user
- [ ] Navigate to different pages
- [ ] Close and reopen browser
- [ ] Should still be logged in (session persists)

### 9. UI/UX Tests
#### Test Case 22: Responsive Design
- [ ] Open app on mobile viewport (DevTools responsive mode)
- [ ] Verify navbar collapses to hamburger menu
- [ ] Verify cards stack vertically
- [ ] Verify forms are readable on small screens

#### Test Case 23: Bootstrap Components
- [ ] Verify all buttons have proper styling
- [ ] Verify cards have shadows and hover effects
- [ ] Verify badges display correctly
- [ ] Verify icons (Bootstrap Icons) render properly
- [ ] Verify alerts are dismissible

#### Test Case 24: HTMX Functionality
- [ ] In queue, click "Like" button
- [ ] Verify request is sent without page reload (check Network tab)
- [ ] Verify page reloads after response
- [ ] Click "Pass" button
- [ ] Verify same HTMX behavior

### 10. Edge Cases
#### Test Case 25: Special Characters
- [ ] Create user with name: "O'Brien"
- [ ] Create user with bio containing quotes and symbols
- [ ] Verify data displays correctly (no XSS, proper escaping)

#### Test Case 26: Long Text
- [ ] Enter 500 character bio (max length)
- [ ] Verify it saves correctly
- [ ] Try 501 characters → should show error

#### Test Case 27: SQL Injection Attempt
- [ ] Try entering `' OR '1'='1` in email field
- [ ] Should not cause any issues (prepared statements)

## Database Verification

### Check Tables Exist
```sql
ddev mysql
USE gymbro;
SHOW TABLES;
-- Should show: users, likes, matches
```

### Check User Data
```sql
SELECT id, name, email, age, pronouns, gender, workout_styles, home_gym, city 
FROM users;
```

### Check Likes
```sql
SELECT l.*, 
       u1.name as liker_name, 
       u2.name as liked_name
FROM likes l
JOIN users u1 ON l.liker_id = u1.id
JOIN users u2 ON l.liked_id = u2.id;
```

### Check Matches
```sql
SELECT m.*, 
       u1.name as user1_name, 
       u2.name as user2_name
FROM matches m
JOIN users u1 ON m.user1_id = u1.id
JOIN users u2 ON m.user2_id = u2.id;
```

## Performance Tests

### Test Case 28: Multiple Concurrent Users
- [ ] Open 3 browser windows (different profiles/incognito)
- [ ] Login as 3 different users simultaneously
- [ ] Perform actions in each
- [ ] Verify no cross-contamination of sessions

### Test Case 29: Database Queries
- [ ] Enable MySQL slow query log
- [ ] Perform queue operations
- [ ] Verify queries use indexes (workout_styles JSON query might be slow)

## Success Criteria

✅ All authentication flows work correctly
✅ Profile creation and editing works
✅ Partner queue shows relevant candidates
✅ Like/Pass functionality works
✅ Matches are created correctly
✅ No SQL injection vulnerabilities
✅ CSRF protection works
✅ Passwords are hashed
✅ Sessions are secure
✅ UI is responsive and user-friendly
✅ HTMX enhances form submissions

## Known Limitations (MVP)

- No email verification
- No password reset functionality
- No profile photos
- No messaging between matches
- No advanced search/filters
- Queue filtering happens partially in PHP (JSON querying)
- Pass action stores in same table as likes
- No pagination for matches
- No user blocking/reporting

## Next Steps After Testing

1. Fix any bugs found
2. Add more test users for realistic demo
3. Consider adding seed data script
4. Optimize database queries
5. Add rate limiting
6. Implement caching
7. Add logging
8. Write unit tests
