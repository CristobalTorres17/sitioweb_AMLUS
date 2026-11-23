<?php 
require("seguro.php");
require("libreria_php/cnn_conexion.php");
$db = new cnn();
$fecha_con_formato = date("Y-m-d");
$accion= "Fin de la Seccion";
$lugar="Administarción del Sistema";

$consulta = $db->consulta("INSERT INTO historial
VALUES (null,'$_SESSION[id]','$fecha_con_formato','$accion','$lugar',null,'Activo')");
					  
session_start();
session_destroy();
header("location: https://www.alcaldialaunion.gob.sv");
exit;
?>
