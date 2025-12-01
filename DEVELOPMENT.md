# 🛠️ GymBuddy - Developer Tips & Best Practices

## Working with DDEV

### Daily Workflow
```bash
# Morning: Start your dev environment
cd /home/luisalvero/workspace/GYMBRO
ddev start
ddev launch

# Evening: Stop to free resources
ddev stop
```

### Quick Database Access
```bash
# View all users
ddev mysql -e "SELECT id, name, email, city, home_gym FROM gymbro.users;"

# View all matches
ddev mysql -e "SELECT * FROM gymbro.matches;"

# Count users
ddev mysql -e "SELECT COUNT(*) as total_users FROM gymbro.users;"

# Interactive mode
ddev mysql
USE gymbro;
SHOW TABLES;
DESCRIBE users;
```

### Viewing Logs
```bash
# PHP errors
ddev logs | grep -i error

# Follow logs in real-time
ddev logs -f

# Specific container logs
ddev logs web
ddev logs db
```

## Development Tips

### Adding New Routes
1. Add route to `public/index.php`:
```php
'GET' => [
    '/new-page' => 'controllers/newpage.php',
],
```

2. Create controller: `controllers/newpage.php`
3. Create view: `views/newpage.php`

### Creating New Forms
Always include:
```php
<form method="POST" action="/your-action">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <!-- Your form fields -->
</form>
```

### Validating POST Data
```php
// In controller
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    setFlash('danger', 'Invalid request');
    redirect('/back');
}

// Validate and sanitize
$field = trim($_POST['field'] ?? '');
if (empty($field)) {
    $errors[] = 'Field is required';
}
```

### Database Queries
Always use prepared statements:
```php
$db = getDb();

// SELECT
$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

// INSERT
$stmt = $db->prepare('INSERT INTO table (col1, col2) VALUES (?, ?)');
$stmt->execute([$val1, $val2]);

// UPDATE
$stmt = $db->prepare('UPDATE users SET name = ? WHERE id = ?');
$stmt->execute([$name, $id]);

// DELETE
$stmt = $db->prepare('DELETE FROM users WHERE id = ?');
$stmt->execute([$id]);
```

### Working with JSON Fields
```php
// Save JSON
$data = ['item1', 'item2', 'item3'];
$json = json_encode($data);
$stmt = $db->prepare('UPDATE users SET json_field = ? WHERE id = ?');
$stmt->execute([$json, $userId]);

// Read JSON
$stmt = $db->prepare('SELECT json_field FROM users WHERE id = ?');
$stmt->execute([$userId]);
$row = $stmt->fetch();
$data = json_decode($row['json_field'], true) ?? [];
```

## Common Tasks

### Add New User Field
1. Update `db/schema.sql`:
```sql
ALTER TABLE users ADD COLUMN new_field VARCHAR(255) DEFAULT NULL;
```

2. Run migration:
```bash
ddev ssh
mysql -uroot -proot gymbro < /var/www/html/db/schema.sql
```

3. Update `views/profile/edit.php` - add form field
4. Update `controllers/profile/update.php` - add validation
5. Update `views/profile/view.php` - display field

### Add New Workout Style
1. Update signup form: `views/auth/signup.php`
2. Update validation: `src/helpers.php` → `validateWorkoutStyles()`
3. Database already handles JSON arrays

### Change Password Requirements
Update regex in:
- `src/helpers.php` → `validatePassword()`
- `views/auth/signup.php` → `pattern` attribute
- `docs/password-validation.md`

## Testing Tips

### Manual Testing
```bash
# Create test data
ddev mysql
USE gymbro;

-- Insert test user
INSERT INTO users (name, age, pronouns, gender, workout_styles, email, password_hash, home_gym, city)
VALUES ('Test User', 25, 'they/them', 'nonbinary', '["weightlifting","cardio"]', 'test@example.com', 
'$2y$10$...(bcrypt hash)', 'Test Gym', 'Test City');

-- Create a like
INSERT INTO likes (liker_id, liked_id) VALUES (1, 2);

-- Create a match
INSERT INTO matches (user1_id, user2_id) VALUES (1, 2);
```

