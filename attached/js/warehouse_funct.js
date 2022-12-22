// ############## CARRUSEL ################
window.onload=function() {
	var raw_finded = $("#carousel_container").find("div.carousel");
	var raw_carousel = [];
	var position = 0;
	for (var x in raw_finded) {
		if (raw_finded[x]['id'] != undefined) {
			raw_carousel.push(raw_finded[x]['id']);
		}
	}
	$("#go_left").on("click", function(){
		position = (position-- <= 0) ? raw_carousel.length-1 : position--;
		str_carousel = '';
		for (var y in raw_carousel) {
			if (y != position && $('#'+raw_carousel[y]).is(":visible")) {
				$('#'+raw_carousel[y]).hide(500);
			}
		}
		$("#"+raw_carousel[position]).show(1000);

	})
	$("#go_right").on("click", function(){
		position = (position++ >= raw_carousel.length-1) ? 0 : position++;
		str_carousel = '';
		for (var y in raw_carousel) {
			if (y != position && $('#'+raw_carousel[y]).is(":visible")) {
				$('#'+raw_carousel[y]).hide(500);
			}
		}
		$("#"+raw_carousel[position]).show(1000);
	})
}
// ######################    FIN CARRUSEL ####################
var raw_marked_ff = new Object();
var raw_ffid_dvid = new Object();
	function close_modal(){
		$("#mod_set_todeliver").hide("200")
	}
	function open_modal(){
		$("#txt_todeliver_quantity").val('')
		$("#mod_set_todeliver").show("200")
	}
	function set_hd_tab(str){
		$("#hd_tab").val(str);
	}
	function set_modal (array_id, codigo, descripcion, todeliver, AI_datoventa_id, tab){
		$("#span_todeliver_codigo").html(codigo);
		$("#span_todeliver_descripcion").html(replace_special_character(descripcion));
		$("#btn_updtodeliver").attr("name",array_id);
		$("#btn_updtodeliver").attr("alt",AI_datoventa_id);
		$("#hd_tab").val(tab);
		$("#span_todeliver_pending").text(todeliver);
		setTimeout(function(){	$("#txt_todeliver_quantity").focus();	},500)
		open_modal();
	}
	function upd_todeliver(field){
		array_id = field.name;
		datoventa_id = $("#btn_updtodeliver").attr("alt");
		var pendiente = $("#span_todeliver_pending").text();
		var tab = $("#hd_tab").val();
		if(tab != 'product'){
			if (parseFloat($("#txt_todeliver_quantity").val()) > pendiente) {
				set_bad_field('txt_todeliver_quantity');
				return false;
			}
		}
		if($("#txt_todeliver_quantity").val() === '' || parseFloat($("#txt_todeliver_quantity").val()) < 0.001 ) {
			set_bad_field('txt_todeliver_quantity');
			return false;
		} set_good_field('txt_todeliver_quantity');
		if (!val_intwdec($("#txt_todeliver_quantity").val())) {	return false;	}
		var cantidad = val_dec($("#txt_todeliver_quantity").val(),3,1,0);
		// var cantidad = val_intw2dec($("#txt_todeliver_quantity").val());
		raw_marked_ff[datoventa_id] = cantidad;
		$("#span_todeliver_"+tab+"_"+array_id).html(cantidad);
		close_modal();
	}
	function upd_todeliver_all(array_id,cantidad,datoventa_id){
		cantidad = val_dec(cantidad,3,1,0);
		var tab = $("#hd_tab").val();
		raw_marked_ff[datoventa_id] = cantidad;
		$("#span_todeliver_"+tab+"_"+array_id).html(cantidad);
	}
	function set_todeliverall(){   //  SOLO FACTURAF
		var raw_finded = $("#tbl_todeliver").find("span.badge_info");
		for (var i = 0; i < raw_finded.length; i++) {
			$('#'+raw_finded[i]['id']).click();
		}
	}


