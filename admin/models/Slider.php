<?php
require_once __DIR__ . '/../config/database.php';


class Slider {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    private function validateSliderId($id) {
        if (!$id || !is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid slider ID");
        }

        $sql = "SELECT COUNT(*) FROM sliders WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $count = $stmt->fetchColumn();

        if ($count === 0) {
            throw new Exception("Slider not found");
        }

        return true;
    }

    public function createSlider($data) {
        try {
            if (empty($data['title'])) {
                throw new Exception('Title is required');
            }

            if (empty($data['image_path'])) {
                throw new Exception('Image path is required');
            }

            $sql = "INSERT INTO sliders (title, description, image_path, status, created_at, updated_at) 
                    VALUES (:title, :description, :image_path, :status, NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':image_path' => $data['image_path'],
                ':status' => $data['status'] ?? 1
            ]);

            if (!$result) {
                throw new PDOException("Failed to create slider");
            }

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating slider: " . $e->getMessage());
            throw new Exception("Failed to create slider: " . $e->getMessage());
        }
    }

    public function getAllSliders() {
        try {
            $sql = "SELECT id, title, description, image_path, status, created_at, updated_at 
                    FROM sliders 
                    ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching sliders: " . $e->getMessage());
            throw new Exception("Failed to fetch sliders: " . $e->getMessage());
        }
    }

    public function getActiveSliders() {
        try {
            $sql = "SELECT id, title, description, image_path, status, created_at, updated_at 
                    FROM sliders 
                    WHERE status = 1 
                    ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching active sliders: " . $e->getMessage());
            throw new Exception("Failed to fetch active sliders: " . $e->getMessage());
        }
    }

    public function getSliderById($id) {
        try {
            if (!$id || !is_numeric($id) || $id <= 0) {
                throw new Exception("Invalid slider ID");
            }

            $sql = "SELECT id, title, description, image_path, status, created_at, updated_at 
                    FROM sliders 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                throw new Exception("Slider not found");
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error fetching slider: " . $e->getMessage());
            throw new Exception("Failed to fetch slider: " . $e->getMessage());
        }
    }

    public function updateSlider($id, $data) {
        try {
            // Validate ID
            $this->validateSliderId($id);

            // Validate required fields
            if (isset($data['title']) && empty($data['title'])) {
                throw new Exception("Title cannot be empty");
            }

            $fields = [];
            $params = [':id' => $id];

            // Build update fields
            if (isset($data['title'])) {
                $fields[] = "title = :title";
                $params[':title'] = $data['title'];
            }
            if (isset($data['description'])) {
                $fields[] = "description = :description";
                $params[':description'] = $data['description'];
            }
            if (isset($data['status'])) {
                $fields[] = "status = :status";
                $params[':status'] = $data['status'];
            }
            if (isset($data['image_path'])) {
                $fields[] = "image_path = :image_path";
                $params[':image_path'] = $data['image_path'];
            }

            if (empty($fields)) {
                throw new Exception("No fields to update");
            }

            // Always update updated_at timestamp
            $fields[] = "updated_at = NOW()";

            $sql = "UPDATE sliders SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);

            if (!$result) {
                throw new PDOException("Failed to update slider");
            }

            // Verify the update
            $affected = $stmt->rowCount();
            if ($affected === 0) {
                throw new Exception("No changes were made to the slider");
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error updating slider: " . $e->getMessage());
            throw new Exception("Failed to update slider: " . $e->getMessage());
        }
    }

    public function deleteSlider($id) {
        try {
            // Validate ID
            $this->validateSliderId($id);

            $sql = "DELETE FROM sliders WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([':id' => $id]);

            if (!$result) {
                throw new PDOException("Failed to delete slider");
            }

            // Verify the deletion
            $affected = $stmt->rowCount();
            if ($affected === 0) {
                throw new Exception("Slider could not be deleted");
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error deleting slider: " . $e->getMessage());
            throw new Exception("Failed to delete slider: " . $e->getMessage());
        }
    }

    public function toggleStatus($id) {
        try {
            // Validate ID
            $this->validateSliderId($id);

            $sql = "UPDATE sliders SET status = NOT status, updated_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([':id' => $id]);

            if (!$result) {
                throw new PDOException("Failed to toggle slider status");
            }

            // Verify the update
            $affected = $stmt->rowCount();
            if ($affected === 0) {
                throw new Exception("Failed to toggle slider status");
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error toggling slider status: " . $e->getMessage());
            throw new Exception("Failed to toggle slider status: " . $e->getMessage());
        }
    }
} 