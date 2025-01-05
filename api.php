<?php
require_once 'config.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts  = explode('/', trim($uri, '/'));

if (count($parts) >= 2 && $parts[0] === 'api.php' && $parts[1] === 'todos') {
    if ($method === 'GET') {
        if (isset($parts[2])) {
            $id = (int)$parts[2];
            $s  = $pdo->prepare("SELECT * FROM todo_list WHERE id = :id");
            $s->execute([':id' => $id]);
            $data = $s->fetch(PDO::FETCH_ASSOC);
            echo json_encode($data ? $data : []);
        } else {
            $s = $pdo->query("SELECT * FROM todo_list ORDER BY id DESC");
            echo json_encode($s->fetchAll(PDO::FETCH_ASSOC));
        }
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $t     = isset($input['title']) ? $input['title'] : '';
        $now   = date('Y-m-d H:i:s');
        $q     = $pdo->prepare("INSERT INTO todo_list (title, is_done, created_at, updated_at) VALUES (:t, 0, :c, :u)");
        $q->execute([':t' => $t, ':c' => $now, ':u' => $now]);
        echo json_encode(['status' => 'created']);
    } elseif ($method === 'PUT') {
        if (isset($parts[2])) {
            $id    = (int)$parts[2];
            $input = json_decode(file_get_contents('php://input'), true);
            $t     = isset($input['title']) ? $input['title'] : '';
            $d     = isset($input['is_done']) ? (int)$input['is_done'] : 0;
            $now   = date('Y-m-d H:i:s');
            $q     = $pdo->prepare("UPDATE todo_list SET title = :t, is_done = :d, updated_at = :u WHERE id = :id");
            $q->execute([':t' => $t, ':d' => $d, ':u' => $now, ':id' => $id]);
            echo json_encode(['status' => 'updated']);
        }
    } elseif ($method === 'DELETE') {
        if (isset($parts[2])) {
            $id = (int)$parts[2];
            $q  = $pdo->prepare("DELETE FROM todo_list WHERE id = :id");
            $q->execute([':id' => $id]);
            echo json_encode(['status' => 'deleted']);
        }
    }
}
?>
