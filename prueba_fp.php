<?php
require_once "attached/php/TfhkaPHPTCP.php";

// $new_ip=($_SERVER['REMOTE_ADDR']);
// echo $new_ip;
// $address=$new_ip;
$address='192.168.0.103';
$service_port=8090;
$itObj = new Tfhka($address,$service_port);

$cmd='chr<68>';
$bol = $itObj->SendCmd($cmd);
echo $bol;
// echo "<br />Raw: ".json_encode($raw);
?>
