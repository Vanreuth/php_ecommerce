<?php
require_once 'config/databases.php';

class PageController {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getPage($pageName) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM pages WHERE page_name = ? LIMIT 1");
            $stmt->execute([$pageName]);
            $page = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$page) {
                return [
                    'title' => 'Page Not Found',
                    'subtitle1' => '',
                    'description1' => '',
                    'subtitle2' => '',
                    'description2' => '',
                    'image1' => 'default.jpg',
                    'image2' => 'default.jpg',
                    'banner_image' => 'default-banner.jpg',
                    'address' => '',
                    'phone' => '',
                    'email' => ''
                ];
            }

            return [
                'title' => $page['title'] ?? '',
                'subtitle1' => $page['subtitle1'] ?? '',
                'description1' => $page['description1'] ?? '',
                'subtitle2' => $page['subtitle2'] ?? '',
                'description2' => $page['description2'] ?? '',
                'image1' => $page['image1'] ?? 'default.jpg',
                'image2' => $page['image2'] ?? 'default.jpg',
                'banner_image' => $page['banner_image'] ?? 'default-banner.jpg',
                'address' => $page['address'] ?? '',
                'phone' => $page['phone'] ?? '',
                'email' => $page['email'] ?? ''
            ];
        } catch (PDOException $e) {
            error_log("Error fetching page content: " . $e->getMessage());
            return [
                'title' => 'Error Loading Page',
                'subtitle1' => '',
                'description1' => 'Sorry, there was an error loading this page.',
                'subtitle2' => '',
                'description2' => '',
                'image1' => 'default.jpg',
                'image2' => 'default.jpg',
                'banner_image' => 'default-banner.jpg',
                'address' => '',
                'phone' => '',
                'email' => ''
            ];
        }
    }
} 