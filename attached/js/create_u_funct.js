// JavaScript Document

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








function sel_user(id,user,mail,pass,type,seudonimo){
	document.getElementById("user_id").value = id;
	document.getElementById("user_user").value = user;
	document.getElementById("user_mail").value = mail;
	document.getElementById("user_pass").value = "";
	document.getElementById("user_type").selectedIndex = type;
	document.getElementById("user_type").value = type;
	document.getElementById("user_seudonimo").value = seudonimo;
}

function js_save_user(){
	if(isEmpty("user_user")){
		setFocus("user_user")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("user_mail")){
		setFocus("user_mail")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("user_seudonimo")){
		setFocus("user_seudonimo")
	alert("Campos vacio");
	return false;	
	}
	if(isSet("user_pass")){
		document.getElementById("user_pass").value = hex_sha1(document.getElementById("user_pass").value)
	}
	var user_id = extraer_value("user_id");
	if(user_id == ''){
		new config_user('add');
	}else{
		new config_user('upd');
	}
}

function js_del_user(){
	if(isEmpty("user_id")){
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("user_user")){
		setFocus("user_user")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("user_mail")){
		setFocus("user_mail")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("user_pass")){
		setFocus("user_pass")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("user_seudonimo")){
		setFocus("user_seudonimo")
	alert("Campos vacio");
	return false;	
	}
		new config_user('del');
}

function sel_medic(id,name,gender,speciality,dir,font){
/*	alert(" id: "+id+" name: "+name+" genero: "+gender+" spe: "+speciality+" dir: "+dir+" fuente: "+font);
*/	document.getElementById("medic_id").value = id;
	document.getElementById("medic_name").value = name;
	document.getElementById("medic_gender").selectedIndex = gender;
	document.getElementById("medic_gender").value = gender;
	document.getElementById("medic_speciality").value = speciality;
	document.getElementById("medic_dir").value = dir;
	document.getElementById("medic_font").selectedIndex = font;
	document.getElementById("medic_font").value = font;
}

function js_save_medic(){
	if(isEmpty("medic_name")){
		setFocus("medic_name")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("medic_speciality")){
		setFocus("medic_speciality")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("medic_dir")){
		setFocus("medic_dir")
	alert("Campos vacio");
	return false;	
	}
	var medic_id = extraer_value("medic_id");
	if(medic_id == ''){
		new config_medic('add');
	}else{
		new config_medic('upd');
	}
}

function js_del_medic(){
	if(isEmpty("medic_id")){
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("medic_name")){
		setFocus("medic_name")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("medic_speciality")){
		setFocus("medic_speciality")
	alert("Campos vacio");
	return false;	
	}
	if(isEmpty("medic_dir")){
		setFocus("medic_dir")
	alert("Campos vacio");
	return false;	
	}
		new config_medic('del');
}

function js_save_rel(){
	new chk_rel()
}
function chk_rel(){
	if(isEmpty("user_id") || isEmpty("medic_id")){
		alert("Hay campos vacios");
		return false();
	}
	save_rel();
}
function js_del_rel(){
	if(isEmpty("user_id") || isEmpty("medic_id")){
		alert("Hay campos vacios");
		return false();
	}
	del_rel();
}
