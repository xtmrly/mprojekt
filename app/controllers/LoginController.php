<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['message'] = 'Vyplňte všechny požadované údaje.';
        header('Location: /mprojekt/public/auth/login');
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['message'] = 'Neplatný e-mail nebo heslo.';
            header('Location: /mprojekt/public/auth/login');
            exit();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'];
        $_SESSION['user_role'] = $user['role']; // Uložení role uživatele

        header('Location: /mprojekt/public/');
        exit();
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Chyba při přihlášení: ' . $e->getMessage();
        header('Location: /mprojekt/public/auth/login');
        exit();
    }
}
