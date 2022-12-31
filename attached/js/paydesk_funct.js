// JavaScript Document
class class_payment
{

}
class class_measure{
	constructor(raw_medida){
		this.array_medida = raw_medida;
	}
}
class class_collect
{
	constructor(total,payed){
		this.total = total;
		this.payed = payed;
	}
	generate_tbl_nuevaventa(nuevaventa, activo) {
		var array_medida = cls_measure.array_medida;
		// var nuevaventa = {};
		// var nuevaventa = (data[<? php echo $_COOKIE['coo_iuser']; ?>][activo] != undefined) ?data[<? php echo $_COOKIE['coo_iuser']; ?>][activo] : { };
		// var total_itbm = 0;
		var total_descuento = 0; 
		var total = 0;
		var raw_tax = {};
		var sub_noimp = 0;
		var sub_imp = 0;
		var total_imp = 0;
		var total = 0;

		if (Object.keys(nuevaventa).length > 0) {
			var content = '';
			for (var x in nuevaventa) {
				var descuento = (nuevaventa[x]['precio'] * nuevaventa[x]['descuento']) / 100;
				var precio_descuento = nuevaventa[x]['precio'] - descuento;
				var impuesto = (precio_descuento * nuevaventa[x]['impuesto']) / 100;
				var precio_unitario = precio_descuento + impuesto;
				precio_unitario = Math.round10(precio_unitario, -4);
				var subtotal = nuevaventa[x]['cantidad'] * precio_unitario;
				// total_itbm += impuesto*nuevaventa[x]['cantidad'];
				total_descuento += descuento * nuevaventa[x]['cantidad'];
				// total += subtotal;
				content += '<tr><td>' + nuevaventa[x]['codigo'] + '</td><td onclick="upd_descripcion_nuevaventa(' + x + ',\'' + replace_regular_character(nuevaventa[x]['descripcion']) + '\')">' + replace_special_character(nuevaventa[x]['descripcion']) + '</td><td>' + array_medida[nuevaventa[x]['medida']] + '</td><td onclick="upd_unidades_nuevaventa(' + x + ');">' + nuevaventa[x]['cantidad'] + '</td><td  onclick="upd_precio_nuevaventa(' + x + ');">' + nuevaventa[x]['precio'] + '</td><td>' + descuento.toFixed(4) + '</td><td>' + impuesto.toFixed(4) + '</td><td>' + precio_unitario.toFixed(4) + '</td><td>' + subtotal.toFixed(2) + '</td><td><button type="button" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="del_nuevaventa(' + x + ');"><strong>X</strong></button></td></tr>';


				raw_tax[nuevaventa[x]['impuesto']] = (raw_tax[nuevaventa[x]['impuesto']]) ? raw_tax[nuevaventa[x]['impuesto']] + (precio_descuento * nuevaventa[x]['cantidad']) : (precio_descuento * nuevaventa[x]['cantidad']);
			}
			for (const a in raw_tax) {
				if (a > 0) {
					sub_imp += raw_tax[a];
					var cal_imp = (a * raw_tax[a]) / 100;
					cal_imp = cal_imp.toFixed(2);
					total_imp += parseFloat(cal_imp);
					total += raw_tax[a] + total_imp;
				} else {
					sub_noimp += raw_tax[a];
				}
			}
			total += sub_noimp;


			activo = activo.replace("_sale", "");
			$("#tbl_product2sell_" + activo + " tbody").html(content);
			$("#span_discount_" + activo).html(total_descuento.toFixed(2));
			// $("#span_itbm_"+activo).html(total_itbm.toFixed(2));
			// $("#span_total_"+activo).html(total.toFixed(2));
			$("#span_taxeable_" + activo).html(sub_imp.toFixed(2));
			$("#span_untaxeable_" + activo).html(sub_noimp.toFixed(2));
			$("#span_itbm_" + activo).html(total_imp.toFixed(2));
			$("#span_total_" + activo).html(total.toFixed(2));
		} else {
			content = content + '<tr><td colspan="10">&nbsp;</td></tr>';
			activo = activo.replace("_sale", "");
			$("#tbl_product2sell_" + activo + " tbody").html(content);
			$("#span_discount_" + activo).html(total_descuento.toFixed(2));
			// $("#span_itbm_"+activo).html(total_itbm.toFixed(2));
			// $("#span_total_"+activo).html(total.toFixed(2));
			$("#span_taxeable_" + activo).html(sub_imp.toFixed(2));
			$("#span_untaxeable_" + activo).html(sub_noimp.toFixed(2));
			$("#span_itbm_" + activo).html(total_imp.toFixed(2));
			$("#span_total_" + activo).html(total.toFixed(2));

		}
	}

	
}

function upd_quantityonnewcollect(datoventa_id){
	var new_quantity=prompt("Ingrese la nueva cantidad:");
	new_quantity = val_intw2dec(new_quantity);
	ans = val_intwdec(new_quantity);
	if(!ans){	return false;	}
	upd_quantityproduct2addcollect(datoventa_id,new_quantity,get("a"))
}

function  unset_filterclient(e){
	if (e.which === 13) {
		$("#btn_addclient").click();
	}else{
		$( "#txt_filterclient").prop("alt","");
	}
	$( function() {
		$( "#txt_filterclient").autocomplete({
			source: "attached/get/filter_client_sell.php",
			minLength: 2,
			select: function( event, ui ) {
	      var n_val = ui.item.value;
	      raw_n_val = n_val.split(" | Dir:");
	      ui.item.value = raw_n_val[0];
				content = '<strong>Nombre:</strong> ' + ui.item.value + ' <strong>RUC:</strong> ' + ui.item.ruc + ' <strong>Tlf.</strong> ' + ui.item.telefono + ' <strong>Dir.</strong> ' + ui.item.direccion.substr(0, 20);
				fire_recall('container_client_recall', content)
				clean_payment($.get('a'),$.get('b'));

				var ruc = ui.item.ruc;
				if (ruc.charAt(0) == 0 && ruc.charAt(1) == '-' || /0{7}/g.test(ruc) === true) {
					shot_snackbar('¡Debe Acomodar la Cédula');
					open_popup('popup_updclient.php?a=' + ui.item.id, 'popup_updclient', '425', '507')
				} else {
					$("#txt_filterclient").prop('alt', ui.item.id);
				}
			}
		});
	});
}
