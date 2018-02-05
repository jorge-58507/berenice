// JavaScript Document
function js_save_fmtcomplete(){
	new chk_fmtcomplete()
}
function chk_fmtcomplete(){
	if(isEmpty("med_speciality") || isEmpty("med_direction")){
		alert("Hay campos vacios");
		return false();
	}
	var h_enter = parseInt(document.getElementById("sel_medenter").value);
		h_exit = parseInt(document.getElementById("sel_medexit").value);
	if(h_enter > h_exit){
		alert("No puedes salir antes de entrar");
		return false();
	}
	save_fmtcomplete();
}

//#################### FUNCION CHECK ESPECIALIDAD ####################
content_speciality = "";
limit_speciality = 150;

function chk_speciality(field){
	var num_char = field.value.length;
	if(num_char > limit_speciality){
		document.getElementById("counter_speciality").innerHTML = "&nbsp;&nbsp;&nbsp;Quedan: ("+0+") caracteres";
		field.value = content_speciality;
	}else{
		var rest = parseInt(limit_speciality) - parseInt(num_char);
		document.getElementById("counter_speciality").innerHTML = "&nbsp;&nbsp;&nbsp;Quedan: ("+rest+") caracteres";
		return content_speciality = field.value;
	}
}
//#################### FUNCION CHECK DIRECCION ####################
content_direction = "";
limit_direction= 150;

function chk_direction(field){
	var num_char = field.value.length;
	if(num_char > limit_direction){
		document.getElementById("counter_direction").innerHTML = "&nbsp;&nbsp;&nbsp;Quedan: ("+0+") caracteres";
		field.value = content_speciality;
	}else{
		var rest = parseInt(limit_speciality) - parseInt(num_char);
		document.getElementById("counter_direction").innerHTML = "&nbsp;&nbsp;&nbsp;Quedan: ("+rest+") caracteres";
		return content_speciality = field.value;
	}
}












