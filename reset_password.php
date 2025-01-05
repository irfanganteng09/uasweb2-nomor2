<?php
session_start();
require_once 'config.php';

$token = isset($_GET['token']) ? $_GET['token'] : null;
if (!$token) {
    die("Token tidak valid.");
}
$stmt = $pdo->prepare("SELECT user_id, expire_time FROM password_resets WHERE token = :t");
$stmt->execute([':t' => $token]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) {
    die("Token tidak ditemukan.");
}
if (time() > strtotime($data['expire_time'])) {
    die("Token kedaluwarsa.");
}

$msg = '';
if (isset($_POST['reset'])) {
    $np = $_POST['new_password'];
    $cp = $_POST['confirm_password'];
    if ($np === $cp) {
        $hp = password_hash($np, PASSWORD_BCRYPT);
        $u  = $pdo->prepare("UPDATE users SET password = :p WHERE id = :i");
        $u->execute([':p' => $hp, ':i' => $data['user_id']]);
        $d  = $pdo->prepare("DELETE FROM password_resets WHERE token = :t");
        $d->execute([':t' => $token]);
        $msg = "Password berhasil diubah.";
    } else {
        $msg = "Konfirmasi password tidak sama.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="max-w-md w-full bg-white rounded shadow p-6">
    <h1 class="text-2xl font-semibold mb-4">Reset Password</h1>
    <?php if ($msg): ?>
    <div class="bg-blue-100 text-blue-700 p-2 rounded mb-4">
        <?= htmlspecialchars($msg) ?>
    </div>
    <?php endif; ?>
    <form method="post" class="space-y-4">
        <div>
            <label class="block mb-1 font-medium">Password Baru</label>
            <input type="password" name="new_password" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
        </div>
        <div>
            <label class="block mb-1 font-medium">Konfirmasi Password</label>
            <input type="password" name="confirm_password" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
        </div>
        <button type="submit" name="reset"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Reset
        </button>
    </form>
</div>
</body>
</html>
