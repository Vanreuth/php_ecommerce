<?php
class PageController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch page content based on page_name ('About' or 'Contact')
    public function getPage($pageName) {
        $stmt = $this->pdo->prepare("SELECT * FROM pages WHERE page_name = :page_name LIMIT 1");
        $stmt->bindParam(":page_name", $pageName);
        $stmt->execute();
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Set default values to prevent errors
        return [
            'title' => $page['title'] ?? '',
            'subtitle1' => $page['subtitle1'] ?? '',
            'description1' => $page['description1'] ?? '',
            'subtitle2' => $page['subtitle2'] ?? '',
            'description2' => $page['description2'] ?? '',
            'image1' => $page['image1'] ?? '',
            'image2' => $page['image2'] ?? '',
            'banner_image' => $page['banner_image'] ?? '',
            'address' => $page['address'] ?? '',  
            'phone' => $page['phone'] ?? '',
            'email' => $page['email'] ?? ''
        ];
    }
    

    // Update page content
    public function updatePage($data) {
        if ($data['page_name'] === 'About') {
            $stmt = $this->pdo->prepare("
                UPDATE pages SET 
                    title = :title,
                    subtitle1 = :subtitle1, description1 = :description1,
                    subtitle2 = :subtitle2, description2 = :description2,
                    image1 = :image1, image2 = :image2,
                    banner_image = :banner_image
                WHERE page_name = :page_name
            ");
    
            return $stmt->execute([
                ':title' => $data['title'],
                ':subtitle1' => $data['subtitle1'],
                ':description1' => $data['description1'],
                ':subtitle2' => $data['subtitle2'],
                ':description2' => $data['description2'],
                ':image1' => $data['image1'],
                ':image2' => $data['image2'],
                ':banner_image' => $data['banner_image'],
                ':page_name' => $data['page_name']
            ]);
    
        } elseif ($data['page_name'] === 'Contact') {
            $stmt = $this->pdo->prepare("
                UPDATE pages SET 
                    title = :title,
                    address = :address, phone = :phone, email = :email,
                    image1 = :image1, banner_image = :banner_image
                WHERE page_name = :page_name
            ");
    
            return $stmt->execute([
                ':title' => $data['title'],
                ':address' => $data['address'],
                ':phone' => $data['phone'],
                ':email' => $data['email'],
                ':image1' => $data['image1'],
                ':banner_image' => $data['banner_image'],
                ':page_name' => $data['page_name']
            ]);
        }
    }
    
    
    public function getAllPages() {
        $stmt = $this->pdo->prepare("SELECT page_name, title FROM pages");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
