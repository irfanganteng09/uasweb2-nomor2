<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $ip       = $_SERVER['REMOTE_ADDR'];

    if (isBlocked($pdo, $ip)) {
        $error = "Terlalu banyak percobaan login.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :u");
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['username']  = $row['username'];
            header('Location: index.php');
            exit;
        } else {
            recordLoginAttempt($pdo, $ip);
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="max-w-md w-full bg-white rounded shadow p-6">
    <h1 class="text-2xl font-semibold mb-4">Login</h1>
    <?php if ($error): ?>
    <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    <form method="post" class="space-y-4">
        <div>
            <label class="block mb-1 font-medium">Username</label>
            <input type="text" name="username" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
        </div>
        <div>
            <label class="block mb-1 font-medium">Password</label>
            <input type="password" name="password" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
        </div>
        <button type="submit" name="login"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Login
        </button>
    </form>
    <div class="mt-4 text-center">
        <a href="forgot_password.php" class="text-blue-600 hover:underline">Lupa password</a>
    </div>
</div>
</body>
</html>
