<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_sale.php';
?>
<?php
$fecha_actual=date('Y-m-d');
$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status,
bh_facturaf.TX_facturaf_numero, bh_facturaf.AI_facturaf_id
FROM ((bh_facturaventa
INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_facturaf ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
WHERE bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id AND TX_facturaventa_fecha = '$fecha_actual'";
switch ($_COOKIE['coo_tuser']) {
case "1":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaventa_fecha, TX_facturaventa_numero DESC LIMIT 10";
break;
case "2":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaventa_fecha, TX_facturaventa_numero  DESC LIMIT 10";
break;
case "4":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaventa_fecha, TX_facturaventa_numero  DESC LIMIT 10";
break;
default:
$txt_facturaventa=$txt_facturaventa." AND
bh_facturaventa.facturaventa_AI_user_id = '{$_COOKIE['coo_iuser']}' ORDER BY TX_facturaventa_status, TX_facturaventa_numero DESC LIMIT 10";
break;
}

$qry_facturaventa=mysql_query($txt_facturaventa);
$rs_facturaventa=mysql_fetch_assoc($qry_facturaventa);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	$("#txt_filterfacturaventa").keyup(function(){
		if($("#txt_date_initial,#txt_date_final").val() == ""){
			return false;
		}
		filter_facturaventa();
	});
  	$("#sel_filterfacturaventa").change(function(){
		filter_facturaventa();
	});

  $( function() {
    var dateFormat = "dd-mm-yy",
      from = $( "#txt_date_initial" )
        .datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 2
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
        }),
      to = $( "#txt_date_final" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 2
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
      });

    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }

      return date;
    }
  });
  $("#clear_date_initial").click(function(){
	 $("#txt_date_initial").val("");
  });
  $("#clear_date_final").click(function(){
	 $("#txt_date_final").val("");
  });


});

</script>

</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
		<div id="logo" ></div>
	</div>
</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<form action="sale.php" method="post" name="form_sell"  id="form_sell">

    <div id="container_filterfacturaventa" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
        <label for="txt_filterfacturaventa">Buscar</label>
        <input type="text" id="txt_filterfacturaventa" class="form-control" />
    </div>
    <div id="container_filterfacturaventa" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
        <label for="sel_filterfacturaventa">Status</label>
        <select id="sel_filterfacturaventa" class="form-control">
        	<option value="">SELECCIONAR</option>
            <option value="ACTIVA">ACTIVA</option>
        	<option value="FACTURADA">FACTURADA</option>
        	<option value="CANCELADA">CANCELADA</option>
        </select>
    </div>
    <div id="container_txtdateinitial" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="txt_date_initial">F. Inicio
        <button type="button" id="clear_date_initial" class="btn btn-danger btn-xs"><strong>!</strong></button></label>
        <input type="text" id="txt_date_initial" class="form-control" readonly="readonly" />
    </div>
    <div id="container_txtdatefinal" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="txt_date_final">F. Final
        <button type="button" id="clear_date_final" class="btn btn-danger btn-xs"><strong>!</strong></button></label>
        <input type="text" id="txt_date_final" class="form-control" readonly="readonly" />
    </div>
	<div id="container_tblfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<thead class="bg-primary">
    	<tr>
    	  <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
        <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Cliente</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº Cotizacion</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Factura Asociada</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Metodo de P.</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Monto</th>
      </tr>
    </thead>
    <tbody>
    <?php if($nr_facturaventa=mysql_num_rows($qry_facturaventa)>0){ ?>
    <?php
	$raw_facturaf=array();
	$total_total=0;
	$total_efectivo=0; $total_tarjeta_credito=0; $total_tarjeta_debito=0; $total_cheque=0; $total_credito=0; $total_notadc=0;
	do{
	?>
    <tr>
        <td><?php
		$time=strtotime($rs_facturaventa['TX_facturaventa_fecha']);
		$date=date('d-m-Y',$time);
		echo $date; ?></td>
        <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_total']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaf_numero']; ?></td>
        <td>
        <?php
		$answer = array_search($rs_facturaventa['AI_facturaf_id'], $raw_facturaf);
			if($answer >= -1){
				$print=0;
			}else{
				$print=1;
				$raw_facturaf[]="{$rs_facturaventa['AI_facturaf_id']}";
			}

		if($print==1){
			$qry_datopago=mysql_query("SELECT TX_datopago_monto, datopago_AI_metododepago_id, bh_metododepago.TX_metododepago_value
			FROM ((bh_datopago
			INNER JOIN bh_facturaf ON bh_datopago.datopago_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
			INNER JOIN bh_metododepago ON bh_datopago.datopago_AI_metododepago_id = bh_metododepago.AI_metododepago_id)
			WHERE bh_facturaf.AI_facturaf_id = '{$rs_facturaventa['AI_facturaf_id']}'");
			$raw_monto=array();
			$i=0;
			while($rs_datopago=mysql_fetch_array($qry_datopago)){
			switch($rs_datopago['datopago_AI_metododepago_id']){
				case '1':	$color='#67b847';	$total_efectivo += $rs_datopago['TX_datopago_monto'];	break;
				case '3':	$color='#e9ca2f';	$total_tarjeta_credito += $rs_datopago['TX_datopago_monto'];	break;
				case '4':	$color='#e9ca2f';	$total_tarjeta_debito += $rs_datopago['TX_datopago_monto'];	break;
				case '5':	$color='#e9ca2f';	$total_credito += $rs_datopago['TX_datopago_monto'];	break;
				case '2':	$color='#57afdb';	$total_cheque += $rs_datopago['TX_datopago_monto'];	break;
				case '7':	$color='#EFA63F';	$total_notadc += $rs_datopago['TX_datopago_monto'];	break;
			}
			echo "<font color='{$color}'>".$rs_datopago['TX_metododepago_value']."</font><br />";
			$raw_monto[$i]=$rs_datopago['TX_datopago_monto'];
			$i++;
			}
		}
		?>
        </td>
        <td>
        <?php
		if($print==1){
			foreach($raw_monto as $monto){
			echo $monto."<br />";
			}
		}
		?>
        </td>
    </tr>
    <?php
	}while($rs_facturaventa=mysql_fetch_assoc($qry_facturaventa));
	$total_total = $total_cheque+$total_credito+$total_efectivo+$total_notadc+$total_tarjeta_credito+$total_tarjeta_debito;
    ?>
    <?php }else{ ?>
    <tr>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <?php } ?>
    </tbody>
    <tfoot class="bg-primary">
    	<tr>
        	<td colspan="7">
            <table id="tbl_total" class="table-condensed table-bordered" style="width:100%">
			<tr>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Efectivo:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_efectivo,2);
				};?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Cheque:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_cheque,2);
				}?>
              </td>
							<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>TDC:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_tarjeta_credito,2);
				}
				?>
							</td>
							<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>TDD:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_tarjeta_debito,2);
				}
				?>
              </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Cr&eacute;dito:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_credito,2);
				}?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Nota de C.:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_notadc,2);
				}?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Total:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_total,2);
				}?>
                </td>
            </tr>
			</table>
            </td>
		</tr>
    </tfoot>
</table>

	</div>
</form>
</div>

<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>
</div>

</body>
</html>
