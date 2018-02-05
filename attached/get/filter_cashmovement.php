<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];
$value=str_replace("/","-",$value);
//echo $value."<br>";
$fecha=date('Y-m-d', strtotime($value));
$qry_cashmovement = mysql_query("SELECT bh_efectivo.TX_efectivo_fecha, bh_efectivo.TX_efectivo_tipo, bh_efectivo.TX_efectivo_motivo, bh_efectivo.TX_efectivo_monto, bh_user.TX_user_seudonimo, bh_efectivo.AI_efectivo_id
FROM (bh_efectivo
INNER JOIN bh_user ON bh_efectivo.efectivo_AI_user_id = bh_user.AI_user_id)
WHERE TX_efectivo_fecha = '$fecha' ORDER BY TX_efectivo_tipo, AI_efectivo_id ASC");
?>
<table id="tbl_cashmovement" class="table table-bordered table-condensed table-striped">
    <caption class="caption">Movimientos del: <?php echo date('d-m-Y',strtotime($value)); ?></caption>
    <thead class="bg-primary">
      <tr>
        <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Tipo</th>
        <th class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Motivo</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Monto</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
      </tr>
    </thead>
    <tfoot class="bg-primary"><tr><td></td><td></td><td></td><td></td></tr></tfoot>
    <tbody>
    <?php
	if($nr_cashmovement=mysql_num_rows($qry_cashmovement) > 0){
	while($rs_cashmovement = mysql_fetch_array($qry_cashmovement)){ ?>
    <tr title="<?php echo $rs_cashmovement['TX_user_seudonimo']; ?>">
      <td><?php echo $rs_cashmovement['TX_efectivo_tipo']; ?></td>
      <td><?php echo $rs_cashmovement['TX_efectivo_motivo']; ?></td>
      <td><?php echo substr($rs_cashmovement['TX_efectivo_monto'],0,20); ?></td>
      <td>
        <button type="button" class="btn btn-info btn-xs" name="<?php echo $rs_cashmovement['AI_efectivo_id'] ?>" onclick="print_html('print_cashmovement.php?a='+this.name)" ><i class="fa fa-print fa-2x" aria-hidden="true"></i></button>
      </td>
    </tr>
    <?php } }else{ ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
    </tr>
	<?php }?>
    </tbody>
    </table>
