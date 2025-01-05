<?php
$host = 'localhost';
$db   = 'app_db';
$user = 'root';
$pass = '';

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die($e->getMessage());
}

function isBlocked(PDO $pdo, string $ip): bool {
    $maxAttempts = 5;
    $timeWindow = 15;
    $s = $pdo->prepare(
        "SELECT COUNT(*) FROM login_attempts
         WHERE ip_address = :ip AND attempt_time > (NOW() - INTERVAL :tw MINUTE)"
    );
    $s->execute([':ip' => $ip, ':tw' => $timeWindow]);
    $c = $s->fetchColumn();
    return ($c >= $maxAttempts);
}

function recordLoginAttempt(PDO $pdo, string $ip) {
    $s = $pdo->prepare("INSERT INTO login_attempts (ip_address, attempt_time) VALUES (:ip, NOW())");
    $s->execute([':ip' => $ip]);
}
