<?php
require_once __DIR__ . '/../models/Product.php';

class AdminController {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }        
        
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = 'Nemáte oprávnění pro přístup do admin panelu.';
            header('Location: /mprojekt/public/');
            exit();
        }
    }

    public function index() {
        require __DIR__ . '/../views/admin/index.php';
    }

    public function createProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $price = (float)$_POST['price'];
            $category = trim($_POST['category']);
            $image = $_FILES['image'];

            if (empty($name) || empty($description) || $price <= 0 || empty($category) || empty($image)) {
                $_SESSION['message'] = 'Vyplňte všechna pole.';
                header('Location: /mprojekt/public/admin');
                exit();
            }

            $imagePath = '/mprojekt/public/assets/images/' . basename($image['name']);
            move_uploaded_file($image['tmp_name'], __DIR__ . '/../../public/assets/images/' . basename($image['name']));

            Product::createProduct($name, $description, $price, $category, $imagePath);

            $_SESSION['message'] = 'Produkt byl úspěšně přidán!';
            header('Location: /mprojekt/public/admin');
            exit();
        }
    }

    public function delete() {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['message'] = 'Neplatné ID produktu.';
            header('Location: /mprojekt/public/admin');
            exit();
        }
    
        $productId = (int)$_GET['id'];
    
        global $pdo;
        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute([':id' => $productId]);
    
            $_SESSION['message'] = 'Produkt byl úspěšně smazán.';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Chyba při mazání produktu: ' . $e->getMessage();
        }
    
        header('Location: /mprojekt/public/admin');
        exit();
    }
    

    public function edit() {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['message'] = 'Neplatné ID produktu.';
            header('Location: /mprojekt/public/admin');
            exit();
        }
    
        $productId = (int)$_GET['id'];
        global $pdo;
    
        // Načtení informací o produktu
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$product) {
            $_SESSION['message'] = 'Produkt nebyl nalezen.';
            header('Location: /mprojekt/public/admin');
            exit();
        }
    
        require __DIR__ . '/../views/admin/edit.php';
    }

    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mprojekt/public/admin');
            exit();
        }
    
        $productId = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $category = trim($_POST['category']);
    
        global $pdo;
    
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = :name, description = :description, price = :price, category = :category WHERE id = :id");
            $stmt->execute([
                ':id' => $productId,
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':category' => $category
            ]);
    
            $_SESSION['message'] = 'Produkt byl úspěšně upraven.';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Chyba při úpravě produktu: ' . $e->getMessage();
        }
    
        header('Location: /mprojekt/public/admin');
        exit();
    }
    
}
