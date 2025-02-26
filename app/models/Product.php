<?php
require_once __DIR__ . '/../../config/database.php';

class Product {
    public static function getAllProducts() {
        global $pdo;
        try {
            $stmt = $pdo->query("SELECT * FROM products");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error getting all products: ' . $e->getMessage());
            return [];
        }
    }

    public static function getProductById($id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                error_log("No product found with ID: $id");
            }
            
            return $product;
        } catch (PDOException $e) {
            error_log('Error getting product by ID: ' . $e->getMessage());
            return false;
        }
    }
}