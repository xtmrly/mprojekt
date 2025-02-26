<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/constants.php';

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($passwordConfirm)) {
        $error_message = 'Všechna pole jsou povinná.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Neplatná e-mailová adresa.';
    } elseif ($password !== $passwordConfirm) {
        $error_message = 'Hesla se neshodují.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Heslo musí mít alespoň 6 znaků.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);

            if ($stmt->rowCount() > 0) {
                $error_message = 'Uživatel s tímto e-mailem již existuje.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)");
                $stmt->execute([
                    ':first_name' => $firstName,
                    ':last_name' => $lastName,
                    ':email' => $email,
                    ':password' => $hashedPassword
                ]);

                header('Location: /mprojekt/app/views/auth/login');
                exit();
            }
        } catch (PDOException $e) {
            $error_message = 'Chyba při registraci: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrace</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
    <script>
        <?php if(!empty($error_message)): ?>
        window.onload = function() {
            alert("<?php echo addslashes($error_message); ?>");
        }
        <?php endif; ?>
    </script>
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <header>
        <h1>Registrace</h1>
    </header>
    <main>
        <form action="" method="POST">
            <label for="first_name">Jméno:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo isset($firstName) ? htmlspecialchars($firstName) : ''; ?>" required>

            <label for="last_name">Příjmení:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo isset($lastName) ? htmlspecialchars($lastName) : ''; ?>" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

            <label for="password">Heslo:</label>
            <input type="password" id="password" name="password" required minlength="6">

            <label for="password_confirm">Heslo znovu:</label>
            <input type="password" id="password_confirm" name="password_confirm" required minlength="6">

            <button type="submit">Registrovat se</button>
            <p>Už máte účet? <a href="/mprojekt/app/views/auth/login">Přihlaste se</a>.</p>
        </form>
    </main>
</body>
</html>