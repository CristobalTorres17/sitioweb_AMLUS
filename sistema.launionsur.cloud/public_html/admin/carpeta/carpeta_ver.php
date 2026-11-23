<?php
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$rol_actual = $_SESSION["rol"];
$con=new cnn;
$consulta=$con->consulta("SELECT * from carpeta,submenu where carpeta.id_submenu=submenu.id_submenu"); 
?>
<!doctype html>
<html>
<?php include("../head.php");?>
<head>
<meta charset="utf-8">
<title></title>
</head>
<body>

<div class="mws-panel grid_7 mws-collapsible">
<div class="mws-panel-header"><span>
<table width="100%" border="0">
  <tbody>
     <td><i class="icon-table"></i>  Administracion  del Portal de Transparencia</td>
     <td align="center"><a href="javascript:agregar('carpeta_agregar.php',600,260);" title="Agregar Nuevo"><i class="icon-plus"></i></a></td>
  </tbody>
       </table>
  </span> </div>
<div class="mws-panel-body no-padding">
<table class="mws-table mws-datatable">
<thead>
<tr>

<th colspan="">Nombre de la carpeta</th>
<th colspan="">pertenece a:</th>
 <th></th>
</tr>
</thead>
<img src="">
      <?php 
       while($ver=$con->arreglo($consulta)){ 
      ?> 
       <tr>
    
       <td width="40%"><?php echo $ver["carpeta"]; ?></td>  
        <td width="40%"><?php echo $ver["submenu"]; ?></td>  
           
      <td align="center"><a href="<?php echo "javascript:editar('carpeta_editar.php?cod=$ver[id_carpeta]',600,260);";?>" class="btn btn-small"><i class="icon-edit"></i></a>&nbsp;&nbsp;
      <a href="<?php echo "carpeta_borrar.php?cod=$ver[id_carpeta]";?>" class="btn btn-small" ><i class="icon-trash"></i></a></td>
     
      </td>
      </tr>
     <?php }?>

</table>
</div></div></div>
<?php include("../footer.php"); ?>
</body>
</html>