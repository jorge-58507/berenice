<?php
require '../../bh_conexion.php';
$link = conexion();

$value = $_GET['a'];
$qry_account =	$link->query("SELECT bh_user.AI_user_id, bh_user.TX_user_seudonimo, bh_user.TX_user_type, bh_tuser.TX_tuser_value FROM (bh_user INNER JOIN bh_tuser ON bh_tuser.AI_tuser_id = bh_user.TX_user_type) WHERE TX_user_seudonimo LIKE '%$value%'")or die($link->error);

		while($rs_account=$qry_account->fetch_array()){ ?>
		<tr onclick="open_popup('popup_upduser.php?a=<?php echo $rs_account['AI_user_id']; ?>','popup_upduser','450','420')">
			<td><?php echo $rs_account['TX_user_seudonimo']; ?></td>
			<td><?php echo $rs_account['TX_tuser_value']; ?></td>
		</tr>
<?php 	} ?>
