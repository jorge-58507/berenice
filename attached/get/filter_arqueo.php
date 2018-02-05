<?php
require '../../bh_conexion.php';
$link = conexion();

function edit_quote($str){
$pat = array("\"", "'", "º", "laremun");
$rep = array("''", "\'", "°", "#");
return $n_str = str_replace($pat, $rep, $str);
}

$value=edit_quote($_GET['a']);
$fecha=date('Y-m-d',strtotime($_GET['b']));
$txt_arqueo="SELECT bh_arqueo.AI_arqueo_id, bh_arqueo.TX_arqueo_fecha, bh_arqueo.TX_arqueo_hora, bh_user.TX_user_seudonimo FROM (bh_arqueo INNER JOIN bh_user ON bh_user.AI_user_id = bh_arqueo.arqueo_AI_user_id) WHERE bh_arqueo.TX_arqueo_fecha = '$fecha' AND bh_user.TX_user_seudonimo LIKE '%$value%'";
$qry_cashregister=$link->query($txt_arqueo)or die($link->error);
while ($rs_cashregister = $qry_cashregister->fetch_array()) {
?>
<tr>
	<td><?php echo $rs_cashregister['TX_arqueo_fecha'];?></td>
	<td><?php echo $rs_cashregister['TX_arqueo_hora']; ?></td>
	<td><?php echo $rs_cashregister['TX_user_seudonimo']; ?></td>
	<td>
	<button type="button" id="btn_print" onclick="print_html('print_cashregister.php?a=<?php echo $rs_cashregister['AI_arqueo_id']; ?>')" class="btn btn-info btn-sm" >
	<i class="fa fa-print" aria-hidden="true"></i>
	</button>
	</td>
</tr>
<?php
}
?>
