<?php
require_once 'config/databases.php';
class Slider {
    private $db;
    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAllSliders() {
        $sql = "SELECT * FROM sliders";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
} 
?>