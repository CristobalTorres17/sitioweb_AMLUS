<?php ob_start();
ini_set('default_charset','UTF8');
require("libreria_php/cnn_conexion.php");
$con = new cnn();
// project manager: Gilber Edgardo Reyes
// fecha 07 de Agosto de 2017	
?>
<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">

<meta name="viewport" content="width=device-width,initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="biblioteca/bootstrap/css/bootstrap.min.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/fonts/ptsans/stylesheet.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/fonts/icomoon/style.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/login.css" media="screen">
<link rel="stylesheet" type="text/css" href="biblioteca/css/mws-theme.css" media="screen">
<link href="imagenes/LOGO/favi.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">
<title>Inicio de sesión</title>
</head>
<body>
    <div id="mws-login-wrapper">
        <div id="mws-login">
            <h1>Inicio de sesión</h1>
            <div class="mws-login-lock"><i class="icon-lock"></i></div>
            <div id="mws-login-form">
                <form class="mws-form" action="" method="post" id="form1" name="form1">
                    <div class="mws-form-row">
                        <div class="mws-form-item">
                            <input type="text" name="username" class="mws-login-username required" placeholder="Usuario">
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <div class="mws-form-item">
                            <input type="password" name="password" class="mws-login-password required" placeholder="Contaseña">
                        </div>
                    </div>
                    
                    <div class="mws-form-row">
                        <input type="submit" value="Iniciar Sesión" class="btn btn-success mws-login-button" name="boton" id="boton">
                    </div>
                </form> 
<?php if(isset($_POST["boton"])){
$u=$_POST["username"];
$usu =strtolower($u);
$p=$_POST["password"];
$pa =strtolower($p);
$pas=md5($pa);

$consulta = $con->consulta("SELECT adm_usuarios.id,adm_usuarios.usu,adm_usuarios.nom,adm_usuarios.ape,adm_usuarios.pas,adm_usuarios.rol,adm_rol.rol,adm_rol.con FROM adm_usuarios INNER JOIN adm_rol ON adm_usuarios.rol = adm_rol.rol 
HAVING adm_usuarios.usu = '$usu' and adm_usuarios.pas='$pas'");
if($con->conteo($consulta)>0)
{ 	
	$view = $con->arreglo($consulta);
	echo "<script> window.location.href='iniciar.php';</script>";	 
 session_start();
 $_SESSION["seg"] = "ok";
 $_SESSION["id"]  = $view["id"];
 $_SESSION["nom"] = $view["nom"]." ".$view["ape"];
 $_SESSION["rol"] = $view["con"];
 exit;
}else{?>
<div class="mws-form-message error">Error! Usuario o Password incorrectos</div>	
<?php } }?>	                
     </div>
        </div>
    </div>
    <!-- JavaScript Plugins -->
    <script src="biblioteca/js/libs/jquery-1.8.3.min.js"></script>
    <script src="biblioteca/js/libs/jquery.placeholder.min.js"></script>
    <script src="biblioteca/custom-plugins/fileinput.js"></script>
    <!-- jQuery-UI Dependent Scripts -->
    <script src="biblioteca/jui/js/jquery-ui-effects.min.js"></script>
    <!-- Plugin Scripts -->
    <script src="biblioteca/plugins/validate/jquery.validate-min.js"></script>
    <!-- Login Script -->
    <script src="biblioteca/js/core/login.js"></script>
</body>
</html>
<?php
ob_end_flush();
?>