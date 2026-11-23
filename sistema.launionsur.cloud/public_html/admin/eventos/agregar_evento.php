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
$img         			= $_FILES['imagen']["name"];	
	
	 
if($img==""){	
	
$consultas = $con->consulta("INSERT INTO eventos
VALUES (null,'$_SESSION[id]','$correlativo','$fecha','$tipo','$onjetivo','$nombre','','Activo','0')");	
    
$consultaS = $con->consulta("SELECT id_evento, nombre FROM eventos ORDER BY id_evento DESC LIMIT 1");	
$datosS = $con->arreglo($consultaS);
$ulti=$datosS["id_evento"];
$nombre=$datosS["nombre"];	
	
echo"
<script>
window.open('galeria.php?id=$ulti&nombre=$nombre','ventana1','width=1254,height=200,scrollbar=NO');

</script>";	
echo "<script> window.opener.location.reload('ver_evento.php'); self.close(); </script>";	    

}else{
if( $_FILES['imagen']['type'] == "image/jpeg" OR $_FILES['imagen']['type'] == "image/jpg" OR $_FILES['imagen']['type'] == "image/png" ){	
  
$consultas = $con->consulta("INSERT INTO eventos
VALUES (null,'$_SESSION[id]','$correlativo','$fecha','$tipo','$onjetivo','$nombre','$img','Activo','0')");

	
move_uploaded_file($_FILES['imagen']['tmp_name'],"images/".$_FILES['imagen']['name']);	
	
$consultaS = $con->consulta("SELECT id_evento, nombre FROM eventos ORDER BY id_evento DESC LIMIT 1");	
$datosS = $con->arreglo($consultaS);
$ulti=$datosS["id_evento"];
$nombre=$datosS["nombre"];	
	
echo"
<script>
window.open('galeria.php?id=$ulti&nombre=$nombre','ventana1','width=1254,height=200,scrollbar=NO');

</script>";	
echo "<script> window.opener.location.reload('ver_evento.php'); self.close(); </script>";	
 
}
else{
echo "<script> alert('Error Formato no Valido, Agregue en Formato JPG o PNG.'); window.location.href='agregar_evento.php'; </script>";	
}	
	
}	

	
}
else{
?>
<!DOCTYPE html>
<html>
<?php include("../head.php");
	 
$consulta = $con->consulta("SELECT correlativo FROM eventos WHERE eventos.eliminado = 0 ORDER BY id_evento DESC" );
$datos = $con->arreglo($consulta);						   
$nuevoCo=$datos["correlativo"]+1;	
	?>
<body>
	
<div class="mws-panel grid_8">
  <div class="mws-panel-header"> <span><i class="icon-book"></i> Nuevo Evento </span> </div>
  <div class="mws-panel-body no-padding">
    <div class="mws-form-inline">
 <form action="" method="post" enctype="multipart/form-data" name="form1" class="form-horizontal" id="form1">
<table border="0" align="center" cellpadding="0" >
  <tr>
	 <td width="472" colspan="4" ><b>Nombre</b> <input name="nombre" type="text" required="required"  id="nombre" value="" size="105"  placeholder="Nombre...">
	  </td></tr>
	
	<tr>
	<td><b>Nº Correlativo:</b>
      <input name="correlativo" type="text"  id="correlativo" value="<?php  echo $nuevoCo; ?>" size="2" readonly> </td>	
		
		<td width="79" align="center" >
		<b>Fecha:  </b></td><td width="144">
      <input name='fecha' type="date" id='fecha' value="<?php echo date("Y-m-d");?>">
		</td>
		<td colspan="2"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tipo de evento: </b>
	      <select name="tipo" required class="mws-select2" id="tipo" style="width: 50%">
<option value="Seleccione..">Seleccione..</option>
<option value="Noticias">Noticias</option>
<option value="Eventos">Eventos</option>
<option value="Actos">Actos</option>
<option value="Publicaciones">Publicaciones</option>
	    </select>	
		
		</td></tr>
	<tr>
	<td valign="middle" colspan="4"><b>Objetivo:</b> <textarea name="objetivo" cols="110" rows="3" id="objetivo"></textarea></td>	
	</tr>
	<tr>
    <td colspan="4" align="center" ><b>Listado</b> <input name="imagen" type="file" id="imagen"></td>
  </tr>

  <tr>
    <td colspan="4" align="center">
 
	<button type="submit" class="btn btn-danger" name="agregar1" id="agregar1"> <i class="icol-disk"></i> Siguiente</button>


   </td>
  </tr>
</table>
</form>
	  </div>
	</div>
	<?php include("../footer.php"); ?>
	</div>
<?php } ?>