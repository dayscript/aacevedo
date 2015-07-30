<?php
class formatoContenido {

	var $arreglo;

	function formatoContenido($arreglo) {
		$this->arreglo = array();
		$this->arreglo = $arreglo;
	}

	function noticias($delimitador = " ", $lineas = false) {
		$return = "";
		if ($lineas) {
			foreach($lineas as $linea){
				$return .= $this->arreglo[$linea] . $delimitador;
			}
		} else {
			foreach($this->arreglo as $linea){
				$return .= $linea . $delimitador;
			}
		}
		return $return;
	}

	function NoticiasRCN($lineas = false) {
		$return = "";
		if ($lineas) {
			foreach($this->arreglo as $arreglo){
				foreach($lineas as $linea){
					$return .= ereg_replace("Mostrar Video", "",$arreglo[$linea]) ."\n\n";
				}
			}
		} else {
			foreach($this->arreglo as $arreglo){
				foreach($arreglo as $ar){
					$return .= $ar ."\n\n";
				}
			}
		}
		return $return;
	}

	function noticiasCali(){
		$return = "";
		foreach($this->arreglo as $arreglo){
			$return = explode(".", implode(" ", $arreglo));
			return $return[0];
		}
	}

	function clasificacionFPC() {
		foreach($this->arreglo as $arreglo){
			foreach($arreglo as $ar){
				$return .= $ar ." ";
			}
			$return .= "\n";
		}
		return $return;
	}

	function dimayor() {
		$cadena = "";
		foreach($this->arreglo as $arreglo){
			foreach($arreglo as $linea){
				if(ereg("alt=", $linea)){
					$tmp = explode("alt=\"", $linea);
					$tmp = explode("\"", $tmp[1]);
					$cadena .= ucfirst(strtolower($tmp[0])) ." ";
				} else {
					$cadena .= $linea ." ";
				}
			}
			$cadena .= "\n";
		}
		return $cadena;
	}

	function noticiasF1(){
		$return = "";
		foreach($this->arreglo as $arreglo){
			$return = explode("<BR><BR>", implode(" ", $arreglo));
			return $return[0];
		}
	}

}
?>
