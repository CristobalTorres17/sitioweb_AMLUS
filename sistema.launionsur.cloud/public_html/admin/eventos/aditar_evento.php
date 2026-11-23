<?php
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con = new cnn();
if(isset($_POST["agregar1"]))
{
$nombre                   = $_POST["nombre"]; 
$correlativo              = $_POST["correlativo"]; 
$fecha                    = $_POST["fecha"]; 
$tipo                     = $_POST["tipo"];  
$onjetivo                 = $_POST["objetivo"];
$img                      = $_FILES['imagen']["name"];
	 
	
if($img==""){
		
$consulta = $con->consulta("UPDATE eventos SET fecha='$fecha', tipo='$tipo', objetivo='$onjetivo', nombre='$nombre' WHERE id_evento='$_GET[cod]'");
}
else{	

	

$consulta = $con->consulta("UPDATE eventos SET fecha='$fecha', tipo='$tipo', objetivo='$onjetivo', nombre='$nombre', listado='$img' WHERE id_evento='$_GET[cod]'");
	
move_uploaded_file($_FILES['imagen']['tmp_name'],"images/".$_FILES['imagen']['name']);	
 
	
	
}
echo "<script> window.opener.location.reload('ver_evento.php'); self.close(); </script>";	
}
else{
?>
<!DOCTYPE html>
<html>
<?php include("../head.php");
	 
$consulta = $con->consulta("SELECT correlativo, fecha, tipo, objetivo, nombre, listado FROM eventos WHERE eliminado = 0 AND id_evento='$_GET[cod]' " );
$datos = $con->arreglo($consulta);						   	
	?>

<body>
	
<div class="mws-panel grid_8">
  <div class="mws-panel-header"> <span><i class="icon-book"></i> Nuevo Evento </span> </div>
  <div class="mws-panel-body no-padding">
    <div class="mws-form-inline">
 <form action="" method="post" enctype="multipart/form-data" name="form1" class="form-horizontal" id="form1">
<table border="0" align="center" cellpadding="0" >
  <tr>
	 <td colspan="4" ><b>Nombre</b> <input name="nombre" type="text" required="required"  id="nombre" value="<?php  echo $datos["nombre"]; ?>" size="105" >
	  </td></tr>
	
	<tr>
	<td width="163"><b>Nº Correlativo:</b>
      <input name="correlativo" type="text"  id="correlativo" value="<?php  echo $datos["correlativo"]; ?>" size="2" readonly> </td>	
		
		<td width="130" align="center" >
		<b>Fecha:  </b></td><td width="194">
      <input name='fecha' type="date" id='fecha' value="<?php  echo $datos["fecha"]; ?>">
		</td>
		<td width="314" colspan="2"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tipo de evento: </b>
	  <select name="tipo" required class="mws-select2" id="tipo" style="width: 30%">
<option value="<?php  echo $datos["tipo"]; ?>"><?php  echo $datos["tipo"]; ?></option>
<option value="Noticias">Noticias</option>
<option value="Eventos">Eventos</option>
<option value="Actos">Actos</option>
<option value="Publicaciones">Publicaciones</option>
	    </select>	
		
		</td></tr>
	    
	<tr>
	<td valign="middle" colspan="4"><b>Objetivo:</b> <textarea name="objetivo" cols="110" rows="3" id="objetivo"><?php  echo $datos["objetivo"]; ?></textarea></td>	
	</tr>
	<tr>
    <td colspan="10" align="center"><b>Listado</b> <input name="imagen" type="file" id="imagen"></td>
  </tr>

  <tr>
    <td colspan="4" align="center">
 
	<button type="submit" class="btn btn-danger" name="agregar1" id="agregar1"> <i class="icol-disk"></i> Guardar Compra</button>


   </td>
  </tr>
</table>
</form>
	  </div>
	</div>
	<?php include("../footer.php"); ?>
	</div>
<?php } ?>