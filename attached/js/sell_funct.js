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
function plus_product2nuevaventa(product_id,precio,descuento,itbm,activo,cantidad,medida){
	// close_popup();
	$.ajax({	data: {"a" : product_id, "b" : precio, "c" : descuento, "d" : itbm, "e" : activo, "f" : cantidad, "g" : medida, "z" : 'plus' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		if(data){
			data = JSON.parse(data);
			generate_tbl_nuevaventa(data,activo);
			activo=activo.replace("_sale","");
			$("#btn_guardar, #btn_facturar").css("display","initial");
			$("#txt_filterproduct").focus();
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});

}

function upd_descripcion_nuevaventa(product_id,description){
	$.ajax({	data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {
		if (data[0][0] === '') {
			return false
		}else{
			var n_description = prompt("Introduzca la nueva descripcion",description);
			if (n_description.length > 100) {
				alert("La descripcion en muy larga");
				upd_descripcion_nuevaventa(product_id,description);
			}else{
				var activo = $(".tab-pane.active").attr("id");
				n_description = n_description.toUpperCase();
				n_description = replace_regular_character(n_description);
				$.ajax({	data: {"a" : product_id, "b" : n_description, "c" : activo, "d" : 'descripcion', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
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
function upd_precio_nuevaventa(product_id){
	$.ajax({ data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	if(data[0][0] != ""){
			var activo = $(".tab-pane.active").attr("id");
			var new_price = prompt("Ingrese el Nvo. Precio:");
			if (new_price === '' || isNaN(new_price)) {
				return false;
			}
			new_price = val_intw2dec(new_price);
			new_price = parseFloat(new_price);
			$.ajax({	data: {"a" : product_id, "b" : new_price.toFixed(2), "c" : activo, "d" : 'precio', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
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
function upd_unidades_nuevaventa(product_id){
	var activo = $(".tab-pane.active").attr("id");
	var new_quantity = prompt("Ingrese la cantidad:");
	if (new_quantity === '' || isNaN(new_quantity)) {
		return false;
	}
	new_quantity = val_intw2dec(new_quantity);
	new_quantity = parseFloat(new_quantity);
	$.ajax({	data: {"a" : product_id, "b" : new_quantity.toFixed(2), "c" : activo, "d" : 'cantidad', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		if(data){
			data = JSON.parse(data);
			generate_tbl_nuevaventa(data,activo);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function del_nuevaventa(product_id){
	var activo = $(".tab-pane.active").attr("id");
	$.ajax({	data: {"a" : product_id, "b" : activo, "z" : 'del' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
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
	popup.close();
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
				content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>RUC:</strong> '+ui.item.ruc+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20)+' <strong>Asiduo.</strong> '+ui.item.asiduo;
				fire_recall('container_client_recall_'+activo, content)
				generate_tbl_favorito(ui.item.json_favorito,activo);
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
			open_popup('popup_addclient.php?a='+name,'popup_addclient','425','420')
		}else{
			open_popup('popup_updclient.php?a='+$("#txt_filterclient_"+activo).prop('alt'),'popup_updclient','425','420')
		}
	}else{
		open_popup('popup_addclient.php?a='+name,'popup_addclient','425','420')
	}
}
