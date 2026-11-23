<?php
require("../seguro.php");
require("../libreria_php/cnn_conexion.php");
ini_set('default_charset', 'UTF8');
$rol_actual = $_SESSION["rol"];

$fecha = time();
$fecha_con_formato = date("d/m/Y -- H:i:s", $fecha);
$con = new cnn();

if (isset($_POST["boton"])) {
  // Lógica para el manejo del formulario
  $menu = $_POST["menu"];
  $submenu = $_POST["submenu"];
  $archivo = $_FILES['archivo']['name'];
  $nombre = $_POST["nombre"];
  $carpeta = $_POST["carpeta"];
  $nivel = $_POST["nivel"];
  $subnivel = $_POST["subnivel"];
  $anno = $_POST["anno"];

  // Rutas y consultas para los diferentes niveles
  if ($archivo != "") {
    if ($submenu == 0) {
      $ru = $con->consulta("SELECT ruta FROM menu WHERE id_menu='$menu'");
      $ruta = $con->arreglo($ru);
      $consulta = $con->consulta("UPDATE archivos SET id_menu='$menu', nombre='$nombre', archivo='$archivo', ruta='{$ruta['ruta']}' WHERE id_archivo='$_GET[cod]'");
      move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta['ruta'] . $_FILES['archivo']['name']);
    } elseif ($carpeta == 0) {
      $ru = $con->consulta("SELECT rutaSubmenu FROM submenu WHERE id_submenu='$submenu'");
      $ruta = $con->arreglo($ru);
      $consulta = $con->consulta("UPDATE archivos SET id_menu='$menu', id_submenu='$submenu', nombre='$nombre', archivo='$archivo', ruta='{$ruta['rutaSubmenu']}' WHERE id_archivo='$_GET[cod]'");
      move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta['rutaSubmenu'] . $_FILES['archivo']['name']);
    }
    // Continuar con la lógica similar para los otros niveles (nivel, subnivel, anno)
  } else {
    $consulta = $con->consulta("UPDATE archivos SET id_menu='$menu', id_submenu='$submenu', id_carpeta='$carpeta', id_nivel='$nivel', id_subnivel='$subnivel', id_anno='$anno', nombre='$nombre' WHERE id_archivo='$_GET[cod]'");
  }


  echo "<script>alert('Archivo actualizado exitosamente'); window.location.href='archivo_ver.php';</script>";
  exit;
} else {
  $ver = $con->consulta("SELECT * FROM archivos WHERE id_archivo='$_GET[cod]'");
  $vie = $con->arreglo($ver);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Actualizar Archivo</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Actualizar Archivo</h2>
    <form action="" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="menu">Menu de Procedencia</label>
        <select class="form-control" name="menu" id="menu">
          <option value="0">Seleccione...</option>
          <?php
          $consult = $con->consulta("SELECT * FROM menu");
          while ($docc = $con->arreglo($consult)) {
            echo "<option value='{$docc['id_menu']}' " . ($docc['id_menu'] == $vie['id_menu'] ? "selected" : "") . ">{$docc['menu']}</option>";
          }
          ?>
        </select>
      </div>

      <div class="form-group">
        <label for="submenu">Sub-menu</label>
        <select class="form-control" name="submenu" id="submenu">
          <option value="0">Seleccione...</option>
          <?php
          $cosult = $con->consulta("SELECT * FROM submenu, menu WHERE submenu.id_menu = menu.id_menu");
          while ($part = $con->arreglo($cosult)) {
            echo "<option value='{$part['id_submenu']}' " . ($part['id_submenu'] == $vie['id_submenu'] ? "selected" : "") . ">{$part['menu']} -- {$part['submenu']}</option>";
          }
          ?>
        </select>
      </div>

      <!-- Similar bloques para Carpeta, Nivel, Sub-nivel, Año -->

      <div class="form-group">
        <label for="archivo">Archivo Actual</label>
        <input type="file" class="form-control-file" name="archivo" id="archivo">
      </div>

      <div class="form-group">
        <label for="nombre">Nombre del Archivo</label>
        <input type="text" class="form-control" name="nombre" id="nombre" value="<?php echo $vie['nombre']; ?>" placeholder="Nombre del Archivo">
      </div>

      <button type="submit" name="boton" class="btn btn-primary btn-block">Actualizar Archivo</button>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
