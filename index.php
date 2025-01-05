<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="max-w-md w-full bg-white rounded shadow p-6">
  <div class="mt-4 space-x-2">
    <a href="todo.php" class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">
      To-Do List
    </a>
    <a href="logout.php" class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">
      Logout
    </a>
  </div>
</div>
</body>
</html>
