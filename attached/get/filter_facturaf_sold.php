<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['a'];
$limit=$_GET['b'];
$date_i=date('Y-m-d',strtotime($_GET['c']));
$date_f=date('Y-m-d',strtotime($_GET['d']));

if($limit == ""){	$line_limit="";	}else{	$line_limit= " LIMIT ".$limit;	}
if (!empty($date_i) && !empty($date_f)) {
	$line_date = " AND TX_facturaf_fecha >=	'$date_i' AND TX_facturaf_fecha <= '$date_f'";
}

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_numero,  bh_facturaf.TX_facturaf_total, bh_cliente.TX_cliente_nombre
FROM (bh_facturaf
	INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
WHERE";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaf=$txt_facturaf." bh_facturaf.TX_facturaf_numero LIKE '%{$arr_value[$it]}%'".$line_date;
	}else{
$txt_facturaf=$txt_facturaf." bh_facturaf.TX_facturaf_numero LIKE '%{$arr_value[$it]}%' AND ";
	}
}


$txt_facturaf=$txt_facturaf." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaf=$txt_facturaf." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%'".$line_date;
	}else{
$txt_facturaf=$txt_facturaf." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturaf .= " ORDER BY TX_facturaf_fecha DESC".$line_limit;

$qry_facturaf=$link->query($txt_facturaf)or die(mysql_error());
$nr_facturaf=$qry_facturaf->num_rows;

		if($nr_facturaf > 0){
		while($rs_facturaf=$qry_facturaf->fetch_array()){ ?>
        <tr onclick="filter_productbysale('<?php echo $rs_facturaf['AI_facturaf_id']; ?>');">
        <td><?php
		$prefecha=strtotime($rs_facturaf['TX_facturaf_fecha']);
		echo $fecha = date('d-m-Y',$prefecha);
		 ?></td>
        <td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
        <td><?php echo $rs_facturaf['TX_cliente_nombre']; ?></td>
        <td></td>
        <td><?php echo number_format($rs_facturaf['TX_facturaf_total'],4); ?></td>
        </tr>
<?php }
			}else{?>
        <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        </tr>
<?php } 	?>
