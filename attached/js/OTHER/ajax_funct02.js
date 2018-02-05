// JavaScript Document
function prueba(str) {
	//var str = " uno";
	alert(str);
}

function pac_info(){
	var str = document.getElementById("pac_ci").value;
	var fecha = document.getElementById("fecha_actual").value;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("td_pac_info").innerHTML=xmlhttp.responseText;	
			}
		}
		xmlhttp.open("GET","attached/get/getpacient_info_by_ci.php?q="+str+"&r="+fecha,true);	xmlhttp.send();	
}

function add_hm(){
	
	var fecha = document.getElementById("fecha_actual").value;
	var inftipo = document.getElementById("inf_tipo").value;
	var ci = document.getElementById("pac_ci").value;
	var nya = document.getElementById("pac_nya").value;
	var edad = document.getElementById("pac_edad").value;
	var h = document.getElementById("pac_h").value;
	var genero = document.getElementById("pac_genero").value;
	
	var fc = document.getElementById("inf_ef_fc").value;
	var tas = document.getElementById("inf_ef_tas").value;
	var tad = document.getElementById("inf_ef_tad").value;
	var fr = document.getElementById("inf_ef_fr").value;
	var temp = document.getElementById("inf_ef_temp").value;
	var gc = document.getElementById("inf_ef_gc").value;

	var condicion = document.getElementById("sel_condition").value;
	var resp = document.getElementById("inf_ef_respiracion").value;
	var hidra = document.getElementById("inf_ef_hidratacion").value;
	var fiebre = document.getElementById("inf_ef_fiebre").value;
	var pupi = document.getElementById("inf_ef_pupila").value;

	var piel = document.getElementById("inf_ef_piel").value;
	var cabeza = document.getElementById("inf_ef_head").value;
	var orl = document.getElementById("inf_ef_orl").value;
	var cuello = document.getElementById("inf_ef_neck").value;
	var torax = document.getElementById("inf_ef_chest").value;
	var abdomen = document.getElementById("inf_ef_abdomen").value;
	var pelvis = document.getElementById("inf_ef_pelvis").value;

	var mc = document.getElementById("inf_mc").value;
	var ea = document.getElementById("inf_ea").value;
	var ante = document.getElementById("inf_antecedente").value;
	var ef = document.getElementById("inf_ef").value;
	var dx = document.getElementById("inf_dx").value;
	var comen = document.getElementById("inf_commentary").value;
	var plan = document.getElementById("inf_plan").value;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("td_pac_info").innerHTML=xmlhttp.responseText;	
			}
		}
		xmlhttp.open("GET","attached/get/add_hm.php?a="+fecha+"&b="+ci+"&c="+nya+"&d="+edad+"&e="+h+"&f="+genero+"&g="+fc+"&h="+tas+"&i="+tad+"&j="+fr+"&k="+temp+"&l="+gc+"&m="+condicion+"&n="+resp+"&o="+hidra+"&p="+fiebre+"&q="+pupi+"&r="+piel+"&s="+cabeza+"&t="+orl+"&u="+cuello+"&v="+torax+"&w="+abdomen+"&x="+pelvis+"&y="+mc+"&z="+ea+"&aa="+ante+"&ab="+ef+"&ac="+dx+"&ad="+comen+"&ae="+plan+"&af="+inftipo,true);	xmlhttp.send();
		alert("Datos agregados exitosamente");	

}

function add_pac_info(){
	var ci = document.getElementById("pac_ci").value;
	var nya = document.getElementById("pac_nya").value;
	var edad = document.getElementById("pac_edad").value;
	var h = document.getElementById("pac_h").value;
	var genero = document.getElementById("pac_genero").value;
	var fecha = document.getElementById("fecha_actual").value;
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("td_pac_info").innerHTML=xmlhttp.responseText;	
			}
		}
		xmlhttp.open("GET","attached/get/add_pacient_info.php?q="+ci+"&r="+nya+"&s="+edad+"&t="+h+"&u="+genero+"&v="+fecha,true);	xmlhttp.send();
		alert("Datos agregados exitosamente");	

}

function load_hm_by_date(){
	var ci = extraer_value("pac_ci");
	var fecha = extraer_value("sel_fecha");
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("td_hm").innerHTML=xmlhttp.responseText;	
			}
		}
		xmlhttp.open("GET","attached/get/hm_info.php?q="+fecha+"&r="+ci,true);	xmlhttp.send();
}







function encrypt_pass(str)
{
	if (window.XMLHttpRequest)
	{	
		xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
  	  document.getElementById("tdpass").innerHTML=xmlhttp.responseText;	}
		}
	xmlhttp.open("GET","attached/get/getencrypt_pass.php?q="+str,true);	xmlhttp.send();
}	

function selectPacient(pac_id, date_id)
{
	if (window.XMLHttpRequest)
	{
		xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		xmlhttp.open("GET","attached/get/upd_datestate.php?q="+date_id,true);	xmlhttp.send();	

		window.open('PoPnewDate.php','open_history', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=auto,resizable,alwaysRaised,dependent,titlebar=no, width=503,height=503');
}









function new_nhis(str)
{
	if (window.XMLHttpRequest)
	{	
		xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
  	  document.getElementById("tdnhis").innerHTML=xmlhttp.responseText;	}
	}
	xmlhttp.open("GET","attached/get/getnew_nhis.php?q="+str,true);	xmlhttp.send();
}	
function already_nhis(str, uid)
{
	if (window.XMLHttpRequest)
	{	
		xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
  	  document.getElementById("tdnhis").innerHTML=xmlhttp.responseText;	}
	}
	xmlhttp.open("GET","attached/get/getalready_nhis.php?q="+str+"&r="+uid,true);	xmlhttp.send();
}	

function calAge(str){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("pac_age").innerHTML=xmlhttp.responseText;	}
		}
		xmlhttp.open("GET","attached/get/getage.php?q="+str,true);	xmlhttp.send();	
}

function pac_filter(uid, str){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("td_date_pac").innerHTML=xmlhttp.responseText;	}
		}
		xmlhttp.open("GET","attached/get/getpacient_filter.php?q="+str+"&r="+uid,true);	xmlhttp.send();	
}
function date_age(uid, str){
	alert(uid+"/"+str);
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	{	if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("td_date_pac_age").innerHTML=xmlhttp.responseText;	}
		}
		xmlhttp.open("GET","attached/get/getdate_age.php?q="+str+"&r="+uid,true);	xmlhttp.send();	
}
function del_date(str){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
			xmlhttp.onreadystatechange=function()	
			{	
			if (xmlhttp.readyState==4 && xmlhttp.status==200)	{
			document.getElementById("res_deldate").innerHTML=xmlhttp.responseText;	
			}
		}
		xmlhttp.open("GET","attached/get/del_date.php?q="+str,true);	xmlhttp.send();	
}
function selectPacient(pac_id, date_id){
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		xmlhttp.open("GET","attached/get/upd_datestate.php?q="+date_id,true);	xmlhttp.send();	

		window.open('PoPnewDate.php','open_history', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=auto,resizable,alwaysRaised,dependent,titlebar=no, width=503,height=503');
}
