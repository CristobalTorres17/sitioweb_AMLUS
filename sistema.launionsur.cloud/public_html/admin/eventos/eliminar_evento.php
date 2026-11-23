<?php
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con = new cnn();

$consultas = $con->consulta("UPDATE eventos SET eliminado=1 WHERE id_evento='$_GET[cod]'");
	
echo "
<script>
window.location.href='ver_evento.php';
</script>
";  

exit;
?>
