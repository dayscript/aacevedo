<?php
//- 60 * 60 * 6
  $filename = "trm.log";
  if(filemtime($filename) < time() - 60 ){

    $str = file("http://obiee.banrep.gov.co/analytics/saw.dll?Go&NQUser=publico&NQPassword=publico&Path=/shared/Consulta%20Series%20Estadisticas%20desde%20Excel/1.%20Tasa%20de%20Cambio%20Peso%20Colombiano/1.1%20TRM%20-%20Disponible%20desde%20el%2027%20de%20noviembre%20de%201991/TRM%20para%20un%20dia&lang=es&");
    $str = implode("",$str);
    $str = strip_tags($str);
    $str = trim(substr($str, strpos($str,"$")+1));
    $str = trim(substr($str, 0, strpos($str, " ")));
    $str = str_replace(",", ".", $str);

    if($str != "TRM" && $str != "0"){
      file_put_contents( $filename, $str );
    }
  }

  echo $trm = file_get_contents($filename);


