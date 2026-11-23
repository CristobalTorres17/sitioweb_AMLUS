<?php 
ini_set('default_charset','UTF8');
class cnn
{
private $conexion;
  public function cnn()
  {
   if(!isset($this->conexion))
   {
    $this->conexion = mysqli_connect('127.0.0.1', 'union_local', getenv('LAUNION_LOCAL_DB_PASSWORD'), 'Dev_a1c4lu5v_alcaldia', (int) (getenv('LAUNION_MYSQL_PORT') ?: 3307));
 //  mysqli_query("SET NAMES 'utf8'");
    mysqli_select_db($this->conexion,"Dev_a1c4lu5v_alcaldia") or die(mysqli_error($this->conexion));
   
   mysqli_set_charset($this->conexion,"utf8");
   }
  }
public function consulta($consulta)
 {

  $resultado = mysqli_query($this->conexion,$consulta);

  if(!$resultado)
  {
   echo 'MySQL Error: ' . mysqli_error($this->conexion);
   exit;
  }
  return $resultado;
 }
// Metodo fetch array 
public function arreglo($consulta)
{
return mysqli_fetch_array($consulta);
}
// Metodo conteo de registros
public function conteo($consulta)
{
return mysqli_num_rows($consulta);
}
// Metodo conteo de registros
public function totalregis()
{
return $this->total_consultas;
}
}
?>