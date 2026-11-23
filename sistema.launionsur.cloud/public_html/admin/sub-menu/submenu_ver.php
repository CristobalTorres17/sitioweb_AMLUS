<?php
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$rol_actual = $_SESSION["rol"];
$con=new cnn;
$consulta=$con->consulta("SELECT * from submenu, menu where submenu.id_menu=menu.id_menu"); 
?>
<!DOCTYPE html>
<html>
<?php include("../head.php");?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
</head>
<body>

<div class="mws-panel grid_8">
<div class="mws-panel-header"><span>
<table width="100%" border="0">
  <tr>
     <td ><i class="icon-table"></i>  Administracion  del Portal de Transparencia</td>
     <td align="center"><a href="javascript:agregar('submenu_agregar.php',500,270);" title="Agregar Nuevo"><i class="icon-plus"></i></a></td>
  </tr>
       </table>
  </span> </div>
<div class="mws-panel-body no-padding">
<table class="mws-table mws-datatable">
<thead>
<tr>
<th colspan="">Nombre del submenu</th>
<th colspan="">pertenece a</th>
 <th></th>
</tr>
</thead>
      <?php 
       while($ver=$con->arreglo($consulta)){ 
      ?> 
       <tr>
    
       <td width="50%"><?php echo $ver["submenu"]; ?></td>
       <td width="35%"><?php echo $ver["menu"]; ?></td>  
           
      <td align="center"><a href="<?php echo "javascript:editar('submenu_editar.php?cod=$ver[id_submenu]',600,270);";?>" class="btn btn-small"><i class="icon-edit"></i></a>&nbsp;&nbsp;
      <a href="<?php echo "submenu_borrar.php?cod=$ver[id_submenu]";?>" class="btn btn-small" ><i class="icon-trash"></i></a></td>
     
      </td>
      </tr>
     <?php }?>

</table>
</div>
</div>
</div>
<?php include("../footer.php"); ?>
</body>
</html>