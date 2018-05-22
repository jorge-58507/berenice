function plus_product2viejaventa(product_id,precio,descuento,itbm,cantidad,medida,promotion){
	close_popup();
	$.ajax({	data: {"a" : product_id, "b" : precio, "c" : descuento, "d" : itbm, "e" : cantidad, "f" : medida, "g" : promotion, "z" : 'plus' }, type: "GET", dataType: "text", url: "attached/php/method_viejaventa.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		if(data){
      data = JSON.parse(data);
			generate_tbl_viejaventa(data);
      var new_value = $("#txt_filterproduct").val();
      new_value = new_value.substr(0,(new_value.length-5));
      $("#txt_filterproduct").val(new_value);
      $("#txt_filterproduct").focus();
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function del_viejaventa (key_viejaventa){
  $.ajax({	data: {"a" : key_viejaventa, "z" : 'del' }, type: "GET", dataType: "text", url: "attached/php/method_viejaventa.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		if(data){
			data = JSON.parse(data);
			generate_tbl_viejaventa(data);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

var upd_descripcion_viejaventa = function(key_viejaventa, old_descripcion){
	$.ajax({	data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {
		if (data[0][0] === '') {  return false  }
		var n_description = prompt("Introduzca la nueva descripcion",replace_special_character(old_descripcion));
    pattern = new RegExp(/\w/,'g');
    var ans = pattern.test(n_description);
    if (!ans) { return false; }
		if (n_description.length > 100) {
			alert("La descripcion en muy larga.");
			upd_descripcion_viejaventa(key_viejaventa,old_descripcion);
		}else{
      n_description = n_description.toUpperCase();
			n_description = url_replace_regular_character(n_description);
		$.ajax({	data: {"a" : key_viejaventa, "b" : n_description, "c" : 'descripcion', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_viejaventa.php",	})
			.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
        data = JSON.parse(data);
  	    generate_tbl_viejaventa(data);
		  })
			.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function upd_unidades_viejaventa(key_viejaventa){
	var new_quantity = prompt("Ingrese la cantidad:");
	if (new_quantity === '' || isNaN(new_quantity) || new_quantity == '0') {
		return false;
	}
	new_quantity = val_intw2dec(new_quantity);
	new_quantity = parseFloat(new_quantity);
	$.ajax({	data: {"a" : key_viejaventa, "b" : new_quantity.toFixed(2), "c" : 'cantidad', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_viejaventa.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		if(data){
			data = JSON.parse(data);
			generate_tbl_viejaventa(data);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function upd_precio_viejaventa(key_viejaventa){
	$.ajax({ data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	if(data[0][0] != ""){
			var new_price = prompt("Ingrese el Nuevo Precio:");
			if (new_price === '' || isNaN(new_price)) {
				return false;
			}
			new_price = val_intw2dec(new_price);
			new_price = parseFloat(new_price);
			$.ajax({	data: {"a" : key_viejaventa, "b" : new_price.toFixed(2), "c" : 'precio', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_viejaventa.php",	})
			.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
				if(data){
					data = JSON.parse(data);
					generate_tbl_viejaventa(data);
				}
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		}	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});

}

function upd_descuento_viejaventa(key_viejaventa){
	$.ajax({ data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	if(data[0][0] != ""){
			var new_discount = prompt("Ingrese el Monto de Descuento:");
			if (new_discount === '' || isNaN(new_discount)) {
				return false;
			}
			new_discount = val_intw2dec(new_discount);
			new_discount = parseFloat(new_discount);
			$.ajax({	data: {"a" : key_viejaventa, "b" : new_discount.toFixed(2), "c" : 'descuento', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_viejaventa.php",	})
			.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
				if(data){
					data = JSON.parse(data);
					generate_tbl_viejaventa(data);
				}
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		}	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});

}
function refresh_tbl_viejaventa(){
	$.ajax({	data: {"z" : 'reload' }, type: "GET", dataType: "text", url: "attached/php/method_viejaventa.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		if(data){
			data = JSON.parse(data);
			generate_tbl_viejaventa(data);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}

function insert_multiple_product2sell(raw_producto,raw_medida,raw_cantidad,raw_precio,raw_impuesto,raw_descuento,promotion_type){
	var multiplo = prompt("Introduzca la cantidad");
	multiplo = val_intw2dec(multiplo);
	if(!val_intwdec(multiplo)){ return false; }
	if(raw_precio.indexOf(0.00)){
		$.ajax({	data: {"a" : raw_producto, "b" : raw_precio, "c" : raw_descuento, "d" : raw_impuesto, "e" : raw_cantidad, "f" : raw_medida, "g" : promotion_type, "h" : multiplo, "z" : 'plus_multiple' }, type: "GET", dataType: "text", url: "attached/php/method_viejaventa.php",	})
		.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
			if(data){
				data = JSON.parse(data);
				generate_tbl_viejaventa(data);
				$("#txt_filterproduct").focus();
			}
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
 	}
}

function set_position_viejaventa(position){
	var n_position = prompt('Indique la posicion',position+1);
	$.ajax({	data: {"a" : position, "b" : n_position, "z" : 'reordenar' }, type: "GET", dataType: "text", url: "attached/php/method_viejaventa.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		if(data){
			data = JSON.parse(data);
			generate_tbl_viejaventa(data);
			$("#txt_filterproduct").focus();
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
