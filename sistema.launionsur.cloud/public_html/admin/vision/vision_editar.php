<?php 
ini_set('default_charset','UTF8');
require("../libreria_php/cnn_conexion.php");
require("../seguro.php");
$con=new cnn;
$db = new cnn();
$fecha = time();
$fecha_con_formato = date("d/m/Y --H:i:s",$fecha);
$cod_editar = $_GET["cod"];
$id= $_SESSION["id"];
if(isset($_POST["boton"])){
  $vision=$_POST["vision"];

  
$insertar=$con->consulta("UPDATE vision SET vision='$vision' WHERE id_vision='$_GET[cod]'");
  ////////////////////////////////HISTORIAL ////////////////////////////////////////////////////
$coon = new cnn();
$consulta = $coon->consulta("SELECT * FROM adm_usuarios WHERE id='$id'");
$datos = $coon->arreglo($consulta);
$vari=$datos["usu"];
$con = new cnn();
$consulta = $con->consulta("SELECT * FROM  vision ORDER BY  vision.id_vision DESC LIMIT 1");
$datosS = $con->arreglo($consulta);
$ulti=$datosS["id_vision"];
$accion= "Actualizo Vision";
$lugar="Administarción de Vision";
$consultas = $db->consulta("INSERT INTO historial VALUES (null,'$vari','$fecha_con_formato','$accion','$lugar','$ulti','activo')");    


  echo"
  <script>
  alert ('Actualización de Vision Exitosamente');
  window.opener.location.reload('vision_ver.php'); self.close();
  </script>
  ";
}else{
$consultar=$con->consulta("SELECT * FROM vision WHERE id_vision='$_GET[cod]'");
$ver=$con->arreglo($consultar); 
?>
<!DOCTYPE html>
<html>
<?php include("../head.php"); ?>
<body>
<div class="mws-panel grid_3">
  <div class="mws-panel-header"> <span><i class="icon-pencil"></i>Actualizando de vision</span> </div>
  <div class="mws-panel-body no-padding">
    <div class="mws-form-inline">
<form id="form1" name="form1" method="post">
  <table width="343" border="0" align="center">
    <tbody>
      <tr>
        <td align="center"><textarea name="vision" cols="75" rows="5" require=""><?php echo $ver["vision"]; ?></textarea></td>
        </tr>
      <tr>
        <td align="center"><input type="submit" name="boton" id="boton" value="Actualizar Datos" class="btn btn-danger"></td>
        </tr>
    </tbody>
    <?php include("../footer.php"); ?>
  </table>
</form>
</div>
  </div>
</div>
</body>
</html>
<?php }?>