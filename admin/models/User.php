<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register($name, $email, $phone, $address, $password) {
        // Check if email already exists
        if ($this->getUserByEmail($email)) {
            return false;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, phone, address, password, role) VALUES (?, ?, ?, ?, ?, 'user')");
        return $stmt->execute([$name, $email, $phone, $address, $hashedPassword]);
    }

    public function login($email, $password) {
        $user = $this->getUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // Don't store password in session
            return $user;
        }
        return false;
    }

    public function addUser($data) {
        try {
            $this->pdo->beginTransaction();

            // Check if email already exists
            if ($this->getUserByEmail($data['email'])) {
                throw new Exception("Email already exists");
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO users (name, email, password, phone, address, role, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");

            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $result = $stmt->execute([
                $data['name'],
                $data['email'],
                $hashedPassword,
                $data['phone'] ?? '',
                $data['address'] ?? '',
                $data['role'] ?? 'user',
                $data['status'] ?? 'active'
            ]);

            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function updateUser($id, $data) {
        try {
            // Check if email already exists for other users
            $existingUser = $this->getUserByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                throw new Exception("Email already exists");
            }

            $sql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, role = ?, status = ?";
            $params = [
                $data['name'],
                $data['email'],
                $data['phone'] ?? '',
                $data['address'] ?? '',
                $data['role'],
                $data['status']
            ];

            // Only update password if it's provided
            if (!empty($data['password'])) {
                $sql .= ", password = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id = ?";
            $params[] = $id;

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deleteUser($id) {
        try {
            // Check if user exists
            $user = $this->getUserById($id);
            if (!$user) {
                throw new Exception("User not found");
            }

            // Don't allow deleting the last admin
            if ($user['role'] === 'admin' && $this->getAdminCount() <= 1) {
                throw new Exception("Cannot delete the last admin user");
            }

            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function toggleStatus($id) {
        try {
            // Get current status
            $stmt = $this->pdo->prepare("SELECT status, role FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("User not found");
            }

            // Don't allow deactivating the last active admin
            if ($user['role'] === 'admin' && $user['status'] === 'active' && $this->getActiveAdminCount() <= 1) {
                throw new Exception("Cannot deactivate the last active admin");
            }

            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET status = CASE 
                    WHEN status = 'active' THEN 'inactive' 
                    ELSE 'active' 
                END 
                WHERE id = ?
            ");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function searchUsers($query) {
        $stmt = $this->pdo->prepare("
            SELECT id, name, email, phone, address, role, status, created_at
            FROM users 
            WHERE name LIKE ? OR email LIKE ?
            ORDER BY created_at DESC
        ");
        $searchTerm = "%{$query}%";
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsersByRole($role) {
        $stmt = $this->pdo->prepare("
            SELECT id, name, email, phone, address, role, status, created_at
            FROM users 
            WHERE role = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveUsers() {
        $stmt = $this->pdo->query("
            SELECT id, name, email, phone, address, role, status, created_at
            FROM users 
            WHERE status = 'active'
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAdminCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
        return $stmt->fetchColumn();
    }

    private function getActiveAdminCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND status = 'active'");
        return $stmt->fetchColumn();
    }
}
?>
