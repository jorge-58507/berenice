
<?php $qry_option = $link->query("SELECT AI_opcion_id, TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_option = array();
while ($rs_option = $qry_option->fetch_array()) {
	$raw_option[$rs_option['TX_opcion_titulo']] = $rs_option['TX_opcion_value'];
} ?>
<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
	<div id="container_txtcopyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<?php echo $raw_option['COPYRIGHT']; ?> - Designed by: <span class="footer_sign">Jorge Salda&ntilde;a</span>
	</div>
</div>
