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



































<?php
ini_set('default_charset','UTF8');
//ini_set("error_reporting",0);
require("../seguro.php");
require("../cnn_conexion.php");
$rol_actual = $_SESSION["rol"];

$fecha = time();
$fecha_con_formato = date("d/m/Y --H:i:s",$fecha);
$con = new cnn();
if(isset($_POST["boton"]))
{	

$menu    = $_POST["menu"];
$submenu = $_POST["submenu"];
$archivo = $_FILES['archivo']['name'];
$nombre  = $_POST["nombre"];
$carpeta = $_POST["carpeta"];
$nivel =$_POST["nivel"];
$subnivel=$_POST["subnivel"];	
$anno    = $_POST["anno"];

	if($submenu==0){
	$ru=$con->consulta("SELECT ruta FROM menu WHERE id_menu='$menu'");
		$ruta=$con->arreglo($ru);
		
	$consulta = $con->consulta("INSERT INTO archivos
						  VALUES (null,'$menu',0,0,0,0,0,'$nombre','$archivo','$fecha_con_formato','$ruta[ruta]')");
move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['ruta'].$_FILES['archivo']['name']);		
	}elseif($carpeta==0){
		
	$ru=$con->consulta("SELECT rutaSubmenu FROM submenu WHERE id_submenu='$submenu'");
		$ruta=$con->arreglo($ru);
		
	$consulta = $con->consulta("INSERT INTO archivos
						  VALUES (null,'$menu','$submenu',0,0,0,0,'$nombre','$archivo','$fecha_con_formato','$ruta[rutaSubmenu]')");
move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['rutaSubmenu'].$_FILES['archivo']['name']);	
	}elseif($nivel==0){
		
	$ru=$con->consulta("SELECT ruta FROM carpeta WHERE id_carpeta='$carpeta'");
		$ruta=$con->arreglo($ru);
		
	$consulta = $con->consulta("INSERT INTO archivos
						  VALUES (null,'$menu','$submenu','$carpeta',0,0,0,'$nombre','$archivo','$fecha_con_formato','$ruta[ruta]')");
move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['ruta'].$_FILES['archivo']['name']);		
	}elseif($subnivel==0){		
		
		$ru=$con->consulta("SELECT ruta FROM nivel WHERE id_nivel='$nivel'");
		$ruta=$con->arreglo($ru);
		
		$consulta = $con->consulta("INSERT INTO archivos
						  VALUES (null,'$menu','$submenu','$carpeta','$nivel',0,0,'$nombre','$archivo','$fecha_con_formato','$ruta[ruta]')");
		move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['ruta'].$_FILES['archivo']['name']);
		
	}elseif($anno==0){
		$ru=$con->consulta("SELECT ruta FROM subnivel WHERE id_subNivel='$subnivel'");
		$ruta=$con->arreglo($ru);
		
		$consulta = $con->consulta("INSERT INTO archivos
						  VALUES (null,'$menu','$submenu','$carpeta','$nivel','$subnivel',0,'$nombre','$archivo','$fecha_con_formato','$ruta[ruta]')");
		move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['ruta'].$_FILES['archivo']['name']);
	}else{
			$ru=$con->consulta("SELECT ruta FROM anno WHERE id_anno='$anno'");
		$ruta=$con->arreglo($ru);
	$consulta = $con->consulta("INSERT INTO archivos
						  VALUES (null,'$menu','$submenu','$carpeta','$nivel','$subnivel','$anno','$nombre','$archivo','$fecha_con_formato','$ruta[ruta]')");
move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta['ruta'].$_FILES['archivo']['name']);		
		
	}
	
////////////////////////////////HISTORIAL ////////////////////////////////////////////////////
$consulta = $con->consulta("SELECT * FROM adm_usuarios WHERE tiq='$tiq_actual'");
$datos = $con->arreglo($consulta);
$vari=$datos["usu"];

$consulta = $con->consulta("SELECT id_archivo FROM  archivos ORDER BY  id_archivo DESC LIMIT 1");
$datosS = $con->arreglo($consulta);
$ulti=$datosS["id_archivo"];
$accion= "Agrego Nuevo Archivo";
$lugar="Administarción de Transparencia";
$consultas = $con->consulta("INSERT INTO historial
						  VALUES (null,'$vari','$fecha_con_formato','$accion','$lugar','$ulti')");		
		
		
	echo "
<script>
alert('Datos Agregado Exitosamente');
window.location.href='agregar.php';
</script>
";	
//mostramos los resultados


exit;
}else {
?><!doctype html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=shift_jis">

<title>Documento sin t&iacute;tulo</title>

<style type="text/css">
<!--
.A {
	color: #FFF;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<form action="" method="post" enctype="multipart/form-data"  name="form1" id="form1">
  <table width="318" border="0" align="center">
    <tr>
      <td align="center" bgcolor="#0080C0" class="A">MENU DONDE PROCEDENCIA</td>
    </tr>
    <tr>
      <td align="center"><?php $consult = $con->consulta("SELECT * FROM menu"); ?>
		
    

       <select name="menu" onchange="submit()">
           <option value="0" selected="selected">Seleccione.....!</option>
		<?php while($docc= $con->arreglo($consult)) {  ?>
           <option value="<?php echo $docc["id_menu"];?>" <?php if($docc["id_menu"]==$_POST["menu"]){?>selected="selected"<?php }?>
           ><?php echo $docc["menu"]; ?>
        </option>
		<?php   } ?>
          </select>
		


    </td>
	  </tr>
	  

    <?php if(isset($_POST["menu"])){ $menu=$_POST["menu"]; ?>
	   <tr>
      <td align="center" bgcolor="#0080C0" class="A">SUB-MENU</td>
    </tr>
	  <tr>
        <td align="center"> <?php  $cosult = $con->consulta("SELECT * FROM submenu WHERE id_menu='$menu'");?>
         <input type="hidden" name="menu" id="menu" value="<?php echo $menu; ?>">
          <select name="submenu" id="submenu" onchange="submit()">
           <option value="0">Seleccione Sub Menu.....</option>
              <?php while($part=$con->arreglo($cosult)){ ?>
              <option value="<?php echo $part["id_submenu"];?>" <?php if($part["id_submenu"]==$_POST["submenu"]){?> selected="selected" <?php }?>><?php echo $part["submenu"];?></option>
              <?php } ?>
        </select>
  


  </td>
    </tr>
 


    <?php if(isset($_POST["submenu"])){ $menu=$_POST["menu"]; $submenu=$_POST["submenu"]; ?>
     <tr>
      <td align="center" bgcolor="#0080C0" class="A">CARPETA</td>
    </tr>
     <tr>
      <td align="center"> <?php  $car = $con->consulta("SELECT * FROM carpeta WHERE 	id_submenu='$submenu'");?>
      <input type="hidden" name="menu" id="menu" value="<?php echo $menu; ?>">
      <input type="hidden" name="submenu" id="submenu" value="<?php echo $submenu; ?>">
      <select name="carpeta" id="carpeta" onchange="submit()">
           <option value="0">Seleccione carpeta.....</option>
              <?php while($carp=$con->arreglo($car)){ ?>
              <option value="<?php echo $carp["id_carpeta"];?>" <?php if($carp["id_carpeta"]==$_POST["carpeta"]){?> selected="selected" <?php }?>><?php echo $carp["carpeta"];?></option>
              <?php } ?>
     




        </select>
      </td>
    </tr>
    <?php if(isset($_POST["carpeta"])){ $menu=$_POST["menu"]; $submenu=$_POST["submenu"]; $carpeta=$_POST["carpeta"]; ?>
	  <tr>
		  <td align="center" bgcolor="#0080C0" class="A">NIVEL</td>
	  </tr>
	  <tr>
	    <td align="center"><?php $ni=$con->consulta("SELECT * FROM nivel WHERE id_carpeta='$carpeta'") ?>
		  <input type="hidden" name="menu" id="menu" value="<?php echo $menu; ?>">
          <input type="hidden" name="submenu" id="submenu" value="<?php echo $submenu; ?>">
          <input type="hidden" name="carpeta" id="carpeta" value="<?php echo $carpeta; ?>">
      <select name="nivel" id="nivel" onchange="submit()">
           <option value="0">Seleccione Nivel.....</option>
              <?php while($niv=$con->arreglo($ni)){ ?>
          <option value="<?php echo $niv["id_nivel"];?>" <?php if($niv["id_nivel"]==$_POST["nivel"]){?> selected="selected" <?php }?>><?php echo $niv["nivel"];?></option>
              <?php } ?>
        </select>
	    </td>
	  </tr>
    <?php if(isset($_POST["nivel"])){ $menu=$_POST["menu"]; $submenu=$_POST["submenu"]; $carpeta=$_POST["carpeta"]; $nivel=$_POST["nivel"]; ?>
    
    <tr>
		  <td align="center" bgcolor="#0080C0" class="A">SUB-NIVEL</td>
    </tr>
	  <tr>
		  <td align="center"><?php $subni=$con->consulta("SELECT * FROM subnivel WHERE id_nivel='$nivel'") ?>
		  <input type="hidden" name="menu" id="menu" value="<?php echo $menu; ?>">
          <input type="hidden" name="submenu" id="submenu" value="<?php echo $submenu; ?>">
          <input type="hidden" name="carpeta" id="carpeta" value="<?php echo $carpeta; ?>">
          <input type="hidden" name="nivel" id="nivel" value="<?php echo $nivel; ?>">
      <select name="subnivel" id="subnivel" onchange="submit()">
           <option value="0">Seleccione Sub-Nivel.....</option>
              <?php while($subniv=$con->arreglo($subni)){ ?>
              <option value="<?php echo $subniv["id_subNivel"];?>" <?php if($subniv["id_subNivel"]==$_POST["subnivel"]){?> selected="selected" <?php }?>><?php echo $subniv["subnivel"];?></option>
              <?php } ?>
        </select>
		  </td>
	  </tr>
    
    <?php if(isset($_POST["subnivel"])){ $menu=$_POST["menu"]; $submenu=$_POST["submenu"]; $carpeta=$_POST["carpeta"]; $nivel=$_POST["nivel"]; $subnivel=$_POST["subnivel"]; ?>
    
    <tr>
      <td align="center" bgcolor="#0080C0" class="A">AÑO</td>
    </tr>
     <tr>
      <td align="center"><?php  $an = $con->consulta("SELECT * FROM anno WHERE id_subnivel='$subnivel'");?>
       <input type="hidden" name="menu" id="menu" value="<?php echo $menu; ?>">
          <input type="hidden" name="submenu" id="submenu" value="<?php echo $submenu; ?>">
          <input type="hidden" name="carpeta" id="carpeta" value="<?php echo $carpeta; ?>">
          <input type="hidden" name="nivel" id="nivel" value="<?php echo $nivel; ?>">
          <input type="hidden" name="subnivel" id="subnivel" value="<?php echo $subnivel; ?>">
      <select name="anno" id="anno">
           <option value="0">Seleccione Año.....</option>
              <?php while($ann=$con->arreglo($an)){ ?>
              <option value="<?php echo $ann["id_anno"];?>" <?php if($ann["id_anno"]==@$_REQUEST["anno"]){?> selected="selected" <?php }?>><?php echo $ann["anno"];?></option>
              <?php } ?>
        </select>
      </td>
    </tr>
    <?php } ?>
    <?php } ?>
    <?php } ?>
    <?php } ?>
    <?php } ?>
    <tr>
      <td align="center" bgcolor="#0080C0" class="A">Archivo<br />
      <input name="nombre" type="text" id="nombre" placeholder="Nombre del Archivo" size="40">
      </td>
    </tr>
    <tr>
      <td align="center"><input type="file" name="archivo" id="archivo"></td>
    </tr>
    <tr>
      <td align="center"><input type="submit" name="boton" id="boton" value="Subri Archivo"></td>
    </tr>
  </table>
</form>
</body>
</html>

</body>
</html>
<?php
}
?>