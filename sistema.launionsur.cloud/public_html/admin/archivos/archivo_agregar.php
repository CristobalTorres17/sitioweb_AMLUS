<?php 
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con=new cnn;
$fecha_con_formato = date("Y-m-d");
$id= $_SESSION["id"];
$publicador="Alcaldia Municipal de La Union";
$var1= $fecha_con_formato;
$var2= $fecha_con_formato;
$fe = substr($var1, 0, 10);  // devuelve "fecha"
$hora = substr($var2,13,10); // devuelve "hora"
if(isset($_POST["boton"])){
  
$menu = $_POST["menu"];
$submenu= $_POST["submenu"];
$carpeta= $_POST["carpeta"];
$nivel= $_POST["nivel"];
$subnivel= $_POST["subnivel"];
$anno= $_POST["anno"];
$nombre= $_POST["nombre"];
$archivo= $_FILES['archivo']['name'];
	
	
//$archivo= basename($archivos, ".pdf");  
	

function urls_amigables($archivo) {
 
// Tranformamos todo a minusculas
 
$archivo = strtolower($archivo);
 
//Rememplazamos caracteres especiales latinos
 
$find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
 
$repl = array('a', 'e', 'i', 'o', 'u', 'n');
 
$archivo = str_replace ($find, $repl, $archivo);
 
// Añaadimos los guiones
 
$find = array(' ', '&', '\r\n', '\n', '+'); 
$archivo = str_replace ($find, '-', $archivo);
 
// Eliminamos y Reemplazamos demás caracteres especiales
 
$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
 
$repl = array('', '-', '');
 
$archivo = preg_replace ($find, $repl, $archivo);
 
return $archivo;
 
}	
	
$r= $archivo;
	

	
	
	
$nombreAnexo= $_POST["nombreAnexo"];
$anexo= $_FILES['anexo']['name'];
    
if ($submenu == "") {
  $submenu = 0;
}

if ($carpeta == "") {
  $carpeta = 0;
}

if ($nivel == "") {
  $nivel = 0;
}

if ($subnivel == "") {
  $subnivel = 0;
}

if ($anno == "") {
  $anno = 0;
}

//Procedimiento para la subida de la imágen de portada
  $NombreTempora = $_FILES['archivo']['tmp_name'];
  $TipoImg = $_FILES['archivo']['type'];
  $NombreImg = date("ymdhis"); //Usamos estos parámetros de date() para obtener un nombre único
/*  if( $TipoImg == 'application/pdf' ){
    $Extension = "pdf";
  }
  else {
    echo "<script>alert('El tipo de archivo no es pdf');</script>";
    echo "<script>window.location.replace('archivo_agregar.php');</script>"; 
    exit;
  }
    
*/

  if($submenu==0){
  $ru=$con->consulta("SELECT ruta FROM menu WHERE id_menu='$menu'");
    $ruta=$con->arreglo($ru);
    
  $consulta = $con->consulta("INSERT INTO archivos VALUES (null,'$menu',0,0,0,0,0,'$nombre','$r','$nombreAnexo','$anexo','$fecha_con_formato','$ruta[ruta]')");
   move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['ruta'].$r);
   move_uploaded_file($_FILES['anexo']['tmp_name'],$ruta['ruta'].$_FILES['anexo']['name']);  
    
      echo"
  <script>
  alert ('archivo agregado Exitosamente');
   window.opener.location.reload('archivo_ver.php');
  window.location.href='archivo_agregar.php';
  </script>
  ";

  }elseif($carpeta==0){
    
  $ru=$con->consulta("SELECT rutaSubmenu FROM submenu WHERE id_submenu='$submenu'");
    $ruta=$con->arreglo($ru);
    
  $consulta = $con->consulta("INSERT INTO archivos
              VALUES (null,'$menu','$submenu',0,0,0,0,'$nombre','$r','$nombreAnexo','$anexo','$fecha_con_formato','$ruta[rutaSubmenu]')");
move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['rutaSubmenu'].$r); 
      move_uploaded_file($_FILES['anexo']['tmp_name'],$ruta['ruta'].$_FILES['anexo']['name']); 
      echo"
  <script>
  alert ('archivo agregado Exitosamente');
   window.opener.location.reload('archivo_ver.php'); 
   window.location.href='archivo_agregar.php';
  </script>
  ";

  }elseif($nivel==0){
    
  $ru=$con->consulta("SELECT ruta FROM carpeta WHERE id_carpeta='$carpeta'");
    $ruta=$con->arreglo($ru);
    
  $consulta = $con->consulta("INSERT INTO archivos
              VALUES (null,'$menu','$submenu','$carpeta',0,0,0,'$nombre','$r','$nombreAnexo','$anexo','$fecha_con_formato','$ruta[ruta]')");
move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['ruta'].$r);
      move_uploaded_file($_FILES['anexo']['tmp_name'],$ruta['ruta'].$_FILES['anexo']['name']); 

      echo"
  <script>
  alert ('archivo agregado Exitosamente');
   window.opener.location.reload('archivo_ver.php'); 
   window.location.href='archivo_agregar.php';
  </script>
  ";
  }elseif($subnivel==0){    
    
    $ru=$con->consulta("SELECT ruta FROM nivel WHERE id_nivel='$nivel'");
    $ruta=$con->arreglo($ru);
    
    $consulta = $con->consulta("INSERT INTO archivos
              VALUES (null,'$menu','$submenu','$carpeta','$nivel',0,0,'$nombre','$r','$nombreAnexo','$anexo','$fecha_con_formato','$ruta[ruta]')");
    move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['ruta'].$r);
    move_uploaded_file($_FILES['anexo']['tmp_name'],$ruta['ruta'].$_FILES['anexo']['name']); 

          echo"
  <script>
  alert ('archivo agregado Exitosamente');
   window.opener.location.reload('archivo_ver.php'); 
  window.location.href='archivo_agregar.php';
  </script>
  ";
    
  }elseif($anno==0){
    $ru=$con->consulta("SELECT ruta FROM subnivel WHERE id_subNivel='$subnivel'");
    $ruta=$con->arreglo($ru);
    
    $consulta = $con->consulta("INSERT INTO archivos VALUES (null,'$menu','$submenu','$carpeta','$nivel','$subnivel',0,'$nombre','$r','$nombreAnexo','$anexo','$fecha_con_formato','$ruta[ruta]')");
    move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['ruta'].$r);
    move_uploaded_file($_FILES['anexo']['tmp_name'],$ruta['ruta'].$_FILES['anexo']['name']); 

          echo"
  <script>
  alert ('archivo agregado Exitosamente');
   window.opener.location.reload('archivo_ver.php'); 
   window.location.href='archivo_agregar.php';
  </script>  ";
  }else{
      $ru=$con->consulta("SELECT ruta FROM anno WHERE id_anno='$anno'");
    $ruta=$con->arreglo($ru);
  $consulta = $con->consulta("INSERT INTO archivos
   VALUES (null,'$menu','$submenu','$carpeta','$nivel','$subnivel','$anno','$nombre','$r','$nombreAnexo','$anexo','$fecha_con_formato','$ruta[ruta]')");
move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['ruta'].$r);    

      echo"
  <script>
  alert ('archivo agregado Exitosamente');
   window.opener.location.reload('archivo_ver.php'); 
   window.location.href='archivo_agregar.php';

  </script>
  ";
    
  }




}else{
 
?>

<!DOCTYPE html>
<html>
<?php include("../head.php"); ?>
<script src="ajax.js"></script>
<body>
<div class="mws-panel grid_5">
  <div class="mws-panel-header"> <span><i class="icon-pencil"></i> ingresar archivo</span> </div>
  <div class="mws-panel-body no-padding">
    <div class="mws-form-inline">
<form id="form1" name="form1" method="post" enctype="multipart/form-data">
  <table width="343" border="0" align="center">
    <tbody>
      <tr>
        <td width="54" height="34"><b>menu:</b></td>
        <?php $consulta = $con->consulta("SELECT * FROM menu"); ?>
        
        <td width="">
            <input type="hidden" value="0" name="submenu">
           <input type="hidden" value="0" name="carpeta">
           <input type="hidden" value="0" name="nivel">
            <input type="hidden" value="0" name="subnivel">
             <input type="hidden" value="0" name="anno">
        <select name="menu" width="25" onchange="load(this.value);limp();">
     
           <option>seleccione...</option>

         <?php while($dato2 = $con->arreglo($consulta)){ ?>
          <option  value="<?php echo $dato2["id_menu"];?>"><?php echo $dato2["menu"];?></option>
          <?php }?>
        </select>
        
        </td>
        </tr>

        <tr>
      <td width="54" height="34"><b> submenu:</b></td>
       <td id="myDiv">
         <!--submenu-->
       </td>
       
     </tr>
        <tr>
       <td width="54" height="34"><b> carpeta:</b></td>
       <td id="myDiv2" >
          <!--carepeta-->
       </td>
        </tr>
       <tr>
        <td width="54" height="34"><b> nivel:</b></td>
       <td id="myDiv3">
          <!--nivel-->
       </td>
        
       </tr>     
        <tr>
        <td width="54" height="34"><b> subnivel:</b></td>
       <td id="myDiv4">
          <!--subnivel-->
       </td>
       </tr> 

           <tr>
        <td width="54" height="34"><b> año:</b></td>
       <td id="myDiv5">
          <!--año-->
       </td>
       </tr> 

           <tr>
        <td width="54" height="34"><b> archivo:</b></td>
       <td>
         <input type="file" aceo name="archivo">
          <input type="text" aceo name="nombre">
       </td>

       </tr> 
       <tr>
        <td width="54" height="34"><b> Anexo:</b></td>
       <td>
         <input type="file" aceo name="anexo">
          <input type="text" aceo name="nombreAnexo">
       </td>

       </tr> 
<tr>
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



