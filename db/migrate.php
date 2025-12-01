<?php
/**
 * Database Migration Runner
 * 
 * Usage: php db/migrate.php
 */

require_once __DIR__ . '/../src/bootstrap.php';

echo "GymBro Database Migration Tool\n";
echo "===============================\n\n";

$db = getDb();

// Create migrations table if not exists
$db->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_migration (migration)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// Get executed migrations
$stmt = $db->query("SELECT migration FROM migrations ORDER BY id");
$executed = $stmt->fetchAll(\PDO::FETCH_COLUMN);

// Get migration files
$migrationsDir = __DIR__ . '/migrations';
if (!is_dir($migrationsDir)) {
    mkdir($migrationsDir, 0755, true);
    echo "Created migrations directory.\n";
}

$files = glob($migrationsDir . '/*.sql');
sort($files);

$pending = [];
foreach ($files as $file) {
    $name = basename($file);
    if (!in_array($name, $executed)) {
        $pending[] = $file;
    }
}

if (empty($pending)) {
    echo "No pending migrations.\n";
    exit(0);
}

echo "Found " . count($pending) . " pending migration(s).\n\n";

foreach ($pending as $file) {
    $name = basename($file);
    echo "Running: $name... ";
    
    try {
        $sql = file_get_contents($file);
        
        // Split by semicolons but handle multi-statement
        $db->exec($sql);
        
        // Record migration
        $stmt = $db->prepare("INSERT INTO migrations (migration) VALUES (?)");
        $stmt->execute([$name]);
        
        echo "✓ Done\n";
    } catch (\PDOException $e) {
        echo "✗ Failed\n";
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\nAll migrations completed successfully!\n";
