<?php
ini_set('default_charset','UTF8');
require("seguro.php");
require("libreria_php/cnn_conexion.php");
$con = new cnn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="biblioteca/plugins/colorpicker/colorpicker.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/plugins/imgareaselect/css/imgareaselect-default.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/plugins/jgrowl/jquery.jgrowl.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/bootstrap/css/bootstrap.min.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/fonts/ptsans/stylesheet.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/fonts/icomoon/style.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/mws-style.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/icons/icol16.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/icons/icol32.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/demo.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/jui/css/jquery.ui.all.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/jui/css/jquery.ui.timepicker.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/jui/jquery-ui.custom.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/mws-theme.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/themer.css" media="screen">
<link href="LOGO/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<title>Admin Portal Web - Inicio</title>
</head>
	<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/ 
libs/jquery/1.3.0/jquery.min.js"></script> -->
<script type="text/javascript"> 
/*var auto_refresh = setInterval( 
function () 
{ 
$('#mws-user-notif').load('contenido.php').fadeIn("slow"); 
}, 1000); // actualiza cada 5 segundos 
*/
</script>
<body>
	<div id="mws-header" class="clearfix">
    	<div id="mws-logo-container">
			<div id="mws-logo-wrap"><a href="iniciar.php"><img src="images/mws-logo.png" alt="mws admin"></a></div>
        </div>
        <div id="mws-user-tools" class="clearfix">
			<?php if($_SESSION["rol"]=="Super" OR $_SESSION["rol"]=="Administrador"){?>
           <!-- Notifications -->
        	<div id="mws-user-notif" class="mws-dropdown-menu">
            <?php //include("contenido.php");?>
            </div>
            <?php } ?>
            <div id="mws-user-info" class="mws-inset">
<?php $consulta = $con->consulta("SELECT foto FROM adm_usuarios WHERE adm_usuarios.id='$_SESSION[id]'");
$dato = $con->arreglo($consulta);?>
            	<div id="mws-user-photo">
                	<img src="usuarios/foto/<?php echo $dato["foto"]; ?>" alt="User Photo">
                </div>
                <div id="mws-user-functions">
                    <div id="mws-username">Hola, <?php echo $_SESSION["nom"];?></div>
                    <ul>
                       <li><a href="<?php echo "usuarios/perfil.php?cod=$_SESSION[id]";?>" target="info">Perfil</a></li>
                        <li><a href="<?php echo "usuarios/usu_claves.php?cod=$_SESSION[id]";?>" target="info">Cambiar Password</a></li>
                        <li><a href="salir.php">Cerrar Secion</a></li>   	                   
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="mws-wrapper">
		<div id="mws-sidebar-stitch"></div>
		<div id="mws-sidebar-bg"></div>
        <div id="mws-sidebar">
            <div id="mws-nav-collapse">
                <span></span>
                <span></span>
                <span></span>
            </div>
            	<br>
            <div id="mws-navigation">
              <?php include("menu.php"); ?>
            </div>         
        </div>
        
        <!-- Main Container Start -->
        <div id="mws-container" class="clearfix">
        
        	<!-- Inner Container Start -->
            <div class="container">
            <div style="padding-bottom:56.25%; position: relative; display: block; height: 0; overflow: hidden;">
            	<iframe style="position: absolute; top: 0; bottom: 0; left: 0; width: 100%; height: 100%; border: 0;" src="imagen.php" name="info"></iframe>
            </div>
            </div>
            <div id="mws-footer">Derechos de la Alcaldia Municipal de La Unión. Todos los derechos reservados. <?php echo date("Y");?></div>
        </div>
    </div>
    <script src="biblioteca/js/libs/jquery-1.8.3.min.js"></script>
    <script src="biblioteca/js/libs/jquery.mousewheel.min.js"></script>
    <script src="biblioteca/js/libs/jquery.placeholder.min.js"></script>
    <script src="biblioteca/custom-plugins/fileinput.js"></script>
    <script src="biblioteca/jui/js/jquery-ui-1.9.2.min.js"></script>
    <script src="biblioteca/jui/jquery-ui.custom.min.js"></script>
    <script src="biblioteca/jui/js/jquery.ui.touch-punch.js"></script>
    <script src="biblioteca/jui/js/timepicker/jquery-ui-timepicker.min.js"></script>
    <script src="biblioteca/plugins/imgareaselect/jquery.imgareaselect.min.js"></script>
    <script src="biblioteca/plugins/jgrowl/jquery.jgrowl-min.js"></script>
    <script src="biblioteca/plugins/validate/jquery.validate-min.js"></script>
    <script src="biblioteca/plugins/colorpicker/colorpicker-min.js"></script>
    <script src="biblioteca/bootstrap/js/bootstrap.min.js"></script>
    <script src="biblioteca/js/core/mws.js"></script>
    <script src="biblioteca/js/core/themer.js"></script>
    <script src="biblioteca/js/demo/demo.widget.js"></script>
</body>
</html>