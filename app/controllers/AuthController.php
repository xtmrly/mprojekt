<?php

require __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];

    // Validace dat
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($passwordConfirm)) {
        die('Všechna pole jsou povinná.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Neplatná e-mailová adresa.');
    }

    if ($password !== $passwordConfirm) {
        die('Hesla se neshodují.');
    }

    if (strlen($password) < 6) {
        die('Heslo musí mít alespoň 6 znaků.');
    }

    // Kontrola, zda uživatel již existuje
    $query = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $query->execute([$email]);
    if ($query->rowCount() > 0) {
        die('Tento e-mail je již zaregistrován.');
    }

    // Hashování hesla
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Vložení uživatele do databáze
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);

    // Přesměrování na přihlašovací stránku
    header("Location: /auth/login");
    exit;
}
