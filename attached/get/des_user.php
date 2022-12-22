<?php
require '../../bh_conexion.php';
$link=conexion();

$user_id =  $_GET['a'];

$link->query("UPDATE bh_user SET TX_user_activo = '0' WHERE AI_user_id = '$user_id'")or die($link->error);


//  ######################### ANSWER   ###############


$qry_account =	$link->query("SELECT bh_user.AI_user_id, bh_user.TX_user_seudonimo, bh_user.TX_user_type, bh_tuser.TX_tuser_value, bh_user.TX_user_activo FROM (bh_user INNER JOIN bh_tuser ON bh_tuser.AI_tuser_id = bh_user.TX_user_type)")or die($link->error);

	while($rs_account=$qry_account->fetch_array()){ 
		$background = ($rs_account['TX_user_activo'] === '0') ? 'bg-warning': '';	?>
		<tr class="<?php echo $background ?>" onclick="open_popup('popup_upduser.php?a=<?php echo $rs_account['AI_user_id']; ?>','popup_upduser','450','420')">
			<td><?php echo $rs_account['TX_user_seudonimo']; ?></td>
			<td><?php echo $rs_account['TX_tuser_value']; ?></td>
			<td>
				<button type="button" class="btn btn-danger btn_squared_sm" onclick="des_user(<?php echo $rs_account['AI_user_id']; ?>)"><i class="fa fa-times"></i></button>
			</td>			
		</tr>
<?php 	} ?>
<?php
$link->close();
?>
