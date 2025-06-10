<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = Database::connect();
    
    // Drop table if exists
    $pdo->exec("DROP TABLE IF EXISTS categories");
    
    // Create categories table
    $sql = "CREATE TABLE categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    
    echo "Categories table created successfully\n";
    
} catch (PDOException $e) {
    die("Error creating categories table: " . $e->getMessage() . "\n");
}
?> 