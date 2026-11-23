<?php 
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con=new cnn;
$db = new cnn();
$fecha = time();
$fecha_con_formato = date("d/m/Y --H:i:s",$fecha);
$id= $_SESSION["id"];
if(isset($_POST["boton"])){
  $vision=$_POST["vision"];

  $insertar=$con->consulta("INSERT INTO vision VALUE (NULL, '$vision' )");
////////////////////////////////HISTORIAL ////////////////////////////////////////////////////
$coon = new cnn();
$consulta = $coon->consulta("SELECT * FROM adm_usuarios WHERE id='$id'");
$datos = $coon->arreglo($consulta);
$vari=$datos["usu"];
$con = new cnn();
$consulta = $con->consulta("SELECT * FROM  vision ORDER BY  vision.id_vision DESC LIMIT 1");
$datosS = $con->arreglo($consulta);
$ulti=$datosS["id_vision"];
$accion= "Agrego Vision";
$lugar="Administarción de Vision";
$consultas = $db->consulta("INSERT INTO historial VALUES (null,'$vari','$fecha_con_formato','$accion','$lugar','$ulti','activo')");    



  echo"
  <script>
  alert ('vision agregado Exitosamente');
   window.opener.location.reload('vision_ver.php'); self.close();

  </script>
  ";
}else{
?>
<!DOCTYPE html>
<html>
<?php include("../head.php"); ?>
<body>
<div class="mws-panel grid_3">
  <div class="mws-panel-header"> <span><i class="icon-pencil"></i> ingresar vision</span> </div>
  <div class="mws-panel-body no-padding">
    <div class="mws-form-inline">
<form id="form1" name="form1" method="post">
  <table width="343" border="0" align="center">
    <tbody>
      <tr>
        <td width="54" height="34"><b> vision:</b></td>
        <td width="500"><textarea require="" name="vision"></textarea></td>
        <td colspan="2" align="center"><input type="submit" name="boton" id="boton" value="Guardar Datos" class="btn btn-danger"></td>
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



