<?php
require '../../bh_con.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$name=$_GET['a'];
$percent=$_GET['b'];
$categoria=$_GET['c'];

mysql_query("INSERT INTO bh_impuesto (TX_impuesto_nombre,TX_impuesto_value,TX_impuesto_categoria) VALUES ('$name','$percent','$categoria')")or die(mysql_error());

?>
	<?php $qry_taxes=mysql_query("SELECT AI_impuesto_id, TX_impuesto_nombre, TX_impuesto_value, TX_impuesto_categoria FROM bh_impuesto"); ?>
	<table id="tbl_tax" class="table table-bordered table-condensed table-striped">
	<thead class="bg-primary">
	<tr>
		<th></th><th></th><th></th><th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	while ($rs_taxes=mysql_fetch_array($qry_taxes)) {
	?>
		<tr>
			<td><?php echo $rs_taxes['1']; ?></td>
			<td><?php echo $rs_taxes['2']; ?></td>
			<td><?php echo $rs_taxes['3']; ?></td>
            <td>
	<button type="button" id="btn_del"  name="<?php echo $rs_taxes['0']; ?>" class="btn btn-danger btn-sm" onclick="del_tax(this.name);"><i class="fa fa-times" aria-hidden="true"></i></button>
            </td>
		</tr>
	<?php
	}
	?>
	</tbody>
	<tfoot class="bg-primary">
		<tr>
			<td></td><td></td><td></td><td></td>
		</tr>
	</tfoot>
	</table
>