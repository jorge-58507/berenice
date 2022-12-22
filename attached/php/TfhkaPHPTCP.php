<?php


class Tfhka
{
	private $err0r="";
	private $status="";
	private $StatusError = "";
	private $resp = "";
	private $socket="";
	private $service_port="";
	private $address="";
	private $lineasProcesadas=0;
	private $arrayS1;
	private $arrayS2;
	private $arrayS3;
	private $arrayS4;
	private $arrayS5;
	private $arrayS6;
	private $arrayRX;
	private $arrayRZ;

	public function __construct($address, $service_port)
	{
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$this->address = $address;
		$this->service_port = $service_port;
		if ( $this->socket == false) {
			echo "socket_create() es false falló: razón: ".socket_strerror(socket_last_error())."\n";
		} else {
			$result = socket_connect($this->socket, $address, $service_port);
			if ($result === false) {
				echo "<br /><br />socket_connect() falló.\nRazón: ($result) " . socket_strerror(socket_last_error($this->socket)) . "\n";
				return false;
			} else {
				return true;
			}
		}
	}
// Funcion que verifica si el puerto est� abierto y la conexi�n con la impresora
//Retorno: true si esta presente y false en lo contrario
function CheckFprinter()
{
	$in = "CheckFprinter():1\0";
	$out="";
	socket_write($this->socket, $in, strlen($in));
	$out = socket_read($this->socket, 1024);
	$this->resp = substr($out,10,1);
	if($this->resp==="T"){
		return true;
	}else{
		return false;
	}
}

//Función que envia un comando a la impresora
//Parámetro: Comando en cadena de caracteres ASCII
//Retorno: true si el comando es valido y false en lo contrario

function SendCmd($cmd = "")
{
	$in = "SendCmd():".$cmd."\0";
	$out="";
	socket_write($this->socket, $in, strlen($in));
	$out = socket_read($this->socket, 1024);
	$this->resp= substr($out,10,1);
	if($this->resp==="T"){
		return true;
	}else{
		return false;
	}
}
// Funcion que verifiva el estado y error de la impresora y lo establece en la variable global  $StatusError
//Retorno: Cadena con la informaci�n del estado y error y validiti bolleana
function ReadFpStatus()
{
	$in = "ReadFpStatus():1\0";
	$out = "";
	socket_write($this->socket, $in, strlen($in));
	$out = socket_read($this->socket, 1024);
	$this->StatusError = explode("|",substr($out,10));
	$this->status=$this->StatusError[0];
	$this->err0r=$this->StatusError[2];
	return $this->status." ".$this->err0r;
}
// Funci�n que ejecuta comandos desde un archivo de texto plano
//Parametro: Ruta del archivo con extencion .txt
//Retorno: Cadena con numero de lineas procesadas en el archivo y estado y error
function SendFileCmd($ruta = "")
{
	$this->lineasProcesadas=0;
	$lineas = file($ruta);
	foreach($lineas as $num_linea => $linea){
		if($this->SendCmd($linea)){
			$this->lineasProcesadas = $this->lineasProcesadas+1;
		}
	}
	return $this->lineasProcesadas;
}
//Funcion que sube al PC un tipo de estado de  la impresora
//Parametro: Tipo de estado en cadena Ejem: S1
//Array: Cadena de datos del estado respectivo
function UploadStatus($cmd = "")
{

	switch ($cmd) {
    	case "S1":
        	$in = "UploadStatus():S1\0";
			$out = "";
			socket_write($this->socket, $in, strlen($in));
			$out = socket_read($this->socket, 1024);
			$this->arrayS1 = explode("|",substr($out,10));
 			return $this->arrayS1;

    	case "S2":
        	$in = "UploadStatus():S2\0";
			$out = "";
			socket_write($this->socket, $in, strlen($in));
			$out = socket_read($this->socket, 1024);
			$this->arrayS2 = explode("|",substr($out,10));
 			return $this->arrayS2;
 		case "S3":
        	$in = "UploadStatus():S3\0";
			$out = "";
			socket_write($this->socket, $in, strlen($in));
			$out = socket_read($this->socket, 1024);
			$this->arrayS3 = explode("|",substr($out,10));
 			return $this->arrayS3;
    	case "S4":
        	$in = "UploadStatus():S4\0";
			$out = "";
			socket_write($this->socket, $in, strlen($in));
			$out = socket_read($this->socket, 1024);
			$this->arrayS4 = explode("|",substr($out,10));
 			return $this->arrayS4;
 		case "S5":
        	$in = "UploadStatus():S5\0";
			$out = "";
			socket_write($this->socket, $in, strlen($in));
			$out = socket_read($this->socket, 1024);
			$this->arrayS5 = explode("|",substr($out,10));
 			return $this->arrayS5;
    	case "S6":
        	$in = "UploadStatus():S6\0";
			$out = "";
			socket_write($this->socket, $in, strlen($in));
			$out = socket_read($this->socket, 1024);
			$this->arrayS6 = explode("|",substr($out,10));
 			return $this->arrayS6;;
       	default:
       		return false;
    }

}
//Funcion que sube al PC reportes X y Z de la impresora
//Parametro: Tipo de reportes en cadena Ejem: U0X.
//Retorno: Cadena de datos del o los reporte(s)
function UploadReport($cmd = "")
{

	switch ($cmd) {
    	case "U0X":
        	$in = "UploadReport():U0X\0";
			$out = "";
			socket_write($this->socket, $in, strlen($in));
			$out = socket_read($this->socket, 1024);
			echo "out:".$out;
			$this->arrayRX = explode("|",substr($out,10));
 			return $this->arrayRX;
    	case "U0Z":
        	$in = "UploadReport():U0Z\0";
			$out = "";
			socket_write($this->socket, $in, strlen($in));
			$out = socket_read($this->socket, 1024);
			$this->arrayRZ = explode("|",substr($out,10));
 			return $this->arrayRZ;
       	default:
       		return false;
    }
}

//Funcion que imprime reporte Z
//Retorno: Boolean que indica si el comando fue aceptado
function PrintZReport()
{

 if($this->SendCmd("I0Z")){
	return true;
 }else{
	return false;
 }
}

//Funcion que imprime reportes X
//Retorno: Boolean que indica si el comando fue aceptado
function PrintXReport()
{

 if($this->SendCmd("I0X")){
	return true;
 }else{
	return false;
 }
}


}

?>
