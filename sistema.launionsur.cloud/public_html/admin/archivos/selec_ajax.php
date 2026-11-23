<?php
ini_set('default_charset','UTF8');
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con=new cnn;
$fecha = time();
$fecha_con_formato = date("d/m/Y --H:i:s",$fecha);
$id= $_SESSION["id"];
$publicador="Alcaldia Municipal de La Union";
$var1= $fecha_con_formato;
$var2= $fecha_con_formato;
$fe = substr($var1, 0, 10);  // devuelve "fecha"
$hora = substr($var2,13,10); // devuelve "hora"


$id=$_POST["q"];
$consulta = $con->consulta("SELECT * FROM submenu where id_menu = '$id' ");  ?>
    
        <td width="">
        <select name="submenu" width="25" onchange="load2(this.value)">
        	<option>seleccione...</option>

         <?php while($dato2 = $con->arreglo($consulta)){ ?>
          <option  value="<?php echo $dato2["id_submenu"];?>"><?php echo $dato2["submenu"];?></option>
          <?php }?>
   </select>
 

    <script src="ajax.js"></script>