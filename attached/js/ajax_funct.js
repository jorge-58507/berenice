// JavaScript Document

var f = new Date();
var yyyy = f.getFullYear();	var mm = (f.getMonth() +1);	var dd = f.getDate();
if(dd<10) {	dd='0'+dd	} 	if(mm<10) {	mm='0'+mm	}
var fecha_actual = yyyy + "-" + mm + "-" + dd;

function clean_session(str){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("response").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/clean_session.php?a="+str,true);	xmlhttp.send();
}
class class_stock {
	add_product(){
		var codigo = $("#txt_codigo").val();			var referencia = $("#txt_referencia").val();
		var nombre = url_replace_regular_character($("#txt_nombre").val());	var medida = $("#sel_medida").val();
		var cantidad = $("#txt_cantidad").val();	var maxima = $("#txt_cantmaxima").val();
		var minima = $("#txt_cantminima").val();	var exento = $("#txt_impuesto").val();
		var letra = $("#sel_letter").val();				var p_4 = $("#txt_p_4").val();
		var p_5 = $("#txt_p_5").val();						var p_3 = $("#txt_p_3").val();
		var p_2 = $("#txt_p_2").val();						var p_1 = $("#txt_p_1").val();
		var familia = $("#sel_subfamilia").val();
		$.ajax({	data: {"a":codigo,"b":nombre,"c":medida,"d":cantidad,"e":maxima,"f":minima,"g":exento,"h":p_5,"i":p_4,"j":p_3,"k":p_2,"l":p_1,"m":referencia,"n":letra,"o":familia,"method":'create'},	type: "GET",	dataType: "text",	url: "attached/get/class_stock.php", })
		.done(function( data, textStatus, jqXHR ) {
			var raw_data = JSON.parse(data);
			$("#tbl_product tbody").html(cls_stock.render_table_product(raw_data));
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	}
	delete_product (product_id)	{
		var str_filter = url_replace_regular_character(document.getElementById("txt_filterproduct").value);
		var limit = ($("input[name=r_limit]:checked").val());
		$.ajax({	data: {"a":product_id,"b":str_filter,"c":limit,"method":'delete'},	type: "GET",	dataType: "text",	url: "attached/get/class_stock.php", })
		.done(function( data, textStatus, jqXHR ) {
			var raw_data = JSON.parse(data);
			$("#tbl_product tbody").html(cls_stock.render_table_product(raw_data['filtered']));
			var bg_snackbar = (raw_data['message'] != 'Desactivado') ? 'bg-danger' : 'bg-warning';
			shot_snackbar(raw_data['message'],bg_snackbar);
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	}
	update_product (product_id) {
		var codigo = $("#txt_codigo").val();			var nombre = url_replace_regular_character($("#txt_nombre").val());
		var medida = $("#sel_medida_descripcion").val();	var impuesto = $("#txt_impuesto").val();
		var cantidad = $("#txt_cantidad").val();	var maxima = $("#txt_cantmaxima").val();
		var minima = $("#txt_cantminima").val();

		var alarma = ( $('#cb_alarm').prop('checked') )  ? '0' : '1';							var active = ( $('#cb_active').prop('checked') )  ? '0' : '1';
		var descontable = ( $('#cb_discountable').prop('checked') )  ? '1' : '0';	var inventariado = ( $('#cb_inventoried').prop('checked') )  ? '1' : '0';

		var referencia = url_replace_regular_character($("#txt_reference").val());	var letra = $("#sel_letter").val();
		var ubicacion = $("#sel_ubication_product").val();	var subfamilia = $("#sel_subfamilia").val();
		var last_filter = url_replace_regular_character(window.opener.$("#txt_filterproduct").val());

		var data = {"a":codigo,"b": nombre, "c": medida, "d": cantidad, "e": maxima, "f": minima, "l": impuesto, "m": alarma, "n": active, "o": referencia, "p": letra, "q": product_id, "r": last_filter, "s": descontable, "t": inventariado, "u": ubicacion, "v" : subfamilia, "method" : 'update' };
		var url_data = data_fetch(data);
		
		var myRequest = new Request(`attached/get/class_stock.php${url_data}`);
		fetch(myRequest)
		.then(function(response) {
			return response.text()
			.then(function(text) {
				var raw_data = JSON.parse(text);
				window.opener.$("#tbl_product tbody").html(cls_stock.render_table_product(raw_data));
				self.close();
			});
		});
	}
	filter_product ()	{
		var value = url_replace_regular_character($("#txt_filterproduct").val());
		var limit = ($("input[name=r_limit]:checked").val());
		$.ajax({	data: {"a" : value, "b" : limit, "method" : "filter"},	type: "GET",	dataType: "text",	url: "attached/get/class_stock.php", })
		.done(function( data, textStatus, jqXHR ) {
			var raw_data = JSON.parse(data);
			$("#tbl_product tbody").html(cls_stock.render_table_product(raw_data));
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	}
	render_table_product (raw_data)	{
		var tbl_product_content = '';
		for (const key in raw_data) { var bg_color = '';
			var tr_style = (raw_data[key]['TX_producto_activo'] === '1') ? 'color:#c67250; font-weight: bolder;':'';
			if(raw_data[key]['TX_producto_inventariado'] === '1') { bg_color = ' background-color:#CFFEBB'; }; 
			if(raw_data[key]['TX_producto_cantidad'] <= raw_data[key]['TX_producto_minimo'] && raw_data[key]['TX_producto_alarma'] == 0) { bg_color = ' background-color:#f4b883'; }; 
			var qty_style = (raw_data[key]['TX_producto_cantidad'] >= raw_data[key]['TX_producto_maximo']) ? 'style="color:#51AA51"' : '';
			if (raw_data[key]['TX_producto_cantidad'] <= raw_data[key]['TX_producto_minimo']) { qty_style = 'style="color:#C63632"'	};
			tbl_product_content += `
				<tr ondblclick="openpopup_updproduct('${raw_data[key]['AI_producto_id']}');"  style="${tr_style} ${bg_color}">
					<td>${raw_data[key]['TX_producto_codigo']}</td>
					<td>${replace_special_character(raw_data[key]['TX_producto_referencia'])}</td>
					<td>${replace_special_character(raw_data[key]['TX_producto_value'])}</td>
					<td ${qty_style}>${raw_data[key]['TX_producto_cantidad']}</td>
					<td>
						<button type="button" class="btn btn-success btn-sm" onclick="open_popup('popup_relacion.php?a=${raw_data[key]['AI_producto_id']}','popup_relacion','500','491')"><i class="fa fa-rotate-right">${raw_data[key]['TX_producto_rotacion']}</i></button>
						<button type="button" name="" id="" class="btn btn-warning btn-sm" onclick="openpopup_updproduct('${raw_data[key]['AI_producto_id']}');"><i class="fa fa-wrench"></i></button>
						<button type="button" name="" id="" class="btn btn-danger btn-sm" onclick="cls_stock.delete_product('${raw_data[key]['AI_producto_id']}');"><i class="fa fa-times"></i></button>
					</td>
				</tr>
			`;
		}
		return tbl_product_content;
	}
	// CLASS CLOSED
}

function upd_cant(id,str){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("response").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/new_exit.php?a="+id+"&b="+str,true);	xmlhttp.send();
		setTimeout("location.reload()",250);
}

function add_newentry(field){
		var id = field.value;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tblnewentry").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/add_newentry.php?a="+id,true);	xmlhttp.send();
		alert("Elemento modificado exitosamente");
		location.reload();
}

function plus_product2purchase(id){
	$("#btn_acept").attr("disabled", true);
	$.ajax({	data: {"a" : id, "b" : $("#txt_quantity").val(), "c" : $("#txt_price").val(), "d" : $("#txt_discount").val(), "e" : $("#txt_itbm").val(), "f" : $("#txt_p_4").val(), "g" : $("#sel_measure").val() },	type: "GET",	dataType: "text",	url: "attached/get/plus_product2purchase.php", })
	.done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
		window.opener.$("#tbl_newentry tbody").html(data);
		setTimeout("self.close()",500);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function del_product2purchase(field){
		var id = field.name;
		$.ajax({	data: {"a" : id},	type: "GET",	dataType: "text",	url: "attached/get/del_product2purchase.php", })
		 .done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
				$("#tbl_newentry tbody").html(data);
			})
		 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function upd_quantitynewpurchase(nuevacompra_id){
	var new_quantity=prompt("Ingrese la cantidad");
	new_quantity=val_intw2dec(new_quantity);
	if (new_quantity ===	'NaN') {
		return false;
	}
	$.ajax({	data: { "a" : nuevacompra_id, "b" : new_quantity },	type: "GET",	dataType: "text",	url: "attached/get/upd_quantitynewpurchase.php", })
	.done(function( data, textStatus, jqXHR ) {
		console.log("GOOD" + textStatus);
		if(data){
			$("#tbl_newentry tbody").html(data);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {		});
};

function upd_pricenewpurchase(nuevacompra_id){
	var new_quantity=prompt("Ingrese el Coste");
	new_quantity=val_intw4dec(new_quantity);
	if (new_quantity ===	'NaN') {
		return false;
	}
	$.ajax({	data: { "a" : nuevacompra_id, "b" : new_quantity },	type: "GET",	dataType: "text",	url: "attached/get/upd_pricenewpurchase.php", })
	.done(function( data, textStatus, jqXHR ) {
		console.log("GOOD" + textStatus);
		if(data){
			$("#tbl_newentry tbody").html(data);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {		});
};

function upd_newpurchase_price(f){
	id = f.id;
	new_price = prompt("Ingrese el Nuevo Precio:");
	new_price = val_intw2dec(new_price);
	ans = val_intwdec(new_price);
	if (!ans) {	return false;	}
	new_price = parseFloat(new_price);
	$.ajax({	data: {"a" : id, "b" : new_price }, type: "GET", dataType: "text", url: "attached/get/upd_newpurchase_price.php",	})
	.done(function( data, textStatus, jqXHR ) {
		$("#tbl_newentry tbody").html(data);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function upd_taxnewpurchase(nuevacompra_id){
	var new_tax=prompt("Ingrese la tasa impositiva");
	new_tax=val_intw4dec(new_tax);
	if (new_tax ===	'NaN') {	return false;	}
	$.ajax({	data: { "a" : nuevacompra_id, "b" : new_tax },	type: "GET",	dataType: "text",	url: "attached/get/upd_taxnewpurchase.php", })
	.done(function( data, textStatus, jqXHR ) {
		console.log("GOOD" + textStatus);
		if(data){
			$("#tbl_newentry tbody").html(data);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {		});
};

function clean_product2purchase(){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("response").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/clean_product2purchase.php",true);	xmlhttp.send();
		setTimeout("window.location='purchase.php'",250);
}

function plus_newprovider(){
	$.ajax({	data: {"a" : $("#txt_providername").val().toUpperCase(), "b" : $("#txt_cif").val(), "c" : $("#txt_direction").val(), "d" : $("#txt_telephone").val(), "e" : $("#sel_type").val(), "f" : $("#txt_dv").val() },	type: "GET",	dataType: "text",	url: "attached/get/plus_newprovider.php", })
	 .done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
			data = JSON.parse(data);
			window.opener.$("#txt_filterprovider").val(data['nombre']);
			window.opener.$("#txt_filterprovider").prop("alt",data['id']);
			setTimeout(function(){ self.close()}, 500);
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function upd_provider(proveedor_id){
	$.ajax({	data: {"a" : $("#txt_providername").val().toUpperCase(), "b" : $("#txt_cif").val(), "c" : $("#txt_direction").val(), "d" : $("#txt_telephone").val(), "e" : $("#sel_type").val(), "f" : $("#txt_dv").val(), "g" : proveedor_id },	type: "GET",	dataType: "text",	url: "attached/get/upd_provider.php", })
	 .done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
			data = JSON.parse(data);
			window.opener.$("#txt_filterprovider").val(data['nombre']);
			window.opener.$("#txt_filterprovider").prop("alt",data['id']);
			setTimeout(function(){ self.close()}, 250);
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function filter_product2purchase(field){
	var value = url_replace_regular_character(field.value);
	// var value = field.value.replace("#","laremun");
		type = field.getAttribute('alt');
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tblproduct").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/filter_product2purchase.php?a="+value+"&b="+type,true);	xmlhttp.send();
}

var intervalo;
function filter_product_sell(field){
	clearInterval(intervalo);
	intervalo = setInterval(function(){
		var value = url_replace_regular_character(field.value);
		var limit = ($("input[name=r_limit]:checked").val());
		$.ajax({	data: {"a" : value, "b" : limit },	type: "GET",	dataType: "text",	url: "attached/get/filter_product_sell.php", })
		.done(function( data, textStatus, jqXHR ) { 
			data = JSON.parse(data);
			var content_tbody = '';
			for (var x in data[1]) {
				var style = (data[1][x]['TX_producto_inventariado'] === '1') ? 'background-color: #cffebb' : '';
				if (data[1][x]['TX_producto_activo'] == 1) { style = 'background-color: #febbbb' }
				content_tbody += `
				<tr onclick="open_product2sell(${data[1][x]['AI_producto_id']},${data[1][x]['TX_producto_activo']})" style="${style};">
					<td title="${data[1][x]['AI_producto_id']}">${data[1][x]['TX_producto_codigo']}</td>
					<td>${replace_special_character(data[1][x]['TX_producto_value'])}</td>
					<td title='Contado el: ${data[1][x]['inventoried']}'>${data[1][x]['TX_producto_cantidad']}</td>
					<td>${(data[1][x]['precio']) ? data[1][x]['precio'] : ''}</td>
				</tr>
				`;
			}
			$("#tbl_product tbody").html(content_tbody);
			$("#tbl_product caption").html(data[0]);
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		clearInterval(intervalo);
	}, 1000);
}

function filter_product_collect(field,fact_id){

	var value = field.value;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_selproduct").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/filter_product_collect.php?a="+value+"&b="+fact_id,true);	xmlhttp.send();
}

function save_sale(status){
	var activo = $(".tab-pane.active").attr("id");
	activo = activo.replace("_sale","");
	if($("#txt_filterclient_"+activo).prop("alt") === ''){
		var confirm = window.confirm('Se guardará como CONTADO, ¿Continuar?')
		if (confirm) {
			$("#txt_filterclient_" + activo).attr("alt", 1);
			$("#txt_filterclient_" + activo).val("CONTADO");
		}else{
			$("#txt_filterclient_" + activo).addClass("input_invalid");
			$("#txt_filterclient_"+activo).val('');	$("#txt_filterclient_"+activo).focus();
			shot_snackbar('No hay cliente seleccionado');
			return false;
		}
	}
	$("#txt_filterclient_" + activo).removeClass("input_invalid");

	if($("#tbl_product2sell_"+activo+" tbody tr td")[0].innerHTML === ""){
		shot_snackbar('No hay productos seleccionados');
		return false;
	}

	$("#btn_guardar").attr("disabled", true);
	setTimeout(() => {	$("#btn_guardar").attr("disabled", false);	}, 3000);

	var	date = $("#txt_date_"+activo).val();
	var client_id = $("#txt_filterclient_"+activo).prop("alt");
	var client = $("#txt_filterclient_"+activo).val();
	var vendor_id = $("#txt_vendedor").prop("alt");
	var observation = $("#txt_observation_"+activo).val();
	var tuser= $.cookie('coo_tuser');
	$.ajax({	data: {"a" : date, "b" : client_id, "c" : client, "d" : vendor_id, "g" : observation, "h" : status, "i" : activo+'_sale' },	type: "GET",	dataType: "text",	url: "attached/get/save_sale.php", })
	.done(function( data, textStatus, jqXHR ) {
		if(data === 'failed'){ alert("Consulte al administrador del sistema"); return false; }
		refresh_tblproduct2sale();
		$("#txt_filterclient_"+activo).prop("alt",'1');
		$("#txt_filterclient_"+activo).val('');
		var ans = window.confirm("¿Desea Imprimir el documento?");
		if (ans) { print_html('print_sale_html.php?a='+data);	}
		if(tuser === '4'){	open_popup_w_scroll('popup_newcollect.php?a='+client_id+'&b='+vendor_id, 'popup_newcollect','525','425');	}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function save_old_sale(){
	var	date = $("#txt_date").val();
	client_id = $("#txt_filterclient").prop("alt");
	client = $("#txt_filterclient").val();
	vendor_id = $("#txt_vendedor").val();
	number = $("#txt_numero").val();
	observation = $("#txt_observation").val();
	facturaventa_id = get('a');
	total = $("#span_total").html();
	total = total.replace(",","");
	$.ajax({	data: {"a" : date, "b" : client_id, "c" : facturaventa_id, "d" : observation },	type: "GET",	dataType: "text",	url: "attached/get/save_old_sale.php", })
	.done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
		if(data === 'failed'){ alert("Consulte al administrador del sistema"); return false;}

		if (data === 'denied') {
			alert("Esta cotizacion se encuentra cobrada.");
		}
		setTimeout("history.back(1)",250);
	})
 	.fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function upd_quantityproduct2sell(product_id,quantity){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tblproduct2sale").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/upd_quantityproduct2sell.php?a="+product_id+"&b="+quantity,true);	xmlhttp.send();
}

function upd_priceproduct2sell(product_id,price){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tblproduct2sale").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/upd_priceproduct2sell.php?a="+product_id+"&b="+price,true);	xmlhttp.send();
}

// function plus_newclient(){
// 	var opener_url = window.opener.location;
// 	patt = RegExp(/old_sale|new_collect/);
// 	activo = (patt.test(opener_url)) ?	'' :	window.opener.$(".tab-pane.active").attr("id");	activo = activo.replace("_sale","");
// 	var	name = url_replace_regular_character($("#txt_clientname").val());
// 	var cif = $("#txt_cif").val();
// 	var dv = ($("#txt_dv").val() === "") ? '00' : $("#txt_dv").val();
// 	var direction = ($("#txt_direction").val() === "" || $("#txt_direction").val().length < 6) ? 'NO INDICA' : $("#txt_direction").val();
// 	var telephone = (is_empty_var($("#txt_telephone").val()) === 0) ? '0000-0000' : $("#txt_telephone").val();
// 	var taxpayer = $('#sel_client_taxpayer').val();
// 	var type = $('#sel_client_type').val();
// 	var email = $('#txt_client_email').val();


// 	$.ajax({	data: {"a" : name, "b" : cif, "c" : direction, "d" : telephone, "e" : activo, "f" : dv, "g" : taxpayer, "h" : type, "i" : email },	type: "GET",	dataType: "text",	url: "attached/get/plus_newclient.php", })
//  	.done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
// 	 	if (activo != '') {
// 			window.opener.$("#container_txtfilterclient_"+activo).html(data);
// 		} else {
// 			window.opener.$("#container_txtfilterclient").html(data);
// 		}
// 		setTimeout("self.close()",250);
// 	})
//  	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
// }

function plus_product2addpaydesk(id,facturaventa_id,arr_factid){
	var	cantidad = document.getElementById("txt_quantity").value;
		precio = document.getElementById("input_price").value;
		descuento = document.getElementById("txt_discount").value;
		itbm = document.getElementById("txt_itbm").value;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
				window.opener.document.getElementById("container_tblproduct2sale").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/plus_product2addpaydesk.php?a="+id+"&b="+cantidad+"&c="+precio+"&d="+descuento+"&e="+itbm+"&f="+facturaventa_id,true);	xmlhttp.send();
		alert("Elemento modificado exitosamente");
		self.close()
}

function del_product2addpaydesk(field,facturaventa_id){
		var id = field.name;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tblproduct2sale").innerHTML=xmlhttp.responseText;
			}
		}
	xmlhttp.open("GET","attached/get/del_product2addpaydesk.php?a="+id+"&b="+facturaventa_id,true);	xmlhttp.send();
		alert("Elemento modificado exitosamente");
		location.reload();
}

function plus_product2addcollect(id,facturaventa_id,str_factid){
	$.ajax({	data: {"a" : id, "c" : $("#input_price").val(), "d" : $("#txt_discount").val(), "e" : $("#txt_itbm").val(), "f" : facturaventa_id, "g" : str_factid, "h" : $("#txt_quantity").val(), "i" : $("#sel_medida").val()},	type: "GET",	dataType: "text",	url: "attached/get/plus_product2addcollect.php", })
	.done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
		window.opener.$("#container_tblproduct2sale").html(data);
		setTimeout("self.close()",300);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function del_product2addcollect(product_id,facturaventa_id,str_factid){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tblproduct2sale").innerHTML=xmlhttp.responseText;
			}
		}
	xmlhttp.open("GET","attached/get/del_product2addcollect.php?a="+product_id+"&d="+facturaventa_id+"&c="+str_factid,true);	xmlhttp.send();
}

function plus_facturaf(str_factid){
	var content = $("#container_btn_process").html();
	$("#container_btn_process").html('<img src="attached/image/Eclipse-2.1s-200px.gif" alt="" width="100px">');
	// $("#btn_process, #btn_generate").attr("disabled", true);
	// setTimeout(() => { $("#btn_process, #btn_generate").attr("disabled", false); }, 10000);
	var	client_id=$("#txt_filterclient").prop("alt");
	if (is_empty_var(client_id) === 0) { 
		$("#container_btn_process").html(content);
		shot_snackbar('Debe seleccionar un cliente');	
		return false;	
	}
	$.ajax({	data: {"a" : str_factid, "b" : client_id },	type: "GET",	dataType: "text",	url: "attached/get/plus_facturaf.php", })
	 	.done(function( data, textStatus, jqXHR ) {
		  if (data){
				patt = RegExp(/Fatal error: Maximum execution time/);
				if(patt.test(data)){
					alert("Exceso de Latencia en el servidor.");
					setTimeout("location.reload()",100);
					return false;
				}
				if (data === "acepted"){
					setTimeout("window.location='print_f_fiscal.php'",100);
				}else{
					alert("Conexion no lograda, Existe un problema interno de red.");
					setTimeout("location.reload()",100);
				}
			}
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function generate_facturaf(str_factid){
	$("#btn_process, #btn_generate").attr("disabled", true);
	setTimeout(() => { $("#btn_process, #btn_generate").attr("disabled", false); }, 10000);

	var	client_id=$("#txt_filterclient").prop("alt");
	$.ajax({	data: {"a" : str_factid, "b" : client_id },	type: "GET",	dataType: "text",	url: "attached/get/generate_facturaf.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 if (data){
			 setTimeout("window.location='generate_f_fiscal.php'",100);
		 }
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function render_payment(txt_data){
	var raw_data = JSON.parse(txt_data);
	var raw_payment = raw_data['data_pago'];
	var content = ``;
	var monto_pagado = 0;
	var cambio = 0;
	var diferencia = 0;
	var ite = 1;
	for (const a in raw_payment) {
		content += `
			<tr>
				<td>${ite++}.-</td>
				<td>${raw_payment[a]['TX_metododepago_value']}</td>
				<td>${raw_payment[a]['TX_pago_numero']}</td>
				<td>${val_dec(raw_payment[a]['TX_pago_monto'],2,1,1)}</td>
				<td>
					<button type="button" name="${raw_payment[a]['AI_pago_id']}" class="btn btn-danger btn-xs btn-fa" onclick="del_payment(this.name,${raw_data['fact_id']})"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></button>
				</td>
			</tr>
		`;
		monto_pagado = parseFloat(monto_pagado) + parseFloat(raw_payment[a]['TX_pago_monto']);
	}
	monto_pagado = monto_pagado.toFixed(2);
	cls_collect.total = parseFloat(raw_data['total_ff']);
	cls_collect.payed = parseFloat(monto_pagado);

	if (cls_collect.total === cls_collect.payed) {
		cambio = 0;
		diferencia = 0;
	} 
	if (cls_collect.total > cls_collect.payed) {
		cambio = 0;
		diferencia = cls_collect.total - cls_collect.payed;
	} else {
		cambio = cls_collect.payed - cls_collect.total;
		diferencia = 0;
	}
	cambio = parseFloat(cambio).toFixed(2)
	diferencia = parseFloat(diferencia).toFixed(2)
	var footer_content = `
		<tr>
			<td colspan="6">
				<div id="container_payment_data" class="container-fluid">
					<div id="payment_total" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<strong>Total: </strong><br />
						B/ <span id="span_payment_total">${  val_dec(cls_collect.total,2,1,1) }</span>
					</div>
					<div id="payment_paid_out" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<strong>Entrega: </strong><br />
						B/ <span id="span_payment_paid_out">${ val_dec(cls_collect.payed,2,1,1) }</span>
					</div>
					<div id="payment_to_pay" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<strong>Diferencia: </strong><br />
						B/ <span id="span_payment_to_pay">${ val_dec(diferencia,2,1,1) }</span>
					</div>
					<div id="payment_change" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<strong>Cambio: </strong><br />
						B/ <span id="span_payment_change">${ val_dec(cambio, 2, 1, 1) }</span>
					</div>
				</div>
			</td>
		</tr>
	`;

	$("#tbl_paymentlist tbody").html(content);
	$("#tbl_paymentlist tfoot").html(footer_content);
}

function plus_payment(metodo,str_factid){
		if($("#txt_amount").val() != "" && parseFloat($("#txt_amount").val()) > 0 ){ monto = parseFloat($("#txt_amount").val())}else{return false};
		var n_control = $("#txt_number").val();

		$.ajax({	data: {"a" : metodo, "b" : monto, "c" : n_control, "d" : str_factid },	type: "GET",	dataType: "text",	url: "attached/get/plus_payment.php", })
		 .done(function( data, textStatus, jqXHR ) { render_payment(data);	})
		 .fail(function( jqXHR, textStatus, errorThrown ) {		});
		 $("#txt_amount").val(""); $("#txt_amount").focus();
		 $("#txt_number").val("");
}

function del_payment(pago_id, str_factid){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
				render_payment(xmlhttp.responseText);
			}
		}
		xmlhttp.open("GET","attached/get/del_payment.php?a="+pago_id+"&b="+str_factid,true);	xmlhttp.send();
		$("#txt_amount").focus();
}

function upd_discount(facturaventa_id,percent){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
				var data = JSON.parse(xmlhttp.responseText);
				if (data['status'] === 'success') {
					setTimeout("window.location.reload()", 250);
				}
			}
		}
		xmlhttp.open("GET","attached/get/upd_discount.php?b="+facturaventa_id+"&c="+percent,true);	xmlhttp.send();
}

function clean_payment(str_factid, client_id){
	$.ajax({	data: {"a" : str_factid, "b" : client_id},	type: "GET",	dataType: "text",	url: "attached/get/clean_payment.php", })
	.done(function( data, textStatus, jqXHR ) { console.log("GOOD " + textStatus);
		$("#container_tblpaymentlist").html(data);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	     console.log("BAD " + textStatus);	});
}

function get_credit_client(client_id){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_payment").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/get_credit_client.php?a="+client_id,true);	xmlhttp.send();
}

function save_datoventa(datoventa_id){
	var	producto_id = $("#txt_producto").prop("alt");
		cantidad = $("#txt_cantidad").val();
		precio = $("#txt_precio").val();
		impuesto = $("#txt_impuesto").val();
		descuento = $("#txt_descuento").val();
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
				// document.getElementById("response").innerHTML=xmlhttp.responseText;
				var data = JSON.parse(xmlhttp.responseText);
				if (data['status'] === 'success') {
					setTimeout("window.opener.location.reload()",250);
					setTimeout("self.close()",300);
				}
			}
		}
		xmlhttp.open("GET","attached/get/save_datoventa.php?a="+producto_id+"&b="+cantidad+"&c="+precio+"&d="+impuesto+"&e="+descuento+"&f="+datoventa_id,true);	xmlhttp.send();

}

function filter_product_editdatoventa(field){
	var value = field.value;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_selproductlist").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/filter_product_editdatoventa.php?a="+value,true);	xmlhttp.send();
}

function filter_client_newnc(field){
	field.value = field.value.toUpperCase();
	document.getElementById("txt_filterclient").alt = "";
	var value = field.value;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_nc_selclient").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/filter_client_sell.php?a="+value,true);	xmlhttp.send();
}

// function plus_newcreditnote(){
// var fecha = $("#span_fecha").text();
// 	numero = $("#span_numero").text();
// 	cliente = $("#txt_filterclient").prop("alt");
// 	motivo = $("#txt_motivo").val();
// 	monto = $("#txt_ncmonto").val();
// 	if (window.XMLHttpRequest){
// 		xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
// 		xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
// 		document.getElementById("response").innerHTML=xmlhttp.responseText;
// 		}
// 	}
// 	xmlhttp.open("GET","attached/get/plus_newcreditnote.php?a="+fecha+"&b="+numero+"&c="+cliente+"&d="+motivo+"&e="+monto,true);	xmlhttp.send();
// 	setTimeout("window.location='print_creditnote.php'",250);
// }

function plus_return(datoventa_id,cantidad,medida_id){
	var debito = window.opener.$("#txt_debito").val();
	$.ajax({	data: {"a" : datoventa_id, "b" : cantidad, "c" : debito, "d" : medida_id },	type: "GET",	dataType: "text",	url: "attached/get/plus_return.php", })
	.done(function( data, textStatus, jqXHR ) { console.log("GOOD " + textStatus);
		window.opener.$("#container_tblreturn").html(data);
		window.opener.$("#txt_debito").prop("disabled", "disabled")
	 	setTimeout("self.close()", 300);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	     console.log("BAD " + textStatus);	});
}

function del_return(nuevadevolucion_id){
	var debito = $("#txt_debito").val();
	$.ajax({	data: {"a" : nuevadevolucion_id, "b" : debito },	type: "GET",	dataType: "text",	url: "attached/get/del_return.php", })
	.done(function( data, textStatus, jqXHR ) { console.log("GOOD " + textStatus);
		$("#container_tblreturn").html(data);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus);	});
}

function clean_newreturn(){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("response").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/clean_newreturn.php",true);	xmlhttp.send();
		setTimeout("history.back(1)",250);
}

function plus_paymentondebit(metodo,str_factid){

	if($("#txt_amount").val() != "" && parseFloat($("#txt_amount").val()) > 0 ){ monto = parseFloat($("#txt_amount").val())}else{return false};
	n_control = $("#txt_number").val();
	console.log(n_control+" monto "+monto);
	$.ajax({	data: {"a" : metodo, "b" : monto, "c" : n_control, "d" : str_factid },	type: "GET",	dataType: "text",	url: "attached/get/plus_paymentondebit.php", })
	 .done(function( data, textStatus, jqXHR ) {	$("#container_tblpaymentlist").html( data );	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
	 $("#txt_amount").val(""); $("#txt_amount").focus();
	 $("#txt_number").val("");

}

function del_paymentondebit(pago_id,str_factid){
	$.ajax({	data: {"a" : pago_id, "b" : str_factid },	type: "GET",	dataType: "text",	url: "attached/get/del_paymentondebit.php", })
	 .done(function( data, textStatus, jqXHR ) {	$("#container_tblpaymentlist").html( data );	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
	 $("#txt_amount").val(""); $("#txt_amount").focus();
	 $("#txt_number").val("");
}

function plus_debit(str_factid){
		motivo = $("#txt_motivond").val();
		$.ajax({	data: {"a" : motivo, "b" : str_factid },	type: "GET",	dataType: "text",	url: "attached/get/plus_debit.php", })
		 .done(function( data, textStatus, jqXHR ) {
			 if(data){

				 window.open("print_debito.php?a="+data);
				 setTimeout("window.location='start.php'",250);
			 }
		 })
		 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function filter_facturaventa(){
	var	value = $("#txt_filterfacturaventa").val();
	status = $("#sel_filterfacturaventa").val();
	date_i = $("#txt_date_initial").val();
	date_f = $("#txt_date_final").val();
	r_user = ($("input[name=r_user]:checked").val());
	if(status === ''){ return false; }
	$("#container_tblfacturaventa").html(`<div class="container-fluid al_center"><img src="attached/image/Eclipse-2.1s-200px.gif" alt="" width="100px"></div>`);
	if(r_user === 'propio'){
		$.ajax({	data: {"a" : value, "b" : status, "c" : date_i, "d" : date_f },	type: "GET",	dataType: "text",	url: "attached/get/filter_facturaventa_usuario.php", })
		 .done(function( data, textStatus, jqXHR ) {
			 if(data){
				 $("#container_tblfacturaventa").html(data);
			 }
		 })
		 .fail(function( jqXHR, textStatus, errorThrown ) {		});
	}else{
		$.ajax({	data: {"a" : value, "b" : status, "c" : date_i, "d" : date_f },	type: "GET",	dataType: "text",	url: "attached/get/filter_facturaventa.php", })
		 .done(function( data, textStatus, jqXHR ) {
			 if(data){
				 $("#container_tblfacturaventa").html(data);
			 }
		 })
		 .fail(function( jqXHR, textStatus, errorThrown ) {		});
	}

	// if (window.XMLHttpRequest){
	// 	xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
	// 	xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
	// 	document.getElementById("container_tblfacturaventa").innerHTML=xmlhttp.responseText;
	// 	}
	// }
	// xmlhttp.open("GET","attached/get/filter_facturaventa.php?a="+value+"&b="+status+"&c="+date_i+"&d="+date_f,true);	xmlhttp.send();
}

function render_paydesk(facturaventa_lista){
	facturaventa_lista = JSON.parse(facturaventa_lista);
	var content = ``;

	for (const a in facturaventa_lista) {
		var cot_date = date_converter('ymd', 'dmy', facturaventa_lista[a]['TX_facturaventa_fecha'])
		if (facturaventa_lista[a]['TX_facturaventa_status'] === 'ACTIVA' ) {
			var btn_open = `<button type="button" id="btn_editfacturaventa" name="${facturaventa_lista[a]['AI_facturaventa_id']}" class="btn btn-warning" onclick="javascript:window.location='old_sale.php?a='+this.name">Abrir</button>`;
		}else{
			var btn_open = `<button type="button" id="btn_editfacturaventa" name="${facturaventa_lista[a]['AI_facturaventa_id']}" class="btn btn-warning" onclick="javascript:window.location='new_paydesk.php?a='+this.name">Abrir</button>`;
		}
		content += `
			<tr ondblclick="open_newcollect(${facturaventa_lista[a]['facturaventa_AI_cliente_id']},${facturaventa_lista[a]['facturaventa_AI_user_id']}')">
				<td>${cot_date}</td>
				<td>${facturaventa_lista[a]['TX_user_seudonimo']}</td>
				<td>${facturaventa_lista[a]['TX_cliente_nombre']}<br />
					<font style="font-size:10px; font-weight:bolder;">${facturaventa_lista[a]['TX_cliente_direccion']}</font>
				</td>
				<td>${facturaventa_lista[a]['TX_facturaventa_numero']}</td>
				<td>B/ ${facturaventa_lista[a]['TX_facturaventa_total']}</td>
				<td class="text_center">
					${btn_open}
				</td>
				<td class="text_center">
					<button type="button" id="btn_newcollect" name="${facturaventa_lista[a]['facturaventa_AI_cliente_id']}" class="btn btn-success" onclick="open_newcollect(this.name,${facturaventa_lista[a]['facturaventa_AI_user_id']});">Cobrar</button>
				</td>
			</tr>
		`
	}
	$('#tbl_facturaventa tbody').html(content);
}

var timer = 0
function filter_paydesk(){
	clearTimeout(timer);
	timer = setTimeout(function () {
		var	value = $("#txt_filterpaydesk").val();
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
				render_paydesk(xmlhttp.responseText);
			}
		}
		xmlhttp.open("GET","attached/get/filter_paydesk.php?a="+value+"&b=''&c=''&d=''",true);	xmlhttp.send();
	}, 1000)
}

function upd_quantityproduct2addcollect(datoventa_id,new_quantity,fact_id){
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
		document.getElementById("container_tblproduct2sale").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","attached/get/upd_quantityproduct2addcollect.php?a="+datoventa_id+"&b="+new_quantity+"&c="+fact_id,true);	xmlhttp.send();
}

function filter_popupnewdebit(str,client_id){
	var date = $("#txt_date").val();
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tblbill").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/filter_popupnewdebit.php?a="+str+"&b="+date+"&c="+client_id,true);	xmlhttp.send();
}

function filter_adminfacturaf(str){
var	value = $("#txt_filterfacturaf").val();
	date_i = $("#txt_date_initial").val();
	date_f = $("#txt_date_final").val();
	limit = $("input[name=r_limit]:checked").val();
	payment_method = $("#sel_paymentmethod").val();
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			var facturaf = JSON.parse(xmlhttp.responseText);
			render_tableff(facturaf);
		}
	}
	xmlhttp.open("GET","attached/get/filter_adminfacturaf.php?a="+value+"&b="+date_i+"&c="+str+"&d="+limit+"&e="+date_f+"&f="+payment_method,true);	xmlhttp.send();
}

function upd_statusbill(facturaventa_id){
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
		document.getElementById("container_tblfacturaf").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","attached/get/upd_statusbill.php?a="+facturaventa_id,true);	xmlhttp.send();
	setTimeout("history.back(1)",250);
}

function filter_sale(value){
	var status = $("#sel_status").val()
	date = $("#txt_date").val();
	$.ajax({	data: {"a" : value, "b" : status, "c" : date},	type: "GET",	dataType: "text",	url: "attached/get/filter_sale.php", })
	.done(function( data, textStatus, jqXHR ) {
		$("#tbl_facturaventa tbody").html(data);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function filter_adminfacturaventa(value){
var status = $("#sel_status").val()
	date = $("#txt_date").val();
		if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
		document.getElementById("container_tblfacturaventa").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","attached/get/filter_adminfacturaventa.php?a="+value+"&b="+status+"&c="+date,true);	xmlhttp.send();
}

function filter_oldquotation(date_limit){
		if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
		document.getElementById("container_tblfacturaventa").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","attached/get/filter_oldquotation.php?a="+date_limit,true);	xmlhttp.send();
}

function filter_psbyproduct(product_id){
	$("#container_datofacturacompra").html('');
	var limit = ($("input[name=r_limit]:checked").val());
	var date_i = $("#txt_date_initial").val();
	var date_f = $("#txt_date_final").val();
	$.ajax({data: {"a" : product_id, "b" : limit, "c" : date_i, "d" : date_f }, type: "GET", dataType: "text", url: "attached/get/filter_ps_byproduct.php",})
	.done(function( data, textStatus, jqXHR ) {
		data = JSON.parse(data);
//  #############################    COMPRA    ###################
		var raw_facturacompra=data[0];
		content_facturacompra = '';
		if(Object.keys(raw_facturacompra).length > 0){ var ttl_compra = 0;
			for (var x in raw_facturacompra) {	ttl_compra = ttl_compra+parseFloat(raw_facturacompra[x]['TX_datocompra_cantidad']);
				content_facturacompra += `
				<tr title="Stock: ${raw_facturacompra[x]['TX_datocompra_existencia']}" onclick="filter_productbypurchase(${raw_facturacompra[x]['AI_facturacompra_id']})" >
					<td>${convertir_formato_fecha(raw_facturacompra[x]['TX_facturacompra_fecha'])}</td>
					<td>${raw_facturacompra[x]['TX_facturacompra_numero']}</td>
					<td>${raw_facturacompra[x]['TX_facturacompra_ordendecompra']}</td>
					<td>${raw_facturacompra[x]['TX_proveedor_nombre']}</td>
					<td>${raw_facturacompra[x]['TX_datocompra_cantidad']}</td>
					<td><button type="button" id="btn_delete" class="btn btn-danger btn-sm" onclick="transform_facturacompra('${raw_facturacompra[x]['AI_facturacompra_id']}')"><i class="fa fa-times"></i></button>&nbsp;<button type="button" id="btn_print" class="btn btn-info btn-sm" name="" onclick="print_html(\'print_purchase_html.php?a=${raw_facturacompra[x]['AI_facturacompra_id']}\')"><i class="fa fa-print"></i></button></td>
				</tr>`;
			}
			document.getElementById('ttl_purchase').innerHTML = `<strong>Total Comprado:</strong><br />${(parseFloat(ttl_compra))}`;
		}else{	content_facturacompra = '<tr><td colspan="5" class="al_center">Vacio</td></tr>';	}
		//  #############################    VENTA    ###################
		var raw_facturaf=data[1];
		content_facturaf = '';
		var total_cantidad=0;
		if(Object.keys(raw_facturaf).length > 0){
			for (var x in raw_facturaf) {
				total_cantidad = total_cantidad+parseFloat(raw_facturaf[x]['TX_datoventa_cantidad']);
				descuento4product = (raw_facturaf[x]['TX_datoventa_descuento']*raw_facturaf[x]['TX_datoventa_precio'])/100;
				precio_descuento = raw_facturaf[x]['TX_datoventa_precio']-descuento4product;
				impuesto4product = (raw_facturaf[x]['TX_datoventa_impuesto']*precio_descuento)/100;
				precio_impuesto = precio_descuento+impuesto4product;
				content_facturaf += `<tr onclick="filter_productbysale(${raw_facturaf[x]['AI_facturaf_id']})" title="inStock: ${raw_facturaf[x]['TX_datoventa_stock']}"><td>${convertir_formato_fecha(raw_facturaf[x]['TX_facturaf_fecha'])}</td><td>${raw_facturaf[x]['TX_facturaf_numero']}</td><td>${raw_facturaf[x]['TX_cliente_nombre']}</td><td>${raw_facturaf[x]['TX_datoventa_cantidad']}</td><td>${precio_impuesto.toFixed(2)}</td></tr>`;
			}
		}else{	content_facturaf = '<tr><td colspan="5" class="al_center">Vacio</td></tr>';	}
		//  #############################    NOTA DE CREDITO    ###################
		var raw_nc=data[2];
		content_nc = ''; total_devuelto=0;
		if(Object.keys(raw_nc).length > 0){
			for (var x in raw_nc) {
				total_devuelto += parseFloat(raw_nc[x]['TX_datodevolucion_cantidad']);
				var anulado = (raw_nc[x]['TX_notadecredito_anulado'] > 0) ? 'ANULADA' : 'NO ANULADA';
				content_nc += `<tr><td>${convertir_formato_fecha(raw_nc[x]['TX_notadecredito_fecha'])}</td><td>${raw_nc[x]['TX_notadecredito_numero']}</td><td>${raw_nc[x]['TX_cliente_nombre']}</td><td>${raw_nc[x]['TX_datodevolucion_cantidad']}</td><td>${anulado}</td></tr>`;
			}
		}else{	content_nc = '<tr><td colspan="5" class="al_center">Vacio</td></tr>';	}

		$("#tbl_facturacompra tbody").html(content_facturacompra);
		$("#tbl_facturaf tbody").html(content_facturaf);
		$("#span_ttl_sold").html('<strong>Total Vendido:</strong> <br/>'+total_cantidad);
		$("#tbl_nc tbody").html(content_nc);
		$("#span_ttl_nc").html('<strong>Total Devuelto:</strong> <br/>'+total_devuelto);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function filter_facturacompra(field){
	var value = field.value;
	var	limit = ($("input[name=r_limit]:checked").val());
	var date_i = $("#txt_date_initial").val();
	var date_f = $("#txt_date_final").val();
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
		document.getElementById("container_tblfacturacompra").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","attached/get/filter_facturacompra.php?a="+value+"&b="+limit+"&c="+date_i+"&d="+date_f,true);	xmlhttp.send();
}

function filter_productbypurchase(facturacompra_id){
	$.ajax({data: {"a" : facturacompra_id }, type: "GET", dataType: "text", url: "attached/get/filter_productbypurchase.php",})
	.done(function( data, textStatus, jqXHR ) {
		$("#container_datofacturacompra").html(data);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function filter_productbysale(facturaf_id){
	$.ajax({	data: { "a" : facturaf_id },	type: "GET",	dataType: "text",	url: "attached/get/filter_productbysale.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 $("#container_datofacturaf").html(data);
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD" + textStatus);});
}

function filter_beneath(){
	var ans = confirm("¿Desea Visualizarlo(s)?");
	if(!ans){ return false; }
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tblproduct").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/filter_beneath.php",true);	xmlhttp.send();
}

function plus_cashmovement(){
	var	tipo = $("#sel_tipo").val();
		motivo = $("#txt_motivo").val();
		monto = $("#txt_monto").val();
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("response").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/plus_cashmovement.php?a="+tipo+"&b="+motivo+"&c="+monto,true);	xmlhttp.send();
		setTimeout("window.opener.open('print_cashmovement.php')",250);
		setTimeout("self.close()",300);
}

function filter_facturaventa_vendedor(){
	var	vendedor_id = $("#sel_vendedor").val();
		date_i = $("#txt_date_initial").val();
		date_f = $("#txt_date_final").val();
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tblfacturaventa").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/filter_facturaventa_vendedor.php?a="+vendedor_id+"&b="+date_i+"&c="+date_f,true);	xmlhttp.send();
}

function filter_adminclient(value){
	var	limit = ($("input[name=r_limit]:checked").val());
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tblclient").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/filter_adminclient.php?a="+value+"&b="+limit,true);	xmlhttp.send();
}

function get_datoventabyfacturaf(facturaf_id){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tbldatoventa").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/get_datoventabyfacturaf.php?a="+facturaf_id,true);	xmlhttp.send();
		var posicion = $("#container_tbldatoventa").offset().top;
		$('html, body').animate({scrollTop:posicion}, 'fast');
}

function upd_credit_term(client_id){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tbldatoventa").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/get_datoventabyfacturaf.php?a="+facturaf_id,true);	xmlhttp.send();
		$('html, body').animate({scrollTop:0}, 'fast');
}

function upd_vendor(str,facturaventa_id){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("response").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/upd_vendor.php?a="+str+"&b="+facturaventa_id,true);	xmlhttp.send();
}

function filter_cashmovement(str){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_cashmovement").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/filter_cashmovement.php?a="+str,true);	xmlhttp.send();
}

function plus_cashregister(){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("response").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/plus_cashregister.php",true);	xmlhttp.send();
}

function upd_report(report_id){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tblreport").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/upd_report.php?a="+report_id,true);	xmlhttp.send();
}

function del_tax(tax_id){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("container_tbltax").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","attached/get/del_tax.php?a="+tax_id,true);	xmlhttp.send();
}
