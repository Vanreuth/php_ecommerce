<?php
require_once 'config/database.php';

class SliderController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function index() {
        try {
            $query = "SELECT * FROM sliders WHERE status = 1 ORDER BY id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $sliders = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $sliders[] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'image_path' => $row['image_path'],
                    'status' => $row['status']
                ];
            }
            
            return $sliders;
        } catch (PDOException $e) {
            error_log("Database Error in SliderController: " . $e->getMessage());
            throw new Exception("Error fetching sliders");
        }
    }
} 