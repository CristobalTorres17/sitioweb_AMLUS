<?php
ini_set('default_charset','UTF8');
require_once("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con = new cnn();
$fecha = date("Y-m-d");
?>
<!DOCTYPE html>
<html>
<?php include("../head.php");?>
<body>
<div class="mws-panel grid_5 mws-collapsible">
<div class="mws-panel-header"> <span>
  <link rel="stylesheet" href="dist/css/lightbox.min.css">
<table width="100%" border="0">
  <tbody>
    <tr>
      <td><i class="icon-table"></i> Lista de imagenes</td>
      <td><a href="<?php echo "javascript:agregar('galeria2.php?id=$_GET[cod]&nombre=$_GET[nombre]',1254,200 )";?>"><li class="icon-plus"></li></a></td>
    </tr>
  </tbody>
</table>
</span> </div>
<div class="mws-panel-body no-padding">
<table class="mws-table mws-datatable">
<thead>
<tr>
<th>Imagenes</th>
<th></th>
</tr>
</thead>
<?php 	
$consulta=$con->consulta("SELECT
eventos.nombre,
galeria.imagen,
galeria.id_galeria
FROM
eventos
INNER JOIN galeria ON galeria.id_evento = eventos.id_evento
WHERE
galeria.elimindo = 0 AND
eventos.id_evento
= '$_GET[cod]'
");
 while ($dato = $con->arreglo($consulta)) { 
?>
<tr >
<td> <a class="example-image-link" href="images/<?php echo $dato["imagen"]; ?>" data-lightbox="example-2" data-title="<?php echo $dato["nombre"];?>"><img class="example-image" src="images/<?php echo $dato["imagen"]; ?>" width="50" height="50"></td>
<td>
<a href="<?php echo "borrar_img.php?id=$dato[id_galeria]&cod=$_GET[cod]";?>" class="btn btn-small" onClick="return confirmar('Esta Seguro que desea Eliminar esta Imagen?')"><i class="icon-trash"></i> Eliminar</a> 	
</td>
</tr>
<?php }  ?>
</table>
  <script src="dist/js/lightbox-plus-jquery.min.js"></script>
</div>
</div>
<?php include("../footer.php"); ?>
</body>
</html>

