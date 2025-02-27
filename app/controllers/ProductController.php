<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController {
    public function index() {
        $products = Product::getAllProducts();
        require __DIR__ . '/../views/products/index.php';
    }

    public function show($id) {
        $product = Product::getProductById($id);
        if (!$product) {
            header('Location: /mprojekt/public/products');
            exit();
        }
        require __DIR__ . '/../views/products/show.php';
    }

    public function search() {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        if ($query === '') {
            $products = [];
        } else {
            $products = Product::search($query);
        }
        require __DIR__ . '/../views/products/search.php';
    }
}
