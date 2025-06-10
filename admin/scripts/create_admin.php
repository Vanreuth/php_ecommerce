<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

try {
    $pdo = Database::connect();
    $userModel = new User($pdo);

    // Check if admin user exists
    $admin = $userModel->getUserByEmail('admin@example.com');
    
    if (!$admin) {
        // Create admin user
        $data = [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'admin123', // You should change this password
            'phone' => '',
            'address' => '',
            'role' => 'admin',
            'status' => 'active'
        ];

        if ($userModel->addUser($data)) {
            echo "Admin user created successfully!\n";
            echo "Email: admin@example.com\n";
            echo "Password: admin123\n";
        } else {
            echo "Failed to create admin user.\n";
        }
    } else {
        echo "Admin user already exists.\n";
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?> 