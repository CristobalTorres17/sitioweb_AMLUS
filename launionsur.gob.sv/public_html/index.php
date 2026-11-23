<?php $head_title="Inicio || La Unión Sur || Inicio PHP Template"?>
<?php
// Ubicado en: C:\wamp64\www\alcaldia-page\parts\home\blog.php
// Incluye la configuración de la conexión
require_once 'config/config.php';

// Consulta segura para obtener los 3 eventos más recientes
$stmt = $pdo->prepare("SELECT id_evento, nombre, fecha, tipo, listado FROM eventos ORDER BY fecha DESC LIMIT 3");
$stmt->execute();
$eventos = $stmt->fetchAll();
?>

<?php require_once('parts/layout/top-layout.php'); ?>
<?php require_once('parts/header/header.php'); ?>

<?php require_once('parts/home/banner.php'); ?>
<?php require_once('parts/home/service.php'); ?>
<?php require_once('parts/home/about.php'); ?>
<?php require_once('parts/home/service2.php'); ?>

<?php require_once('parts/home/video.php'); ?>
<?php require_once('parts/home/counter.php'); ?>
<?php require_once('parts/home/project.php'); ?>
<?php require_once('parts/home/pricing.php'); ?>
<?php require_once('parts/home/blog.php'); ?>
<?php require_once('parts/home/cta.php'); ?>

<?php require_once('parts/footer/footer.php'); ?>
<?php require_once('parts/layout/bottom-layout.php'); ?>
