// JavaScript Document
function send_login(form){
var obj_form = document.forms[form];
obj_form.submit();
}
function empty_pass(){
	var str1 = document.forms[0]['password_login'].name;
	if (isEmpty(str1)) {
		setFocus(str1);
		return false;
//		alert("Faltan datos para continuar.");
	}
}
function quick_access(field){
	$("#password_login").val(field.name);
	var obj_form = document.forms['form_login'].submit();
}