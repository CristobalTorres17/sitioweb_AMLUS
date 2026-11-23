<?php
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con = new cnn();

$consultas = $con->consulta("UPDATE galeria SET elimindo=1 WHERE id_galeria='$_GET[id]'");
	
header("Location:evento_galeria.php?cod=$_GET[cod]");

exit;
?>
