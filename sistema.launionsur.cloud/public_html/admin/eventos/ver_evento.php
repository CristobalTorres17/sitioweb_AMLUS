<?php
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con = new cnn();
?>

<!DOCTYPE html>
<html>
<?php include("../head.php");?>
<title>Administracion de Eventos</title>
<body>
<div class="mws-panel grid_7 mws-collapsible">
  <div class="mws-panel-header"><span>
  <table width="100%" border="0">
  <tbody>
    <tr>
      <td><i class="icon-table"></i> Eventos</td>
      <td align="center"><a href="<?php echo "javascript:agregar('agregar_evento.php',1007,340)";?>">
      <i class="icon-plus"></i></a></td>
    </tr>
  </tbody>
</table>
</span> </div>
<div class="mws-panel-body no-padding">
<table class="mws-table mws-datatable">
<thead>
<tr>
<th>Nº de Eventos</th>
<th>Nombre</th>
<th>Tipo Evento</th>
<th>Fecha Evento</th>
<th></th>
</tr>
</thead>
<?php 
$consulta=$con->consulta("SELECT  id_evento, correlativo, fecha, tipo, nombre FROM eventos WHERE estado = 'Activo' AND eliminado = 0 ORDER BY fecha DESC");

while ($dato = $con->arreglo($consulta)) { ?> 
<tr>
<td><?php echo $dato["correlativo"]; ?></td>
<td><a href="<?php echo "javascript:editar('evento_galeria.php?cod=$dato[id_evento]&nombre=$dato[nombre]',729,668);";?>"><?php echo $dato["nombre"]; ?></a></td>
<td><?php echo $dato["tipo"]; ?></td>
<td><?php echo $dato["fecha"]; ?></td>
	
<td>
<a href="<?php echo "javascript:editar('aditar_evento.php?cod=$dato[id_evento]&nombre=$dato[nombre]',1007,385)";?>" class="btn btn-small"><i class="icon-edit"></i> Editar</a>

<a title="Eliminar evento" href="<?php echo "eliminar_evento.php?cod=$dato[id_evento]";?>" class="btn btn-small" onClick="return confirmar('Esta Seguro que desea Eliminar este Evento?')"><i class="icon-trash"></i></a>
</td>
</tr>  
<?php } ?>
</table>
</div>
</div>
<?php include("../footer.php"); ?>
</body>
</html> 