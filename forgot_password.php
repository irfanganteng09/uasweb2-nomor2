<?php
session_start();
require_once 'config.php';

$msg = '';
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :e");
    $stmt->execute([':e' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expire = date('Y-m-d H:i:s', time() + 3600);
        $ins = $pdo->prepare(
            "INSERT INTO password_resets (user_id, token, expire_time)
             VALUES (:u, :t, :e)"
        );
        $ins->execute([':u' => $user['id'], ':t' => $token, ':e' => $expire]);
        $link = "http://localhost/reset_password.php?token=".$token;
        $msg = "Silakan cek email Anda. Link reset: ".$link;
    } else {
        $msg = "Email tidak terdaftar.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="max-w-md w-full bg-white rounded shadow p-6">
    <h1 class="text-2xl font-semibold mb-4">Lupa Password</h1>
    <?php if ($msg): ?>
    <div class="bg-blue-100 text-blue-700 p-2 rounded mb-4">
        <?= htmlspecialchars($msg) ?>
    </div>
    <?php endif; ?>
    <form method="post" class="space-y-4">
        <div>
            <label class="block mb-1 font-medium">Email</label>
            <input type="email" name="email" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
        </div>
        <button type="submit" name="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Kirim
        </button>
    </form>
</div>
</body>
</html>
