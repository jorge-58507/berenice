
<?php $qry_option = $link->query("SELECT AI_opcion_id, TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_option = array();
while ($rs_option = $qry_option->fetch_array()) {
	$raw_option[$rs_option['TX_opcion_titulo']] = $rs_option['TX_opcion_value'];
} ?>
<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
	<div id="container_btnconfig" class="col-xs-4 col-sm-2 col-md-2 col-lg-2 al_left">
		<button type="button" class="btn btn-danger" id="btn_exit" onclick="document.location='configuration.php'"><span class="glyphicon glyphicon-wrench" aria-hidden="true" title="Ir al configuraci&oacute;n"></span></button>
	</div>
	<div id="container_txtcopyright" class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
		<?php echo $raw_option['COPYRIGHT'].' '.date('Y'); ?> - Developed by: <span class="footer_sign">Jorge Salda&ntilde;a</span>
	</div>
	<div id="container_btnstart" class="col-xs-8 col-sm-2 col-md-2 col-lg-2" style="margin-top: -5px;">
		<div id="div_btn_exit">
			<button type="button" class="btn btn-danger" id="btn_exit" onclick="document.location='./index.php'">Salir</button>
		</div>
		<div id="div_btn_start">
			<button type="button" class="btn btn-danger btn-sm" onclick="document.location='./start.php'" style="font-size: 14px;"><span class="glyphicon glyphicon-home" aria-hidden="true" title="Ir al inicio"></span></button>
		</div>
		<div id="div_btn_notification">
			<?php include 'attached/php/inc_notification_footer.php' ?>
		</div>
	</div>
</div>