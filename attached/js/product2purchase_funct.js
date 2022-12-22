// JavaScript Document
//#################### FUNCION CHECK CANTIDAD PARA SUMAR ####################
content_quantity = "";
limit_quantity = 10;

function chk_quantity(field){
	var num_char = field.value.length;
	if(num_char > limit_quantity){
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
		field.value = content_quantity;
	}else{
		return content_quantity = field.value;
	}
}

content_price = "";
limit_price = 15;

function chk_price(field){
	var num_char = field.value.length;
	if(num_char > limit_price){
		field.value = content_price;
		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_price = field.value;
	}
}

content_itbm = "";
limit_itbm = 3;

function chk_itbm(field){
	var num_char = field.value.length;
	if(num_char > limit_itbm){
		field.value = content_itbm;
		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_itbm = field.value;
	}
}

content_descuento = "";
limit_descuento = 3;

function chk_descuento(field){
	var num_char = field.value.length;
	if(num_char > limit_descuento){
		field.value = content_descuento;
		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_descuento = field.value;
	}
}

//content_quantity = "";
//limit_quantity = 10;

//function chk_quantity(field){
//	field.value = Solo_Numerico(field.value);
//	var num_char = field.value.length;
//	if(num_char > limit_quantity){
//		field.value = content_quantity;
////		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
//	}else{
//		new plus_stock(field.value);
//		return content_quantity = field.value;
//	}
//}
//


