<?php
require_once __DIR__ . '/../config/database.php';
class Page {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getPage($title) {
        $stmt = $this->pdo->prepare("SELECT * FROM pages WHERE title = ?");
        $stmt->execute([$title]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePage($data) {
        $sql = "UPDATE pages SET 
                title = ?, subtitle1 = ?, description1 = ?, 
                subtitle2 = ?, description2 = ?, 
                image1 = ?, image2 = ?, 
                address = ?, phone = ?, email = ? 
                WHERE page_name = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'], $data['subtitle1'], $data['description1'],
            $data['subtitle2'], $data['description2'],
            $data['image1'], $data['image2'],
            $data['address'], $data['phone'], $data['email'],
            $data['page_name']
        ]);
    }
    
}


?>