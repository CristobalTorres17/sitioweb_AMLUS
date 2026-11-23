<?php 
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con=new cnn;
$db = new cnn();
$fecha = time();
$fecha_con_formato = date("d/m/Y --H:i:s",$fecha);
$fechas = date("Y-m-d");
$id= $_SESSION["id"];
$publicador="Alcaldia Municipal de La Union";
$var1= $fecha_con_formato;
$var2= $fecha_con_formato;
$fe = substr($var1, 0, 10);  // devuelve "fecha"
$hora = substr($var2,13,10); // devuelve "hora"
if(isset($_POST["boton"])){
  
$menu = $_POST["menu"];
$submenu = $_POST["submenu"];
$carpeta = $_POST["carpeta"];

$ver=$db->consulta("SELECT ruta FROM menu WHERE id_menu='$menu'");
$esta=$db->arreglo($ver);
$r=$esta["ruta"];



///////carpeta txt dentro del año y tomo /////////////////
$rr="../archivos/".$r.$carpeta."/";
$dirmake = mkdir($rr, 0777);
$rutaMenu=$r.$carpeta."/";


$consulta = $db->consulta("INSERT INTO submenu VALUES (null, '$menu', '$submenu', '$rutaMenu')");


////////////////////////////////HISTORIAL ////////////////////////////////////////////////////
$coon = new cnn();
$consulta = $coon->consulta("SELECT * FROM adm_usuarios WHERE id='$id'");
$datos = $coon->arreglo($consulta);
//$vari=$datos["usu"];
$con = new cnn();
$consulta = $con->consulta("SELECT * FROM  submenu ORDER BY  submenu.id_submenu DESC LIMIT 1");
$datosS = $con->arreglo($consulta);
$ulti=$datosS["id_submenu"];
$accion= "Agrego submenu";
$lugar="submenu";
$consultas = $db->consulta("INSERT INTO historial VALUES (null, '$id', '$fechas', '$accion', '$lugar', '$ulti', 'activo')");    



  echo"
  <script>
  alert ('submenu agregado Exitosamente');
   window.opener.location.reload('submenu_ver.php'); self.close();

  </script>
  ";
}else{
?>
<!DOCTYPE html>
<html>
<?php include("../head.php"); ?>
<body>
<div class="mws-panel grid_5">
  <div class="mws-panel-header"> <span><i class="icon-pencil"></i> ingresar submenu</span> </div>
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
          <option value="<?php echo $dato2["id_menu"];?>"><?php echo $dato2["menu"];?></option>
          <?php }?>
        </select>
        </td>
      </tr>
      <tr>
        <td width="54" height="34"><b> nombre submenu:</b></td>
         <td><input type="text" name="submenu" id="submenu" size="35"/></td>
        </tr>
      <tr>
        <td width="54" height="34"><b> carpeta:</b></td>
         <td><input type="text" name="carpeta" id="carpeta" size="35"/></td>
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










