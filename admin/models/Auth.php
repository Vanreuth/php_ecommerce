<?php
require_once '../config/database.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = $GLOBALS['pdo'];
    }
    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && $user['password'] == $password) {
            return $user;
        }

        return false; 
    }
    
}
?>