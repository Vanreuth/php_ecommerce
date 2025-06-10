<?php
require_once 'admin/config/database.php';

try {
    $pdo = Database::connect();
    
    // Test the connection
    echo "Database connection successful!\n\n";
    
    // Run the categories query
    $stmt = $pdo->query("SELECT `id`, `name`, `description`, `created_at` FROM `categories`");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Categories found: " . count($categories) . "\n\n";
    
    if (count($categories) > 0) {
        echo "Categories list:\n";
        foreach ($categories as $category) {
            echo "ID: " . $category['id'] . "\n";
            echo "Name: " . $category['name'] . "\n";
            echo "Description: " . $category['description'] . "\n";
            echo "Created at: " . $category['created_at'] . "\n";
            echo "------------------------\n";
        }
    } else {
        echo "No categories found. Let's create the table if it doesn't exist.\n\n";
        
        // Check if table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
        if ($stmt->rowCount() == 0) {
            echo "Categories table does not exist. Creating it now...\n";
            
            // Create categories table
            $sql = "CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            $pdo->exec($sql);
            echo "Categories table created successfully!\n";
            
            // Insert a test category
            $sql = "INSERT INTO categories (name, description) VALUES ('Test Category', 'This is a test category')";
            $pdo->exec($sql);
            echo "Test category added successfully!\n";
        } else {
            echo "Categories table exists but is empty.\n";
        }
    }
    
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
?> 