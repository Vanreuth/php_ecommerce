<?php
ob_start();
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Slider.php';

class SliderController {
    private $sliderModel;
    private $uploadDir;

    public function __construct() {
        $pdo = Database::connect();
        $this->sliderModel = new Slider($pdo);
        $this->uploadDir = __DIR__ . '/../uploads/sliders/';
        
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            try {

                switch ($action) {
                    case 'add':
                        $this->addSlider();
                        
                        break;
                    case 'update':
                        $this->updateSlider();
                      
                        break;
                    case 'delete':
                        $this->deleteSlider();
                        
                        break;
                    case 'toggle_status':
                        $this->toggleStatus();
                      
                        break;
                    default:
                        throw new Exception('Invalid action');
                }
                $_SESSION['success'] = 'Operation completed successfully';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            // Redirect back to the slider management page
            header('Location: /eccommerce/admin/?p=sliders');
            exit;
        }
    }

    private function validateSliderId($id) {
        if (!isset($id)) {
            throw new Exception('Slider ID is missing');
        }

        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id <= 0) {
            throw new Exception('Invalid slider ID format');
        }

        // Check if slider exists
        try {
            $slider = $this->sliderModel->getSliderById($id);
            if (!$slider) {
                throw new Exception('Slider not found');
            }
            return $id;
        } catch (Exception $e) {
            throw new Exception('Error validating slider: ' . $e->getMessage());
        }
    }

    private function addSlider() {
        // Validate inputs
        $title = $this->validateInput($_POST['title']);
        if (empty($title)) {
            throw new Exception('Title is required');
        }

        // Handle image upload
        $imagePath = $this->handleImageUpload($_FILES['image']);

        $data = [
            'title' => $title,
            'description' => $this->validateInput($_POST['description'] ?? ''),
            'image_path' => $imagePath,
            'status' => isset($_POST['status']) ? 1 : 0
        ];

        if (!$this->sliderModel->createSlider($data)) {
            throw new Exception('Failed to create slider');
        }
    }

    private function updateSlider() {
        // Validate slider ID and ensure it exists
        $id = $this->validateSliderId($_POST['id'] ?? null);

        // Validate title
        $title = $this->validateInput($_POST['title'] ?? '');
        if (empty($title)) {
            throw new Exception('Title is required');
        }

        $data = [
            'title' => $title,
            'description' => $this->validateInput($_POST['description'] ?? ''),
            'status' => isset($_POST['status']) ? 1 : 0
        ];

        // Handle image upload if new image is provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $data['image_path'] = $this->handleImageUpload($_FILES['image']);
                
                // Delete old image
                $existingSlider = $this->sliderModel->getSliderById($id);
                if ($existingSlider && !empty($existingSlider['image_path'])) {
                    $oldImagePath = $this->uploadDir . $existingSlider['image_path'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            } else {
                throw new Exception('Error uploading image');
            }
        }

        if (!$this->sliderModel->updateSlider($id, $data)) {
            throw new Exception('Failed to update slider');
        }
    }

    private function deleteSlider() {
        // Validate slider ID and ensure it exists
        $id = $this->validateSliderId($_POST['id'] ?? null);

        // Get slider before deletion to handle image
        $existingSlider = $this->sliderModel->getSliderById($id);
        
        // Delete image file first if it exists
        if ($existingSlider && !empty($existingSlider['image_path'])) {
            $imagePath = $this->uploadDir . $existingSlider['image_path'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if (!$this->sliderModel->deleteSlider($id)) {
            throw new Exception('Failed to delete slider');
        }
    }

    private function toggleStatus() {
        // Validate slider ID and ensure it exists
        $id = $this->validateSliderId($_POST['id'] ?? null);

        if (!$this->sliderModel->toggleStatus($id)) {
            throw new Exception('Failed to toggle slider status');
        }
    }

    private function handleImageUpload($file) {
        if (!isset($file) || !is_array($file)) {
            throw new Exception('No image file provided');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception('Image file is too large');
                case UPLOAD_ERR_PARTIAL:
                    throw new Exception('Image file was only partially uploaded');
                case UPLOAD_ERR_NO_FILE:
                    throw new Exception('No image file was uploaded');
                default:
                    throw new Exception('Image upload failed');
            }
        }

        // Validate file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new Exception('Image file size must be less than 2MB');
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid image type. Only JPG, PNG and GIF are allowed');
        }

        // Generate unique filename
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);
        $newFilename = uniqid('slider_', true) . '.' . $extension;
        $targetPath = $this->uploadDir . $newFilename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to save image file');
        }

        return $newFilename;
    }

    private function validateInput($input) {
        return trim(strip_tags($input));
    }
}

// Initialize controller and handle request
$controller = new SliderController();
$controller->handleRequest(); 