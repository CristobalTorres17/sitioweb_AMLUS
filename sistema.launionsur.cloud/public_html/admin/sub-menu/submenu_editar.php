<?php 
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con=new cnn;
$db = new cnn();
$fecha = time();
$fecha_con_formato = date("d/m/Y --H:i:s",$fecha);
$id= $_SESSION["id"];
$publicador="Alcaldia Municipal de La Union";
$var1= $fecha_con_formato;
$var2= $fecha_con_formato;
$cod_editar = $_GET["cod"];
$fe = substr($var1, 0, 10);  // devuelve "fecha"
$hora = substr($var2,13,10); // devuelve "hora"
if(isset($_POST["boton"])){
  
$menu = $_POST["menu"];
$submenu = $_POST["submenu"];
$consulta = $db->consulta("UPDATE submenu SET id_menu='$menu',submenu='$submenu' WHERE id_submenu='$cod_editar'");

////////////////////////////////HISTORIAL ////////////////////////////////////////////////////
$coon = new cnn();
$consulta = $coon->consulta("SELECT * FROM adm_usuarios WHERE id='$id'");
$datos = $coon->arreglo($consulta);
$vari=$datos["usu"];
$con = new cnn();
$consulta = $con->consulta("SELECT * FROM  submenu WHERE id_submenu = '$cod_editar' ");
$datosS = $con->arreglo($consulta);
$ulti=$datosS["id_submenu"];
$accion= "edito submenu";
$lugar="Administarción de submenu";
$consultas = $db->consulta("INSERT INTO historial VALUES (null,'$vari','$fecha_con_formato','$accion','$lugar','$ulti','activo')");    



  echo"
  <script>
  alert ('submenu editado Exitosamente');
   window.opener.location.reload('submenu_ver.php'); self.close();

  </script>
  ";
}else{
$consulta = $con->consulta("SELECT * FROM menu,submenu WHERE id_submenu='$cod_editar'");
$dato = $con->arreglo($consulta); 
?>
<!DOCTYPE html>
<html>
<?php include("../head.php"); ?>
<body>
<div class="mws-panel grid_5">
  <div class="mws-panel-header"> <span><i class="icon-pencil"></i> modificar submenu</span> </div>
  <div class="mws-panel-body no-padding">
    <div class="mws-form-inline">
<form id="form1" name="form1" method="post">
  <table width="343" border="0" align="center">
    <tbody>
      <tr>
       <td width="54" height="34"><b> menu:</b></td>
        <?php
        $con = new cnn();
        $consulta = $con->consulta("SELECT * FROM menu");
        ?>
        <td width="">
        <select name="menu" width="25">
         <?php while($dato2 = $con->arreglo($consulta)){ ?>
           <option value="<?php echo $dato2["id_menu"];?>" <?php if($dato2["id_menu"]==$dato["id_menu"]){echo "selected='selected'";}else{echo "";}?>><?php echo $dato2["menu"];?></option>
          <?php }?>
        </select>
        </td>

       </tr>
       <tr>
        <td width="54" height="34"><b> nombre submenu:</b></td>
         <td><input type="text" name="submenu" value="<?php echo $dato['submenu']; ?>" id="submenu" size="35"/></td>
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
<?php }?>





