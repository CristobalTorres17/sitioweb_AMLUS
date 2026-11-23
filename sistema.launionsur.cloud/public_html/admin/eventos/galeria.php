<?php
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con = new cnn();
if(isset($_FILES['files'])){

  $errors= array();
	foreach($_FILES['files']['tmp_name'] as $key => $tmp_name ){
		$file_name = $key.$_FILES['files']['name'][$key];
		$file_size =$_FILES['files']['size'][$key];
		$file_tmp =$_FILES['files']['tmp_name'][$key];
		$file_type=$_FILES['files']['type'][$key];	
        if($file_size > 8388608){
			$errors[]='File size must be less than 8 MB';
        }

$desired_dir="images";
	
$consulta = $con->consulta("INSERT INTO galeria VALUES (NULL,'$_GET[id]','$file_name','Activo',0)");
move_uploaded_file($file_tmp,"$desired_dir/".$file_name);	
	
 echo "<script> alert('Imagenes agregadas exitosamente'); self.close(); </script>";	
		
}	 
}	
//}
else{
?>
<!DOCTYPE html>
<html>
<?php include("../head.php");?>
<script>
function fileValidation(){
    var fileInput = document.getElementById('files[]');
    var filePath = fileInput.value;
    var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
    if(!allowedExtensions.exec(filePath)){
        alert('Cargue un archivo con extensión jpeg/.jpg/.png ');
        fileInput.value = '';
        return false;
    }
}	
</script>
<body>
<div class="mws-panel grid_8">
  <div class="mws-panel-header"> <span><i class="icon-book"></i> <?php echo $_GET["nombre"];?> </span> </div>
  <div class="mws-panel-body no-padding">
    <div class="mws-form-inline">
 <form action="" method="post" enctype="multipart/form-data" name="form1" class="form-horizontal" id="form1">
<table border="0" align="center" cellpadding="0" >

	<tr>
    <td colspan="10" align="center"><b>Imagen</b> <input name="files[]" type="file" id="files[]" onchange="return fileValidation()" multiple="multiple"></td>
  </tr>
  <tr><td>
  	<p>Maximo 4 imagnes </p>
  </td></tr>
  <tr>
    <td colspan="3" align="center">
 
<button type="submit" class="btn btn-danger" > <i class="icol-disk"></i> Guardar imagenes</button>


   </td>
  </tr>
</table>
</form>
	  </div>
	</div>
	<?php include("../footer.php"); ?>
	</div>
<?php } ?>