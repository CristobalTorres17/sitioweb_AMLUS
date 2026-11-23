<?php 
// search-event.php
require_once 'config/config.php';

$query = $_GET['query'] ?? '';
$query = trim($query);

if(strlen($query) < 3) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT id_evento, nombre FROM eventos WHERE nombre LIKE :query ORDER BY fecha DESC LIMIT 3");
$stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
