<?php
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con=new cnn;
$consulta=$con->consulta("SELECT archivos.nombre, archivos.id_archivo, menu.menu, submenu.submenu
FROM archivos
INNER JOIN menu ON archivos.id_menu = menu.id_menu
INNER JOIN submenu ON archivos.id_submenu = submenu.id_submenu"); 

?>
<!DOCTYPE html>
<?php include("../head.php");?>
<body>
<div class="mws-panel grid_8">
<div class="mws-panel-header"><span>
<table width="100%" border="0">
  <tbody>
     <td ><i class="icon-table"></i>  Administracion  archivos</td>
     <td align="center"><a href="javascript:agregar('archivo_agregar.php',500,425);" title="Agregar Nuevo"><i class="icon-plus"></i></a></td>
  </tbody>
       </table>
  </span> </div>
<div class="mws-panel-body no-padding">
<table class="mws-table mws-datatable">
<thead>
<tr>
<th>N°</th>
<th colspan="">memu</th>
<th colspan="">submenu</th>
<th colspan="">archivo</th>
 <th></th>
</tr>
</thead>

      <?php 
       while($ver=$con->arreglo($consulta)){ 
      ?> 
       <tr>
    <td><?php echo $ver["id_archivo"]; ?></td> 
       <td><?php echo $ver["menu"]; ?></td>  
        <td><?php echo $ver["submenu"]; ?></td>  
         <td><?php echo $ver["nombre"]; ?></td>  
           
      
      <td align="center"><a href="<?php echo "javascript:editar('editar.php?cod=$ver[id_archivo]',500,700);";?>" class="btn btn-small"><i class="icon-edit"></i></a>
      <a href="<?php echo "eliminar_borrar.php?cod=$ver[id_archivo]";?>" class="btn btn-small" ><i class="icon-trash"></i></a>
      </td>
      </tr>
     <?php }?>

</table>
</div></div>
<?php include("../footer.php"); ?>
</body>
</html>