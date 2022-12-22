<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';

$account_id=$_GET['a'];
$proveedor_id=$_GET['b'];

			$bh_del="DELETE FROM bh_banconumero WHERE AI_banconumero_id = '$account_id'";
			$link->query($bh_del) or die($link->error);

// ############################# ANSWER ####################

$qry_bank_account = $link->query("SELECT bh_banco.TX_banco_value, bh_user.TX_user_seudonimo, bh_banconumero.AI_banconumero_id, bh_banconumero.TX_banconumero_value FROM (((bh_banconumero INNER JOIN bh_banco ON bh_banco.AI_banco_id = bh_banconumero.banconumero_AI_banco_id) INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_banconumero.banconumero_AI_proveedor_id) INNER JOIN bh_user ON bh_user.AI_user_id = bh_banconumero.banconumero_AI_user_id) WHERE bh_proveedor.AI_proveedor_id = '$proveedor_id'") or die($link->error);

 while($rs_bank_account = $qry_bank_account->fetch_array()){ ?>
		<tr title="<?php echo $rs_bank_account['TX_user_seudonimo'] ?>">
			<td><?php echo $rs_bank_account['TX_banco_value'] ?></td>
			<td><?php echo $rs_bank_account['TX_banconumero_value'] ?></td>
			<td class="al_center"><button type="button" class="btn btn-danger btn-sm" onclick="del_account_number('<?php echo $rs_bank_account['AI_banconumero_id'] ?>')"><i class="fa fa-times" aria-hidden="true"></i></button</td>
		</tr>
<?php } ?>


?>
