<?php require("seguro.php"); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Documento sin título</title>
</head>
<body>
<table width="473" height="227" border="0" align="center">
  <tr>
    <td align="center"><img src="imagenes/LOGO/Logo.png" width="638" height="300"><br><h1>BIENVENIDO: </h1></td>
  </tr>
  <tr>
    <td align="center"><h3><?php echo $_SESSION["nom"]; ?></h3></td>
  </tr>
</table>
</body>
</html>