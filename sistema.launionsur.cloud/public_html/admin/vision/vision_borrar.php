<?php
ini_set('default_charset','UTF8');
require("../libreria_php/cnn_conexion.php");
require("../seguro.php");
$con=new cnn;
$db = new cnn();
$fecha = time();
$fecha_con_formato = date("d/m/Y --H:i:s",$fecha);
$cod_editar = $_GET["cod"];
$id= $_SESSION["id"];
$con=new cnn;


$up=$con->consulta("DELETE FROM vision WHERE id_vision='$_GET[cod]'");
echo "<script>
alert('Datos Eliminados Exitosamente');
window.location.href='vision_ver.php';
</script>";  

////////////////////////////////HISTORIAL ////////////////////////////////////////////////////
$coon = new cnn();
$consulta = $coon->consulta("SELECT * FROM adm_usuarios WHERE id='$id'");
$datos = $coon->arreglo($consulta);
$vari=$datos["usu"];
$con = new cnn();
$consulta = $con->consulta("SELECT * FROM  vision ORDER BY  vision.id_vision DESC LIMIT 1");
$datosS = $con->arreglo($consulta);
$ulti=$datosS["id_vision"];
$accion= "elimino Vision";
$lugar="Administarción de Vision";
$consultas = $db->consulta("INSERT INTO historial VALUES (null,'$vari','$fecha_con_formato','$accion','$lugar','$ulti','activo')");    



 ?>
