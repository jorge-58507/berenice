// JavaScript Document

function open_product2sell(id){
	open_popup('popup_product2sell.php?a='+id+'', 'popup_product2sell','425','450');
}

function set_txtfilterclient(field){
	document.getElementById("txt_filterclient").value = field.text;
	document.getElementById("txt_filterclient").alt = field.value;
	$.ajax({	data: { "a" : field.value },	type: "GET",	dataType: "JSON",	url: "attached/get/get_client_info.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 content = '<strong>Nombre:</strong> '+data[field.value]['nombre']+' <strong>Tlf.</strong> '+data[field.value]['telefono']+' <strong>Dir.</strong> '+data[field.value]['direccion'].substr(0,20)+' <strong>RUC:</strong> '+data[field.value]['cif'];
		 fire_recall('container_client_recall', content)
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function upd_unidadesnuevaventa(product_id){
	var new_quantity = prompt("Ingrese la cantidad:");
	new_quantity = val_intw2dec(new_quantity);
	upd_quantityproduct2sell(product_id,new_quantity);
}
function upd_precionuevaventa(product_id){
	$.ajax({ data: {"a" : "1"}, type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	if(data[0][0] != ""){
			var new_price = prompt("Ingrese el Nvo. Precio:");
			new_price = val_intw2dec(new_price);
			new_price = parseFloat(new_price);
			upd_priceproduct2sell(product_id,new_price);
		}	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
//  #########################  FUNCIONES PARA LA EDICION DEL TEXT NUEVAVENTA ##################
function plus_product2nuevaventa(product_id,precio,descuento,itbm,activo,cantidad,medida,promotion){
	close_popup();
	$.ajax({	data: {"a" : product_id, "b" : precio, "c" : descuento, "d" : itbm, "e" : activo, "f" : cantidad, "g" : medida, "h" : promotion, "z" : 'plus' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		if(data){
			if(data === 'failed'){ alert("Consulte al administrador del sistema"); return false;}
			data = JSON.parse(data);
			generate_tbl_nuevaventa(data,activo);
			activo=activo.replace("_sale","");
			$("#btn_guardar, #btn_facturar").css("display","initial");
			$("#txt_filterproduct").select();
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});

}

function upd_descripcion_nuevaventa(key_nuevaventa,descripcion){
	descripcion = replace_special_character(descripcion);
	$.ajax({	data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {
		if (data[0][0] === '') {
			return false
		}else{
			var n_description = prompt("Introduzca la nueva descripcion",descripcion);
			if (n_description.length > 100) {
				alert("La descripcion en muy larga");
				descripcion = replace_regular_character(descripcion);
				upd_descripcion_nuevaventa(key_nuevaventa,descripcion);
			}else{
				var activo = $(".tab-pane.active").attr("id");
				n_description = n_description.toUpperCase();
				n_description = url_replace_regular_character(n_description);
				$.ajax({	data: {"a" : key_nuevaventa, "b" : n_description, "c" : activo, "d" : 'descripcion', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
				.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
					if(data){
					data = JSON.parse(data);
					generate_tbl_nuevaventa(data,activo);
				}
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
			}
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function upd_precio_nuevaventa(key_nuevaventa){
	$.ajax({ data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	if(data[0][0] != ""){
			var activo = $(".tab-pane.active").attr("id");
			var new_price = prompt("Ingrese el Nvo. Precio:");
			if (new_price === '' || isNaN(new_price)) {
				return false;
			}
			new_price = val_intw2dec(new_price);
			new_price = parseFloat(new_price);
			$.ajax({	data: {"a" : key_nuevaventa, "b" : new_price.toFixed(2), "c" : activo, "d" : 'precio', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
			.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
				if(data){
					data = JSON.parse(data);
					generate_tbl_nuevaventa(data,activo);
				}
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		}	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});

}
function upd_unidades_nuevaventa(key_nuevaventa){
	var activo = $(".tab-pane.active").attr("id");
	var new_quantity = prompt("Ingrese la cantidad:");
	if (new_quantity === '' || isNaN(new_quantity) || new_quantity < 0.001) {
		return false;
	}
	new_quantity = val_intw2dec(new_quantity);
	new_quantity = parseFloat(new_quantity);
	$.ajax({	data: {"a" : key_nuevaventa, "b" : new_quantity.toFixed(2), "c" : activo, "d" : 'cantidad', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		if(data){
			data = JSON.parse(data);
			generate_tbl_nuevaventa(data,activo);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function del_nuevaventa(key_nuevaventa){
	var activo = $(".tab-pane.active").attr("id");
	$.ajax({	data: {"a" : key_nuevaventa, "b" : activo, "z" : 'del' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		if(data){
			data = JSON.parse(data);
			generate_tbl_nuevaventa(data,activo);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function refresh_tblproduct2sale(){
	var activo = $(".tab-pane.active").attr("id");
	$.ajax({	data: {"a" : activo, "z" : 'reload' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		if(data){
			data = JSON.parse(data);
			generate_tbl_nuevaventa(data,activo);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}


function close_popup(){
	if(popup){
		popup.close();
	}
}

/*########################## FUNCTIONS FOR NEW_PAYDESK   #############################*/

function open_product2addpaydesk(id, facturaventa_id){
	popup = window.open("popup_product2addpaydesk.php?a="+id+"&b="+facturaventa_id+"", "popup_product2sell", 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=420');
}

// #############################

function  unset_filterclient(e){
	var activo = $(".tab-pane.active").attr("id");
			activo = activo.replace("_sale","");
	if (e.which === 13) {
		$("#btn_addclient_"+activo).click();
	}else{
		$( "#txt_filterclient_"+activo).prop("alt","");
	}

	$( function() {
		$( "#txt_filterclient_"+activo).autocomplete({
			source: "attached/get/filter_client_sell.php",
			minLength: 2,
			select: function( event, ui ) {
				var n_val = ui.item.value;
				raw_n_val = n_val.split(" | Dir:");
				ui.item.value = raw_n_val[0];
				$("#txt_filterclient_"+activo).prop('alt', ui.item.id);
				$("#txt_filterclient_"+activo).prop('title', 'Completa las ventas '+ui.item.asiduo+" de las veces");
				content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>RUC:</strong> '+ui.item.ruc+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20);
				fire_recall('container_client_recall_'+activo, content)
				
				var ruc = ui.item.ruc;
				if (ruc.charAt(0) == 0 && ruc.charAt(1) == '-' || ruc.charAt(0) == 0 && ruc.charAt(1) == 0) {
					shot_snackbar('¡Debe Acomodar la Cédula','bg_danger');
					add_client();
				}
			}
		});
	});
}

function add_client(){
	var activo = $(".tab-pane.active").attr("id");
			activo = activo.replace("_sale","");
	var name = $("#txt_filterclient_"+activo).val();
			name = url_replace_regular_character(name);
	if($("#txt_filterclient_"+activo).prop('alt') != ""){
		if($("#txt_filterclient_"+activo).prop('alt') === "1"){
			open_popup('popup_addclient.php?a='+name,'popup_addclient','425','460')
		}else{
			open_popup('popup_updclient.php?a=' + $("#txt_filterclient_" + activo).prop('alt'), 'popup_updclient', '425','507')
		}
	}else{
		open_popup('popup_addclient.php?a=' + name, 'popup_addclient', '425','460')
	}
}
function calculate_ceiling(){
	var metraje = $("#txt_metraje").val();
	var raw_ceiling = new Array();
	// ############     SIM     ################
	// 2X2
		raw_ceiling.push({"lamina" : 2.82*metraje, "t12" : 0.26*metraje, "t4" : 1.41*metraje, "angulo" : 0.33*metraje, "clavo" : 2*metraje});
	// 2x4
		raw_ceiling.push({"lamina" : 1.41*metraje, "t12" : 0.26*metraje, "t4" : 1.41*metraje, "angulo" : 0.33*metraje, "clavo" : 2*metraje});
		contenido_tbody = `
		<tr>
			<td class="al_center">Laminas:</td>
			<td>${raw_ceiling[0]['lamina'].toFixed(2)}</td>
			<td>${raw_ceiling[1]['lamina'].toFixed(2)}</td>
		</tr>
		<tr>
			<td class="al_center">T 12:</td>
			<td>${raw_ceiling[0]['t12'].toFixed(2)}</td>
			<td>${raw_ceiling[1]['t12'].toFixed(2)}</td>
		</tr>
		<tr>
			<td class="al_center">T 4:</td>
			<td>${raw_ceiling[0]['t4'].toFixed(2)}</td>
			<td>${raw_ceiling[1]['t4'].toFixed(2)}</td>
		</tr>
		<tr>
			<td class="al_center">Ang.12:</td>
			<td>${raw_ceiling[0]['angulo'].toFixed(2)}</td>
			<td>${raw_ceiling[1]['angulo'].toFixed(2)}</td>
		</tr>
		<tr>
			<td class="al_center">Clavo:</td>
			<td>${raw_ceiling[0]['clavo'].toFixed(2)}</td>
			<td>${raw_ceiling[1]['clavo'].toFixed(2)}</td>
		</tr>
			`;
			$("#tbl_ceiling tbody").html(contenido_tbody);
}

function insert_herraje_tina(){
	
}