<?php
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con=new cnn;

$consulta=$con->consulta("SELECT * FROM vision");
?>
<!DOCTYPE html>
<html>
<?php include("../head.php");?>
<body>
<div class="mws-panel grid_7">
<div class="mws-panel-header"><span><i class="icon-table"></i>  Administrar Vision</span> </div>
<div class="mws-panel-body no-padding">
<table class="mws-table mws-datatable">
<thead>
<tr>
<th>Vision</th>
<th></th>
</tr>
</thead>
<?php while($ver=$con->arreglo($consulta)){ ?> 
<tr>
<td><?php echo $ver["vision"]; ?></td>     
<td align="center"><a href="<?php echo "javascript:editar('vision_editar.php?cod=$ver[id_vision]',700,300);";?>" class="btn btn-small"><i class="icon-edit"></i> Editar</a>&nbsp;&nbsp;
<a href="<?php echo "vision_borrar.php?cod=$ver[id_vision]";?>" class="btn btn-small" ><i class="icon-trash"></i> Eliminar</a></td>
</td>
</tr>
<?php }?>   
</table>
</div></div></div>
<?php include("../footer.php"); ?>
</body>
</html>