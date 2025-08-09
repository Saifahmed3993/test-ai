<?php
// Database setup script for Railway

// Database configuration from Railway environment variables
$host = getenv('MYSQLHOST');
$username = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$dbName = getenv('MYSQLDATABASE');

try {
    // Connect to MySQL without selecting a database first
    $pdo = new PDO("mysql:host=$host;port=3306", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Career Guidance Database Setup</h2>";
    echo "<p>Setting up database...</p>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✓ Database '$dbName' created successfully</p>";
    
    // Select the database
    $pdo->exec("USE `$dbName`");
    
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
    echo "<p>Your database is ready. You can now:</p>";
    echo "<ul>";
    echo "<li><a href='login.html'>Go to Login Page</a></li>";
    echo "<li><a href='register.html'>Go to Registration Page</a></li>";
    echo "<li><a href='index.html'>Go to Main Application</a></li>";
    echo "</ul>";
    
    echo "<h4>Test Credentials:</h4>";
    echo "<p><strong>Email:</strong> admin@example.com<br>";
    echo "<strong>Password:</strong> password</p>";
    
} catch(PDOException $e) {
    echo "<h2>Error</h2>";
    echo "<p>Database setup failed: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure your Railway MySQL service is running and variables are set correctly.</p>";
}
