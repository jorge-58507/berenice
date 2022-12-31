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
	.done(function( data, textStatus, jqXHR ) {	 
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
			if (n_description.length > 700) {
				alert("La descripcion en muy larga");
				descripcion = replace_regular_character(descripcion);
				upd_descripcion_nuevaventa(key_nuevaventa,descripcion);
			}else{
				var activo = $(".tab-pane.active").attr("id");
				n_description = n_description.toUpperCase();
				n_description = url_replace_regular_character(n_description);
				$.ajax({	data: {"a" : key_nuevaventa, "b" : n_description, "c" : activo, "d" : 'descripcion', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
				.done(function( data, textStatus, jqXHR ) {	 
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
			.done(function( data, textStatus, jqXHR ) {	 
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
	.done(function( data, textStatus, jqXHR ) {	 
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
	.done(function( data, textStatus, jqXHR ) {	 
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
	.done(function( data, textStatus, jqXHR ) {	 
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
				content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>RUC:</strong> '+ui.item.ruc+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20);
				fire_recall('container_client_recall_'+activo, content)
				
				var ruc = ui.item.ruc;
				if (ruc.charAt(0) == 0 && ruc.charAt(1) == '-' || ruc.charAt(0) == 0 && ruc.charAt(1) == 0) {
					shot_snackbar('¡Debe Acomodar la Cédula','bg_danger');
					open_popup('popup_updclient.php?a=' + ui.item.id, 'popup_updclient', '425', '507')
				}else{
					$("#txt_filterclient_" + activo).prop('alt', ui.item.id);
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

class client
{
	chk_client_taxpayer(field) {
		switch (field.value) {
			case "0": //NATURAL - CONSUMIDOR
				$('#sel_client_taxpayer').val(1);
				$('#sel_client_type').val(2);
				break;
			case "1": //NATURAL - JURIDICO
				$('#sel_client_taxpayer').val(1);
				$('#sel_client_type').val(1);
				break;
			case "2": //JURIDICO - CONTRIBUYENTE
				$('#sel_client_taxpayer').val(2);
				$('#sel_client_type').val(1);
				break;

			case "3": //JURIDICO - GOBIERNO
				$('#sel_client_taxpayer').val(2);
				$('#sel_client_type').val(3);
				break;

			case "4": //JURIDICO - EXTRANJERO
				$('#sel_client_taxpayer').val(2);
				$('#sel_client_type').val(4);
				break;

			case "5": //NATURAL - EXTRANJERO
				$('#sel_client_taxpayer').val(1);
				$('#sel_client_type').val(4);
				break;
		}
	}
	save_client() {
		$("#txt_clientname").val($("#txt_clientname").val().toUpperCase());

		var ans_client = /^[a-zA-Z]+[\W\s]+(?:[a-zA-Z]+\W*)*$/g.test($('#txt_clientname').val())
		if (ans_client === false || /CONTADO+/.test($('#txt_clientname').val()) === true) {
			shot_snackbar('Acomode el nombre del cliente');
			$("#txt_clientname").focus(); return false;
		}
		if (is_empty_var($('#sel_client_type').val()) === 0 || is_empty_var($('#sel_client_taxpayer').val()) === 0) {
			shot_snackbar('Seleccione el tipo de cliente.');
			$("#sel_client").focus(); return false;
		}
		if ($('#sel_client_taxpayer').val() === "2") { //CLIENTES JURIDICOS 1,3,4
			
			if ($('#sel_client_type').val() === "2") {
				shot_snackbar('Seleccione el tipo de cliente.');
				$("#sel_client").focus(); return false;
			}
			if (is_empty_var($("#txt_cif").val()) === 0 || $("#txt_cif").val().length < 10 || /0{10}/g.test(ruc) === true) {
				shot_snackbar('Debe ingresar un RUC de 10 d&iacute;gitos v&aacute;lido.', 'bg-warning');
				$("#txt_cif").focus(); return false;
			}
			if (is_empty_var($("#txt_dv").val()) === 0) {
				shot_snackbar('Falta el D&iacute;gito Verificador.', 'bg-warning');
				$("#txt_dv").focus(); return false;
			}
			var patron = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
			var valueForm = $("#txt_client_email").val();
			if (valueForm.search(patron) != 0) {
				shot_snackbar('Verifique el correo electrónico.', 'bg-warning');
				$("#txt_client_email").focus(); return false;
			}
		} else { //CLIENTES NATURALES 1,2,4
			if ($('#sel_client_type').val() === "3") {
				shot_snackbar('Seleccione el tipo de cliente.');
				$("#sel_client").focus(); return false;
			}

			var ruc = $("#txt_cif").val();
			if (is_empty_var($("#txt_cif").val()) === 0 || $("#txt_cif").val().length < 5 || ruc.charAt(0) == 0 && ruc.charAt(1) == '-' || ruc.charAt(0) == 0 && ruc.charAt(1) == 0 || /0{5}/g.test(ruc) === true) {
				shot_snackbar('Debe ingresar documento valido de 5 d&iacute;gitos.');
				$("#txt_cif").focus(); return false;
			}
			if ($('#sel_client_type').val() === "1") { //SI ES contribuyente
				if (is_empty_var($("#txt_dv").val()) === 0) {
					shot_snackbar('Falta el D&iacute;gito Verificador.', 'bg-warning'); //VERIFICA EL DV
					$("#txt_dv").focus(); return false;
				}

				var patron = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
				var valueForm = $("#txt_client_email").val();
				if (is_empty_var($("#txt_client_email").val()) === 0 || valueForm.search(patron) != 0) { //verifica el email
					shot_snackbar('Verifique el correo electrónico.', 'bg-warning');
					$("#txt_client_email").focus(); return false;
				}
			}
		}
		cls_client.plus_newclient();
	}
	plus_newclient() {
		var opener_url = window.opener.location;
		var patt = RegExp(/old_sale|new_collect/);
		var activo = (patt.test(opener_url)) ? '' : window.opener.$(".tab-pane.active").attr("id"); activo = activo.replace("_sale", "");
		var name = url_replace_regular_character($("#txt_clientname").val());
		var cif = $("#txt_cif").val();
		var dv = (is_empty_var($("#txt_dv").val()) === 0) ? '00' : $("#txt_dv").val();
		var direction = ($("#txt_direction").val() === "" || $("#txt_direction").val().length < 6) ? 'NO INDICA' : $("#txt_direction").val();
		var telephone = (is_empty_var($("#txt_telephone").val()) === 0) ? '0000-0000' : $("#txt_telephone").val();
		var taxpayer = $('#sel_client_taxpayer').val();
		var type = $('#sel_client_type').val();
		var email = $('#txt_client_email').val();

		var info = { "a": name, "b": cif, "c": direction, "d": telephone, "e": activo, "f": dv, "g": taxpayer, "h": type, "i": email };
		$.ajax({ data: info, type: "GET", dataType: "text", url: "attached/get/plus_newclient.php", })
		.done(function (data, textStatus, jqXHR) {
			data = JSON.parse(data);
			if (data['status'] === 'denied') {
				shot_snackbar('Este cliente ya existe'); return false;
			}
			var content = `
    	<label class="label label_blue_sky" for="txt_filterclient">Cliente:</label>
			<input type="text" class="form-control" alt="${data['client_id']}" id="txt_filterclient${data['active']}" name="txt_filterclient" value="${data['name']}" onkeyup="${data['function']}(event)" />
			`;
			if (activo != '') {
				window.opener.$("#container_txtfilterclient_" + activo).html(content);
			} else {
				window.opener.$("#container_txtfilterclient").html(content);
			}
			setTimeout("self.close()", 250);
		})
		.fail(function (jqXHR, textStatus, errorThrown) { console.log("BAD " + textStatus); });
	}
	edit_client(){
		$("#txt_clientname").val($("#txt_clientname").val().toUpperCase());
		var client_name = ($('#txt_clientname').val()).trim()
		var ans_client = /^[a-zA-Z]+[\W\s]+(?:[a-zA-Z]+\W*)*$/g.test(client_name)
		if (ans_client === false || /CONTADO+/.test(client_name) === true) {
			shot_snackbar('Acomode el nombre del cliente');
			$("#txt_clientname").focus(); return false;
		}
		if (is_empty_var($('#sel_client_type').val()) === 0 || is_empty_var($('#sel_client_taxpayer').val()) === 0) {
			shot_snackbar('Seleccione el tipo de cliente.');
			$("#sel_client").focus(); return false;
		}
		if ($('#sel_client_taxpayer').val() === "2") { //CLIENTES JURIDICOS 1,3,4

			if ($('#sel_client_type').val() === "2") {
				shot_snackbar('Seleccione el tipo de cliente.');
				$("#sel_client").focus(); return false;
			}
			if (is_empty_var($("#txt_cif").val()) === 0 || $("#txt_cif").val().length < 10 || /0{10}/g.test(ruc) === true) {
				shot_snackbar('Debe ingresar un RUC de 10 d&iacute;gitos v&aacute;lido.', 'bg-warning');
				$("#txt_cif").focus(); return false;
			}
			if (is_empty_var($("#txt_dv").val()) === 0) {
				shot_snackbar('Falta el D&iacute;gito Verificador.', 'bg-warning');
				$("#txt_dv").focus(); return false;
			}
			var patron = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
			var valueForm = $("#txt_client_email").val();
			if (valueForm.search(patron) != 0) {
				shot_snackbar('Verifique el correo electrónico.', 'bg-warning');
				$("#txt_client_email").focus(); return false;
			}
		} else { //CLIENTES NATURALES 1,2,4
			if ($('#sel_client_type').val() === "3") {
				shot_snackbar('Seleccione el tipo de cliente.');
				$("#sel_client").focus(); return false;
			}

			var ruc = $("#txt_cif").val();
			if (is_empty_var($("#txt_cif").val()) === 0 || $("#txt_cif").val().length < 5 || ruc.charAt(0) == 0 && ruc.charAt(1) == '-' || ruc.charAt(0) == 0 && ruc.charAt(1) == 0 || /0{5}/g.test(ruc) === true) {
				shot_snackbar('Debe ingresar documento valido de 5 d&iacute;gitos.');
				$("#txt_cif").focus(); return false;
			}
			if ($('#sel_client_type').val() === "1") { //SI ES contribuyente
				if (is_empty_var($("#txt_dv").val()) === 0) {
					shot_snackbar('Falta el D&iacute;gito Verificador.', 'bg-warning'); //VERIFICA EL DV
					$("#txt_dv").focus(); return false;
				}

				var patron = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
				var valueForm = $("#txt_client_email").val();
				if (is_empty_var($("#txt_client_email").val()) === 0 || valueForm.search(patron) != 0) { //verifica el email
					shot_snackbar('Verifique el correo electrónico.', 'bg-warning');
					$("#txt_client_email").focus(); return false;
				}
			}
		}
		cls_client.update_client();
	}
	update_client(){
		var opener_url = window.opener.location;
		var patt = RegExp(/old_sale|new_collect/);
		var activo = (patt.test(opener_url)) ? '' : window.opener.$(".tab-pane.active").attr("id"); activo = activo.replace("_sale", "");
		var client_id = document.getElementById('txt_clientname').getAttribute("name");
		var name = url_replace_regular_character($("#txt_clientname").val());
		name = name.trim();
		var cif = $("#txt_cif").val();
		var dv = (is_empty_var($("#txt_dv").val()) === 0) ? '00' : $("#txt_dv").val();
		var direction = ($("#txt_direction").val() === "" || $("#txt_direction").val().length < 6) ? 'NO INDICA' : $("#txt_direction").val();
		var telephone = (is_empty_var($("#txt_telephone").val()) === 0) ? '0000-0000' : $("#txt_telephone").val();
		var taxpayer = $('#sel_client_taxpayer').val();
		var type = $('#sel_client_type').val();
		var email = $('#txt_client_email').val();
		var pending = ($('#cb_pending').prop('checked')) ? '1' : '0';

		var info = { "a": name, "b": cif, "c": telephone, "d": direction, "e": client_id, "f": activo, "g": dv, "h": pending, "i": taxpayer, "j": type, "k": email };
		$.ajax({data: info,type: "GET",dataType: "text",url: "attached/get/upd_client_info.php",})
		.done(function (data, textStatus, jqXHR) {
			var data_obj = JSON.parse(data);
			if (data_obj['status'] === 'failed') {
				shot_snackbar(data_obj['message'], 'bg-warning');
				return false;
			}
			var func = data_obj['function'];
			if (/oldsale/.test(data_obj['function'])) {
				if (/new_collect/.test(opener_url)) {
					func = 'unset_filterclient';
				}
			}
			var content = `
				<label class="label label_blue_sky" for="txt_filterclient">Cliente:</label>
				<input type="text" class="form-control" alt="${data_obj['client_id']}" id="txt_filterclient${data_obj['active']}" name="txt_filterclient" value="${data_obj['name']}" onkeyup="${func}(event)" />
			`;
			if (activo != '') {
				window.opener.$("#container_txtfilterclient" + "_" + activo).html(content);
			} else {
				window.opener.$("#container_txtfilterclient").html(content);
			}
			setTimeout("self.close()", 250);
		})
		.fail(function (jqXHR, textStatus, errorThrown) { console.log("BAD " + textStatus); });

	}
}