function generate_todeliver_facturaf(raw_todeliver_ff){
	var keys_todeliver = Object.keys(raw_todeliver_ff);
	content = '';
	for (var i = keys_todeliver.length-1; i >= 0; i--) {
		if(i > keys_todeliver.length-21){
			content += `
				<div class="col-xs-4 col-sm-3 col-md-6 col-lg-6 px_0 py_7 non_selectable">
					<div class="tarjeta" style="width: 18rem;" onclick="get_porentregar_facturaf(${keys_todeliver[i]})">
						<div class="tarjeta_body">
							<h4 class="tarjeta_header"		>${replace_special_character(raw_todeliver_ff[keys_todeliver[i]]['cliente']).substr(0,30)}</h4>
							<h6 class="tarjeta_subtitulo"	>${ (raw_todeliver_ff[keys_todeliver[i]]['flete'] === 0) ? 'Retira' : 'Enviar' }</h6>
							<p class="card-text"><strong>No.</strong> ${raw_todeliver_ff[keys_todeliver[i]]['facturaf']} <br/> ${convertir_formato_fecha(raw_todeliver_ff[keys_todeliver[i]]['fecha'])} (${raw_todeliver_ff[keys_todeliver[i]]['hora']})</p>
						</div>
					</div>
				</div>
			`;
		}
	}
	$("#container_leftside #container_tbl_todeliverff").hide(50);
	$("#container_leftside #container_tbl_todeliverff").html(content);
	$("#container_leftside #container_tbl_todeliverff").show(400);
}
function get_porentregar_facturaf(ff_id){
	$("#tbl_todeliver #content_dbody").html('<img src="attached/image/Eclipse-2.1s-200px.gif" width="50px" alt="">');
	$("#tbl_delivered #content_dbody").hide(100);
	$.ajax({	data: {"a" : ff_id, "z" : 'facturaf'},	type: "GET",	dataType: "text",	url: "attached/get/get_porentregar.php", })
	.done(function( data, textStatus, jqXHR ) {
		raw_data = JSON.parse(data);
		var caption = raw_data[2];
		raw_entregado = raw_data[1];
		raw_data = raw_data[0];
			var content_body = ``;
			for (var i in raw_data[ff_id]) {
				raw_data[ff_id][i]['entrega'] = 0;
				raw_add = [`'${i}','${raw_data[ff_id][i]['TX_producto_codigo']}','${raw_data[ff_id][i]['TX_datoventa_descripcion']}','${raw_data[ff_id][i]['cantidad']-raw_data[ff_id][i]['entregado']}','${raw_data[ff_id][i]['AI_datoventa_id']}','facturaf'`];
				content_body += `
				 	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding " onclick="set_modal(${raw_add})">${raw_data[ff_id][i]['TX_producto_codigo']}</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 cell no_padding " onclick="set_modal(${raw_add})">${replace_special_character(raw_data[ff_id][i]['TX_datoventa_descripcion'])}</div>
						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0 "><span id="span_pendiente_facturaf_${i}" class="badge badge_info" onclick="upd_todeliver_all('${i}','${raw_data[ff_id][i]['cantidad']-raw_data[ff_id][i]['entregado']}','${raw_data[ff_id][i]['AI_datoventa_id']}')">${raw_data[ff_id][i]['cantidad']-raw_data[ff_id][i]['entregado']}</span></div>
						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0 container_spantodeliver" onclick="set_modal(${raw_add})"><span id="span_todeliver_facturaf_${i}" class="badge badge_warning">${raw_data[ff_id][i]['entrega']}</span></div>
					</div>
				 `;
			 }
			//  $("#tbl_todeliver #content_dbody").hide(100);
			 $("#tbl_todeliver #content_dbody").html(content_body);
			 $("#tbl_todeliver #content_dbody").show(400);			 
			 $("#btn_todeliver_save").attr("name",ff_id);
			 $("#btn_todeliverall").css("display","block");
			 $("#todeliver_caption").html(caption);
			 for (var i in raw_marked_ff) {
				 delete raw_marked_ff[i];
			 }
// ######################    ENTREGADO      #########################
			 var content_entregado = '';
			 var index_entrega = '';
			 for (var i in raw_entregado[ff_id]) {
				 if(index_entrega != raw_entregado[ff_id][i]['AI_entrega_id']){
					 index_entrega = raw_entregado[ff_id][i]['AI_entrega_id'];
					 content_entregado += `
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding bb_1"	><strong>${convertir_formato_fecha(raw_entregado[ff_id][i]['TX_entrega_fecha'])} - (${raw_entregado[ff_id][i]['TX_entrega_hora']}) #${raw_entregado[ff_id][i]['AI_entrega_id']}</strong></div>
							<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"	>&nbsp;</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_entregado[ff_id][i]['TX_producto_codigo']}</div>
							<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"			>${replace_special_character(raw_entregado[ff_id][i]['TX_datoventa_descripcion'])}</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_entregado[ff_id][i]['TX_datoentrega_cantidad']}</div>
						</div>
					 `;
				 }else{
					 content_entregado += `
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_entregado[ff_id][i]['TX_producto_codigo']}</div>
							<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"			>${replace_special_character(raw_entregado[ff_id][i]['TX_datoventa_descripcion'])}</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${parseFloat(raw_entregado[ff_id][i]['TX_datoentrega_cantidad']).toFixed(2)}</div>
						</div>
					`;
				}
			}
			// $("#tbl_delivered #content_dbody").hide(100);
			$("#tbl_delivered #content_dbody").html(content_entregado);
			$("#tbl_delivered #content_dbody").show(400);
			// console.log($("#tbl_todeliver").offset().top);
			
			$('html, body').animate({
				scrollTop: $("#todeliver_caption").offset().top
			}, 300);
			$("#btn_todeliver_save").prop("disabled", false);
	 	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function save_todeliverff(ff_id){
	$('#btn_todeliver_save').attr('disabled', true);
	if( $.isEmptyObject(raw_marked_ff) ){		console.log("No ha Seleccionado Articulos"); $("#btn_todeliver_save").prop("disabled", false);	return false;	}
	$.ajax({	data: {"a" : ff_id, "b" : raw_marked_ff},	type: "GET",	dataType: "text",	url: "attached/get/save_porentregar.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 if(data === 'All Right'){
			 get_porentregar_facturaf(ff_id);
		 }
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}
function save_multiple_deliver(){
	var tab = $("#hd_tab").val();
	if( $.isEmptyObject(raw_marked_ff) ){		console.log("No ha Seleccionado Articulos"); return false;	}
	$.ajax({	data: {"a" : raw_marked_ff, "z" : tab},	type: "GET",	dataType: "text",	url: "attached/get/save_multiple_porentregar.php", })
	 .done(function( data, textStatus, jqXHR ) {

		 var tab = $("#hd_tab").val();
		 $.ajax({	data: {"a" : data, "z" : tab},	type: "GET",	dataType: "text",	url: "attached/get/get_porentregar.php", })
			.done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
				 data = JSON.parse(data);
				 if (tab === 'client') {
					 generate_todeliver_client(data);
				 } else {
					 generate_todeliver_product(data)
				 }
			 })
			.fail(function( jqXHR, textStatus, errorThrown ) {		});

		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function filter_todeliverff(raw_todeliver_ff,value){
	str = replace_regular_character(value);
	var raw_filtered = new Object();
	var patt = new RegExp(str);
	for (var i in raw_todeliver_ff) {
  	if(patt.test(raw_todeliver_ff[i]['facturaf'])){
			raw_filtered[i] = raw_todeliver_ff[i];
		}
	}
	var keys_filtered = Object.keys(raw_filtered);
	if (keys_filtered > 0) {
		generate_todeliver_facturaf(raw_filtered);
		return false;
	}
	for (var i in raw_todeliver_ff) {
  	if(patt.test(raw_todeliver_ff[i]['cliente'])){
			raw_filtered[i] = raw_todeliver_ff[i];
		}
	}
	generate_todeliver_facturaf(raw_filtered);
}

// ###################################  CLIENTE    ########################
// ###################################  CLIENTE    ########################
// ###################################  CLIENTE    ########################

function generate_todeliver_client(raw_todeliver){
	content = ''; var ff_id=''; var raw_dvid = [];
	var raw_todeliver_client = raw_todeliver[0];
	var raw_delivered_client = raw_todeliver[1];
	for(var i  in raw_todeliver_client) {
		if (ff_id != raw_todeliver_client[i]['AI_facturaf_id']) {
			raw_ffid_dvid[raw_todeliver_client[i]['AI_facturaf_id']] += raw_dvid;
			raw_dvid=[];
		}
		ff_id = raw_todeliver_client[i]['AI_facturaf_id'];
		raw_dvid.push(raw_todeliver_client[i]['AI_datoventa_id']);
		raw_add = [`'${i}','${raw_todeliver_client[i]['TX_producto_codigo']}','${raw_todeliver_client[i]['TX_datoventa_descripcion']}','${raw_todeliver_client[i]['TX_datoventa_cantidad']-raw_todeliver_client[i]['entregado']}','${raw_todeliver_client[i]['AI_datoventa_id']}','client'`];
		content += `
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 cell" onclick="set_modal(${raw_add})">
					${raw_todeliver_client[i]['TX_facturaf_numero']}
				</div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell" onclick="set_modal(${raw_add})">
					${convertir_formato_fecha(raw_todeliver_client[i]['TX_facturaf_fecha'])} (${raw_todeliver_client[i]['TX_facturaf_hora']})
				</div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell" onclick="set_modal(${raw_add})">
					${raw_todeliver_client[i]['TX_producto_codigo']}
				</div>
				<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 cell" onclick="set_modal(${raw_add})">
					${replace_special_character(raw_todeliver_client[i]['TX_datoventa_descripcion'])}
				</div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 px_7 py_0 cell">
					<span id="span_pendiente_client_${i}" class="badge badge_info" onclick="upd_todeliver_all('${i}','${raw_todeliver_client[i]['TX_datoventa_cantidad']-raw_todeliver_client[i]['entregado']}','${raw_todeliver_client[i]['AI_datoventa_id']}')">${raw_todeliver_client[i]['TX_datoventa_cantidad']-raw_todeliver_client[i]['entregado']}</span>
				</div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 cell px_7 py_0 container_spantodeliver" onclick="set_modal(${raw_add})">
					<span id="span_todeliver_client_${i}" class="badge badge_warning">${0}</span>
				</div>
			</div>
		`;
	}
	$("#tbl_todeliver_client #todeliver_client_dbody").hide(100);
	$("#tbl_todeliver_client #todeliver_client_dbody").html(content);
	$("#tbl_todeliver_client #todeliver_client_dbody").show(400);
	// ######################    ENTREGADO      #########################
 var content_entregado = '';
 var index_entrega = '';
 for (var i in raw_delivered_client) {
	 if(index_entrega != raw_delivered_client[i]['AI_entrega_id']){
		 index_entrega = raw_delivered_client[i]['AI_entrega_id'];
		 content_entregado += `
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding bb_1"	><strong>${convertir_formato_fecha(raw_delivered_client[i]['TX_entrega_fecha'])} - (${raw_delivered_client[i]['TX_entrega_hora']})</strong></div>
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"	>&nbsp;</div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_delivered_client[i]['TX_producto_codigo']}</div>
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"			>${replace_special_character(raw_delivered_client[i]['TX_datoventa_descripcion'])}</div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_delivered_client[i]['TX_datoentrega_cantidad']}</div>
			</div>
		 `;
	 }else{
		 content_entregado += `
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_delivered_client[i]['TX_producto_codigo']}</div>
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"			>${replace_special_character(raw_delivered_client[i]['TX_datoventa_descripcion'])}</div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${parseFloat(raw_delivered_client[i]['TX_datoentrega_cantidad']).toFixed(2)}</div>
			</div>
		 `;
	 }
 }
 $("#tbl_delivered_client #content_dbody").hide(100);
 $("#tbl_delivered_client #content_dbody").html(content_entregado);
 $("#tbl_delivered_client #content_dbody").show(400);
	for (var i in raw_marked_ff) {
		delete raw_marked_ff[i];
	}
}

// ###################################  PRODUCTO    ########################
// ###################################  PRODUCTO    ########################
// ###################################  PRODUCTO    ########################

function filter_product(){
	var str = $("#txt_filterproduct").val();
			str = url_replace_regular_character(str);
	$.ajax({	data: {"a" : str},	type: "GET",	dataType: "text",	url: "attached/get/filter_product_warehouse.php", })
	 .done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
			raw_data = JSON.parse(data);
			generate_tbl_product(raw_data);
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function generate_tbl_product(raw_data){
	var content_tblproduct = '';
	for (var x in raw_data[1]) {
		content_tblproduct += `
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1" onclick="get_porentregar_product(${raw_data[1][x]['AI_producto_id']})">
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell px_7 py_0"				>${raw_data[1][x]['TX_producto_codigo']}</div>
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"			>${replace_special_character(raw_data[1][x]['TX_producto_value'])}</div>
			</div>
		`;
	}
	$("#tbl_product #content_dbody").hide(100);
	$("#tbl_product #content_caption").html(raw_data[0]);
	$("#tbl_product #content_dbody").html(content_tblproduct);
	$("#tbl_product #content_dbody").show(400);
}

function get_porentregar_product(product_id){
	$.ajax({	data: {"a" : product_id, "z" : 'product'},	type: "GET",	dataType: "text",	url: "attached/get/get_porentregar.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 data = JSON.parse(data);
		 generate_todeliver_product(data,product_id)
	 	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function generate_todeliver_product(raw_data,product_id){
	raw_entregado = raw_data[1];
	raw_porentregar = raw_data[0];
		var content_body = ``;
		for (var i in raw_porentregar) {
			var por_entregar = raw_porentregar[i]['TX_datoventa_cantidad']-raw_porentregar[i]['entregado'];
			raw_add = [`'${i}','${raw_porentregar[i]['TX_producto_codigo']}','${raw_porentregar[i]['TX_datoventa_descripcion']}','${raw_porentregar[i]['TX_datoventa_cantidad']-raw_porentregar[i]['entregado']}','${raw_porentregar[i]['AI_datoventa_id']}','product'`];
			content_body += `
			 <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
				 <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding " onclick="set_modal(${raw_add})">${replace_special_character(raw_porentregar[i]['TX_cliente_nombre'])}</div>
				 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding " onclick="set_modal(${raw_add})">${raw_porentregar[i]['TX_facturaf_numero']}</div>
				 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding " onclick="set_modal(${raw_add})">${raw_porentregar[i]['TX_facturaf_fecha']}<br />(${raw_porentregar[i]['TX_facturaf_hora']})</div>
				 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0 "  ><span id="span_pendiente_facturaf_${i}" class="badge badge_info" onclick="upd_todeliver_all('${i}','${por_entregar}','${raw_porentregar[i]['AI_datoventa_id']}')"	>${por_entregar}</span></div>
				 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0 container_spantodeliver" onclick="set_modal(${raw_add})"><span id="span_todeliver_product_${i}" class="badge badge_warning">${raw_porentregar[i]['entrega']}</span></div>
			 </div>
			`;
		}
		$("#tbl_todeliver_product #content_dbody").hide(100);
		$("#tbl_todeliver_product #content_dbody").html(content_body);
		$("#tbl_todeliver_product #content_dbody").show(400);
		$("#btn_process_product").attr("name",product_id);
		for (var i in raw_marked_ff) {
			delete raw_marked_ff[i];
		}
// ######################    ENTREGADO      #########################
		var content_entregado = '';
		var index_entrega = '';
		for (var i in raw_entregado) {
		  if(index_entrega != raw_entregado[i]['AI_entrega_id']){
		 	 index_entrega = raw_entregado[i]['AI_entrega_id'];
		 	 content_entregado += `
		 		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
		 			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding bb_1"	><strong>${convertir_formato_fecha(raw_entregado[i]['TX_entrega_fecha'])} - (${raw_entregado[i]['TX_entrega_hora']})</strong></div>
		 			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"	>&nbsp;</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>${replace_special_character(raw_entregado[i]['TX_cliente_nombre'])}</div>
		 			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_entregado[i]['TX_facturaf_numero']}</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>${replace_special_character(raw_entregado[i]['TX_datoventa_descripcion'])}</div>
		 			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_entregado[i]['TX_datoentrega_cantidad']}</div>
		 		</div>
		 	 `;
		  }else{
		 	 content_entregado += `
		 		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>${replace_special_character(raw_entregado[i]['TX_cliente_nombre'])}</div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_entregado[i]['TX_facturaf_numero']}</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>${replace_special_character(raw_entregado[i]['TX_datoventa_descripcion'])}</div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_entregado[i]['TX_datoentrega_cantidad']}</div>
		 		</div>
		 	 `;
		  }
		}
		$("#tbl_todeliver_previus_product #content_dbody").hide(100);
		$("#tbl_todeliver_previus_product #content_dbody").html(content_entregado);
		$("#tbl_todeliver_previus_product #content_dbody").show(400);
	}



// ######################################################## DELIVERED FUNCTIONS   ########################

function filter_deliveredff(e){
	$("#txt_filter_deliveredff").select();
	var str_deliveredff = $("#txt_filter_deliveredff").val(); var limit = ($("input[name=r_limit]:checked").val());
	var fecha_i = $("#txt_date_initial_deliveredff").val(); 	var fecha_f = $("#txt_date_final_deliveredff").val();

	$.ajax({	data: {"a" : url_replace_regular_character(str_deliveredff), "b" : limit, "c" : fecha_i, "d" : fecha_f },	type: "GET",	dataType: "text",	url: "attached/get/filter_deliveredff.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 raw_data = JSON.parse(data);
		 generate_delivered_filteredff(raw_data)
	 	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function generate_delivered_filteredff(raw_entregado){
	var content_body = ``;
	for (var i in raw_entregado) {
		content_body += `
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding" onclick="get_entregado_facturaf(${raw_entregado[i]['AI_facturaf_id']});" style="cursor:pointer;">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding" style="background-color:rgba(0,0,0,.03)">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 cell no_padding al_center bb_1"><h4>${replace_special_character(raw_entregado[i]['TX_cliente_nombre'])}</h4></div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_000_2">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 cell no_padding " >No. ${raw_entregado[i]['TX_facturaf_numero']}</div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 cell no_padding " >${raw_entregado[i]['TX_facturaf_fecha']} (${raw_entregado[i]['TX_facturaf_hora']})</div>
			</div>
		</div>
		`;
	}
	$("#container_tbl_deliveredff ").hide(100);
	$("#container_tbl_deliveredff ").html(content_body);
	$("#container_tbl_deliveredff ").show(400);
}
function get_entregado_facturaf(ff_id){
	$.ajax({	data: {"a" : ff_id, "z" : 'facturaf'},	type: "GET",	dataType: "text",	url: "attached/get/get_entregado.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 raw_data = JSON.parse(data);
		 generate_delivered_facturaf(raw_data)
	 	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function generate_delivered_facturaf(raw_return){
	raw_datoventa = raw_return[0];
	raw_delivered = raw_return[1];
	var content_body = ``;
	for (var i in raw_datoventa) {
		content_body += `
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding " >${raw_datoventa[i]['TX_producto_codigo']}</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 cell no_padding " >${replace_special_character(raw_datoventa[i]['TX_datoventa_descripcion'])}</div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding al_center" >${raw_datoventa[i]['cantidad']}</div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding al_center" >${raw_datoventa[i]['entregado']}</div>
		</div>
		`;
	}
	$("#tbl_delivered_facturaf #content_dbody").hide(100);
	$("#tbl_delivered_facturaf #content_dbody").html(content_body);
	$("#tbl_delivered_facturaf #content_dbody").show(400);
	// ######################    ENTREGADO      #########################
	var content_entregado = '';
	var index_entrega = '';
	var raw_to_count = Object.keys(raw_delivered);
	$("#span_count_delivered").html('Entregas Previas ('+raw_to_count.length+')');
	if(raw_to_count.length > 0){
		for (var i in raw_delivered) {
		  if(index_entrega != raw_delivered[i]['AI_entrega_id']){
				index_entrega = raw_delivered[i]['AI_entrega_id'];
		 	 	content_entregado += `
			 		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
			 			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding bb_1"	><strong>${convertir_formato_fecha(raw_delivered[i]['TX_entrega_fecha'])} - (${raw_delivered[i]['TX_entrega_hora']})</strong></div>
			 			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"			>&nbsp;</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>${raw_delivered[i]['TX_producto_codigo']}</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 cell no_padding"			>${replace_special_character(raw_delivered[i]['TX_datoventa_descripcion'])}</div>
			 			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0 al_center"				>${raw_delivered[i]['TX_datoentrega_cantidad']}</div>
			 		</div>
			 	 `;
		  }else{
		 	 content_entregado += `
		 		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>${raw_delivered[i]['TX_producto_codigo']}</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 cell no_padding"			>${replace_special_character(raw_delivered[i]['TX_datoventa_descripcion'])}</div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0 al_center"				>${raw_delivered[i]['TX_datoentrega_cantidad']}</div>
		 		</div>
		 	 `;
		  }
		}
	}else{
		content_entregado += `
		 <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
			 <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>&nbsp;</div>
			 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"			>&nbsp;</div>
			 <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>&nbsp;</div>
			 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"			>&nbsp;</div>
		 </div>
		`;
	}

	$("#tbl_delivered_previus_facturaf #content_dbody").hide(100);
	$("#tbl_delivered_previus_facturaf #content_dbody").html(content_entregado);
	$("#tbl_delivered_previus_facturaf #content_dbody").show(400);
}
function generate_delivered_client(raw_data){
		content = ''; 	var ff_id=''; 	var raw_dvid = [];
		raw_datoventa = raw_data[0];
		raw_delivered = raw_data[1];
		for(var i  in raw_datoventa) {
			if (ff_id != raw_datoventa[i]['AI_facturaf_id']) {
				raw_ffid_dvid[raw_datoventa[i]['AI_facturaf_id']] += raw_dvid;
				raw_dvid=[];
			}
			ff_id = raw_datoventa[i]['AI_facturaf_id'];
			raw_dvid.push(raw_datoventa[i]['AI_datoventa_id']);
			content += `
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 cell">${raw_datoventa[i]['TX_facturaf_numero']}</div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell">${convertir_formato_fecha(raw_datoventa[i]['TX_facturaf_fecha'])} (${raw_datoventa[i]['TX_facturaf_hora']})</div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell al_center">${raw_datoventa[i]['TX_producto_codigo']}</div>
					<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 cell">${replace_special_character(raw_datoventa[i]['TX_datoventa_descripcion'])}</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 cell px_7 py_0 al_center">${raw_datoventa[i]['TX_datoventa_cantidad']}</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 cell px_7 py_0 al_center">${raw_datoventa[i]['entregado']}</div>
				</div>
			`;
		}
		$("#tbl_delivered_client #delivered_client_dbody").hide(100);
		$("#tbl_delivered_client #delivered_client_dbody").html(content);
		$("#tbl_delivered_client #delivered_client_dbody").show(400);
		// ######################    ENTREGADO      #########################
		var content_entregado = '';
	  var index_entrega = '';
	  for (var i in raw_delivered) {
	 	 if(index_entrega != raw_delivered[i]['AI_entrega_id']){
	 		 index_entrega = raw_delivered[i]['AI_entrega_id'];
	 		 content_entregado += `
	 			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
	 				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding bb_1"	><strong>${convertir_formato_fecha(raw_delivered[i]['TX_entrega_fecha'])} - (${raw_delivered[i]['TX_entrega_hora']})</strong></div>
	 				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"	>&nbsp;</div>
	 				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_delivered[i]['TX_producto_codigo']}</div>
	 				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"			>${replace_special_character(raw_delivered[i]['TX_datoventa_descripcion'])}</div>
	 				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_delivered[i]['TX_datoentrega_cantidad']}</div>
	 			</div>
	 		 `;
	 	 }else{
	 		 content_entregado += `
	 			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
	 				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_delivered[i]['TX_producto_codigo']}</div>
	 				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"			>${replace_special_character(raw_delivered[i]['TX_datoventa_descripcion'])}</div>
	 				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${parseFloat(raw_delivered[i]['TX_datoentrega_cantidad']).toFixed(2)}</div>
	 			</div>
	 		 `;
	 	 }
	  }
	  $("#tbl_delivered_previus_client #content_dbody").hide(100);
	  $("#tbl_delivered_previus_client #content_dbody").html(content_entregado);
	  $("#tbl_delivered_previus_client #content_dbody").show(400);
}
function filter_product_delivered(){
	var str = $("#txt_filterproduct_delivered").val();
			str = url_replace_regular_character(str);
	$.ajax({	data: {"a" : str},	type: "GET",	dataType: "text",	url: "attached/get/filter_product_warehouse.php", })
	 .done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
			raw_data = JSON.parse(data);
			generate_tbl_product_delivered(raw_data);
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}
function generate_tbl_product_delivered (raw_data){
	var content_tblproduct = '';
	for (var x in raw_data[1]) {
		content_tblproduct += `
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1" onclick="get_entregado_product(${raw_data[1][x]['AI_producto_id']})">
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell px_7 py_0"				>${raw_data[1][x]['TX_producto_codigo']}</div>
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"			>${replace_special_character(raw_data[1][x]['TX_producto_value'])}</div>
			</div>
		`;
	}
	$("#tbl_product_delivered #content_dbody").hide(100);
	$("#tbl_product_delivered #content_caption").html(raw_data[0]);
	$("#tbl_product_delivered #content_dbody").html(content_tblproduct);
	$("#tbl_product_delivered #content_dbody").show(400);
}
function get_entregado_product(producto_id){
	$.ajax({	data: {"a" : producto_id, "z" : 'product'},	type: "GET",	dataType: "text",	url: "attached/get/get_entregado.php", })
	 .done(function( data, textStatus, jqXHR ) {
			raw_data = JSON.parse(data);
			generate_delivered_product(raw_data)
	 	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function generate_delivered_product(raw_data){
	raw_datoventa = raw_data[0];
	raw_delivered = raw_data[1];
	var content_body = ``;
	for (var i in raw_datoventa) {
		content_body += `
		 <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
			 <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding " >${replace_special_character(raw_datoventa[i]['TX_cliente_nombre'])}</div>
			 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding " >${raw_datoventa[i]['TX_facturaf_numero']}</div>
			 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding al_center" >${raw_datoventa[i]['TX_facturaf_fecha']}<br />(${raw_datoventa[i]['TX_facturaf_hora']})</div>
			 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0 al_center"  >${raw_datoventa[i]['TX_datoventa_cantidad']}</div>
			 <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0 al_center"  >${raw_datoventa[i]['entregado']}</div>
		 </div>
		`;
	}
	$("#tbl_delivered_product #content_dbody").hide(100);
	$("#tbl_delivered_product #content_dbody").html(content_body);
	$("#tbl_delivered_product #content_dbody").show(400);
	// ######################    ENTREGADO      #########################
	var content_entregado = '';
	var index_entrega = '';
	for (var i in raw_delivered) {
		if(index_entrega != raw_delivered[i]['AI_entrega_id']){
		 index_entrega = raw_delivered[i]['AI_entrega_id'];
		 content_entregado += `
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding bb_1"	><strong>${convertir_formato_fecha(raw_delivered[i]['TX_entrega_fecha'])} - (${raw_delivered[i]['TX_entrega_hora']})</strong></div>
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell no_padding"	>&nbsp;</div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>${replace_special_character(raw_delivered[i]['TX_cliente_nombre'])}</div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_delivered[i]['TX_facturaf_numero']}</div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>${replace_special_character(raw_delivered[i]['TX_datoventa_descripcion'])}</div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_delivered[i]['TX_datoentrega_cantidad']}</div>
			</div>
		 `;
		}else{
		 content_entregado += `
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding bb_1">
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>${replace_special_character(raw_delivered[i]['TX_cliente_nombre'])}</div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_delivered[i]['TX_facturaf_numero']}</div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell no_padding"			>${replace_special_character(raw_delivered[i]['TX_datoventa_descripcion'])}</div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell px_7 py_0"				>${raw_delivered[i]['TX_datoentrega_cantidad']}</div>
			</div>
		 `;
		}
	}
	$("#tbl_delivered_previus_product #content_dbody").hide(100);
	$("#tbl_delivered_previus_product #content_dbody").html(content_entregado);
	$("#tbl_delivered_previus_product #content_dbody").show(400);
}
