<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$mysqli = new mysqli("localhost", "forge", "cOgjZoIGk9eK3e4LFIfM", "services");

if ($mysqli->connect_errno) {
    printf("Falló la conexión: %s\n", $mysqli->connect_error);
    exit();
}

if( isset($_GET['economy'])) {

  if($_GET['economy'] == "TRM"){

    $query ='SELECT * FROM services.economy_test where name ="TRM" ';
    if($resultado = $mysqli->query($query,MYSQLI_USE_RESULT)){
      echo "entro";
      while($obj = $resultado->fetch_object()){
              echo json_encode($obj);

      }
    }
  }

}else{
  echo "BAD REQUEST";
}



