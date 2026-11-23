<?php 
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con=new cnn;
$db = new cnn();
$fecha = time();
$fecha_con_formato = date("d/m/Y --H:i:s",$fecha);
$id= $_SESSION["id"];
$cod_editar = $_GET["cod"];
$publicador="Alcaldia Municipal de La Union";
$var1= $fecha_con_formato;
$var2= $fecha_con_formato;
$fe = substr($var1, 0, 10);  // devuelve "fecha"
$hora = substr($var2,13,10); // devuelve "hora"




$con = new cnn();
$consulta = $con->consulta("SELECT * FROM noticias WHERE id_noti='$cod_editar'");
$dato = $con->arreglo($consulta);
?>
<!DOCTYPE html>
<html>
<?php include("../head.php"); ?>
<body>
<div class="mws-panel grid_5">
  <div class="mws-panel-header"> <span><i class="icon-pencil"></i> ingresar imagen</span> </div>
  <div class="mws-panel-body no-padding">
    <div class="mws-form-inline">
<form id="form1" name="form1" method="post" enctype="multipart/form-data">
  <table width="343" border="0" align="center">
    <tbody>
      <tr>
       <td width="54" height="34"><b> titulo:</b></td>
       <td><input name="titulo" value="<?php echo $dato['titulo']; ?>" type="text" id="titulo" size="35" />

       </td>

       <tr>
         <td width="54" height="34"><b> Encabezado:</b></td>
         <td> <textarea name="encabezado"  id="encabezado" cols="30" rows="5"><?php echo $dato['encabezado']; ?></textarea></td>

         <td width="54" height="34"><b> Detalle:</b></td>
         <td><textarea name="detalle" id="detalle" cols="30" rows="5"><?php echo $dato['detalle']; ?></textarea></td>
       </tr>
       <tr>
        <td width="54" height="34"><b> imagen:</b></td>
         <td colspan="2"><input type="file" name="filFotoPortada" id="filFotoPortada"/></td>
         <td align="center"><img src="images/<?php echo $dato['imagen']; ?>" width="357" height="231" /></td>
       </tr>
        <td colspan="4" align="center"><input type="submit" name="boton" id="boton" value="Guardar Datos" class="btn btn-danger"></td>
      </tr>
    </tbody>
  </table>
  <?php include("../footer.php"); ?>
</form>
</div>
  </div>
</div>
</body>
</html>
<?php //}?>


