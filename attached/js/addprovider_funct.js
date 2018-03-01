// JavaScript Document

content_providername = "";
limit_providername = 50;

function chk_providername(field){
	field.value = field.value.toUpperCase();
	var num_char = field.value.length;
	if(num_char > limit_providername){
		field.value = content_providername;
	}else{
		return content_providername = field.value;
	}
}

content_cif = "";
limit_cif = 22;

function chk_cif(field){
	field.value = field.value.toUpperCase();
	var num_char = field.value.length;
	if(num_char > limit_cif){
		field.value = content_cif;
	}else{
		return content_cif = field.value;
	}
}

content_direction = "";
limit_direction = 140;

function chk_direction(field){
	field.value = field.value.toUpperCase();
	var num_char = field.value.length;
	if(num_char > limit_direction){
		field.value = content_direction;
	}else{
		return content_direction = field.value;
	}
}

content_telephone = "";
limit_telephone = 30;

function chk_telephone(field){
	var num_char = field.value.length;
	if(num_char > limit_telephone){
		field.value = content_telephone;
	}else{
		return content_telephone = field.value;
	}
}

content_clientname = "";
limit_clientname = 50;

function chk_clientname(field){
	field.value = field.value.toUpperCase();
	var num_char = field.value.length;
	if(num_char > limit_clientname){
		field.value = content_clientname;
	}else{
		return content_clientname = field.value;
	}
}
