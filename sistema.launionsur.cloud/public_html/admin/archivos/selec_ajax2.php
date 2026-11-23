<?php
ini_set('default_charset','UTF8');
//require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
$con=new cnn;

$fecha = time();
$fecha_con_formato = date("d/m/Y --H:i:s",$fecha);
@$id= $_SESSION["id"];
$publicador="Alcaldia Municipal de La Union";
$var1= $fecha_con_formato;
$var2= $fecha_con_formato;
$fe = substr($var1, 0, 10);  // devuelve "fecha"
$hora = substr($var2,13,10); // devuelve "hora"


        $id=$_POST["r"];
        $consulta = $con->consulta("SELECT * FROM carpeta where id_submenu = '$id' ");
        ?>
   
        <td width="">
        <select name="carpeta" width="25" id="v1" onchange="load3(this.value)">
          <option>seleccione...</option>
         <?php while($dato2 = $con->arreglo($consulta)){ ?>
          <option  value="<?php echo $dato2["id_carpeta"];?>"><?php echo $dato2["carpeta"];?></option>
          <?php }?>
   </select>


<script src="ajax.js"></script>