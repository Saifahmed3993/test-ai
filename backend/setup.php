<?php
// Database setup script
// Run this file once to create the database and tables

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL without selecting a database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Career Guidance Database Setup</h2>";
    echo "<p>Setting up database...</p>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS career_guidance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✓ Database 'career_guidance' created successfully</p>";
    
    // Select the database
    $pdo->exec("USE career_guidance");
    
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
    echo "<p>Please make sure:</p>";
    echo "<ul>";
    echo "<li>XAMPP is running</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>You have proper permissions</li>";
    echo "</ul>";
}
?>

<style>
body {
    font-family: 'Inter', sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f8fafc;
}

h2 {
    color: #8B5CF6;
    border-bottom: 2px solid #8B5CF6;
    padding-bottom: 10px;
}

p {
    line-height: 1.6;
    color: #374151;
}

ul {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

li {
    margin-bottom: 10px;
}

a {
    color: #8B5CF6;
    text-decoration: none;
    font-weight: 500;
}

a:hover {
    text-decoration: underline;
}

h3 {
    color: #10B981;
    margin-top: 30px;
}

h4 {
    color: #3B82F6;
    margin-top: 20px;
}
</style>
