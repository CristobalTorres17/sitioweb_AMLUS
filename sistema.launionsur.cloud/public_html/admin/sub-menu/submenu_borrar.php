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


$up=$con->consulta("DELETE FROM submenu WHERE id_submenu='$_GET[cod]'");
echo "<script>
alert('Datos Eliminados Exitosamente');
window.location.href='submenu_ver.php';
</script>";  

////////////////////////////////HISTORIAL ////////////////////////////////////////////////////
$coon = new cnn();
$consulta = $coon->consulta("SELECT * FROM adm_usuarios WHERE id='$id'");
$datos = $coon->arreglo($consulta);
$vari=$datos["usu"];
$con = new cnn();
$consulta = $con->consulta("SELECT * FROM  submenu WHERE id_submenu='$_GET[cod]'");
$datosS = $con->arreglo($consulta);
$ulti=$datosS["id_submenu"];
$accion= "elimino submenu";
$lugar="Administarción de submenu";
$consultas = $db->consulta("INSERT INTO historial VALUES (null,'$vari','$fecha_con_formato','$accion','$lugar','$ulti','activo')");    



 ?>