### Testing Password Hashing
```bash
ddev ssh
php -r "echo password_hash('MyPass123@#', PASSWORD_DEFAULT);"
```

### Clear Test Data
```bash
ddev mysql
USE gymbro;
TRUNCATE TABLE matches;
TRUNCATE TABLE likes;
DELETE FROM users WHERE email LIKE '%test%';
```

## Performance Tips

### Enable Query Logging
```bash
ddev ssh
mysql -uroot -proot -e "SET GLOBAL general_log = 'ON';"
```

### Check Slow Queries
```sql
-- In MySQL
SHOW FULL PROCESSLIST;
EXPLAIN SELECT * FROM users WHERE city = 'San Francisco';
```

### Add Indexes
```sql
-- If queries are slow
CREATE INDEX idx_custom ON users(column_name);
```

## Security Checklist

### Before Production
- [ ] Change database credentials
- [ ] Enable HTTPS only
- [ ] Set secure session settings
- [ ] Add rate limiting
- [ ] Enable CORS properly
- [ ] Set up error logging (not display)
- [ ] Add input sanitization
- [ ] Implement CAPTCHA
- [ ] Add email verification
- [ ] Set up backup strategy

### Regular Checks
```bash
# Check for exposed passwords
grep -r "password" . --include="*.php" | grep -v "password_hash\|password_verify"

# Check for SQL injection risks
grep -r "->query" . --include="*.php"  # Should use prepare() instead

# Check for XSS risks
grep -r "echo \$_" . --include="*.php"  # Should use escape()
```

## Debugging

### Enable Error Display (Dev Only)
Add to `public/index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Debug Variables
```php
// Pretty print arrays
echo '<pre>';
print_r($variable);
echo '</pre>';

// Die and dump
var_dump($variable);
die();

// Log to file
error_log('Debug: ' . print_r($variable, true));
```

### HTMX Debugging
```javascript
// In browser console
htmx.logAll();

// Check HTMX requests in Network tab
// Look for hx-trigger, hx-post, etc.
```

## Code Style

### PHP Standards
```php
// Use type hints when possible
function getUserById(int $id): ?array {
    // ...
}

// Use strict comparison
if ($value === 'something') { }

// Use null coalescing
$name = $user['name'] ?? 'Guest';

// Short array syntax
$array = ['item1', 'item2'];
```

### HTML/Views
```php
// Escape all output
<?= escape($user['name']) ?>

// Use ternary for conditionals
<div class="<?= $isActive ? 'active' : '' ?>">

// Keep views clean
<?php if (!empty($items)): ?>
    <?php foreach ($items as $item): ?>
        <p><?= escape($item) ?></p>
    <?php endforeach; ?>
<?php endif; ?>
```

## Git Workflow

```bash
# Before making changes
git status
git pull origin main

# Make changes
git add .
git commit -m "feat: add new feature"

# Push changes
git push origin main

# Commit message types:
# feat: New feature
# fix: Bug fix
# docs: Documentation
# style: Formatting
# refactor: Code restructuring
# test: Tests
# chore: Maintenance
```

## Useful Aliases

Add to `~/.bashrc`:
```bash
alias gbs='cd /home/luisalvero/workspace/GYMBRO && ddev start'
alias gbstop='cd /home/luisalvero/workspace/GYMBRO && ddev stop'
alias gblogs='cd /home/luisalvero/workspace/GYMBRO && ddev logs -f'
alias gbmysql='cd /home/luisalvero/workspace/GYMBRO && ddev mysql'
alias gbssh='cd /home/luisalvero/workspace/GYMBRO && ddev ssh'
```

## Resources

### Documentation
- PHP: https://www.php.net/manual/
- MySQL: https://dev.mysql.com/doc/
- Bootstrap: https://getbootstrap.com/
- HTMX: https://htmx.org/
- DDEV: https://ddev.readthedocs.io/

### Tools
- PHPStorm (IDE)
- VSCode with PHP extensions
- MySQL Workbench (GUI)
- Postman (API testing)
- Browser DevTools

### Learning
- PHP The Right Way: https://phptherightway.com/
- OWASP Security: https://owasp.org/
- Web.dev (Performance): https://web.dev/

---

Happy coding! 🚀
