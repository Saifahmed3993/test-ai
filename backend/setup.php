<?php
// Database setup script for Railway

// Read database credentials from environment variables
$host = getenv('MYSQLHOST');
$port = getenv('MYSQLPORT');
$db   = getenv('MYSQL_DATABASE');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');

try {
    // Connect to MySQL without selecting a database first
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Career Guidance Database Setup</h2>";
    echo "<p>Setting up database...</p>";

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✓ Database '$db' created successfully</p>";

    // Select the database
    $pdo->exec("USE `$db`");

    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p>✓ Users table created successfully</p>";

    // Insert sample user
    $samplePassword = password_hash('password', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['admin', 'admin@example.com', $samplePassword]);
    echo "<p>✓ Sample user created (admin@example.com / password)</p>";

    echo "<h3>Setup Complete!</h3>";
    echo "<p>Your database is ready.</p>";

} catch(PDOException $e) {
    echo "<h2>Error</h2>";
    echo "<p>Database setup failed: " . $e->getMessage() . "</p>";
}
?>
