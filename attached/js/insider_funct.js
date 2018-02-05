// JavaScript Document
//#################### FUNCION SELECTS ####################
// function set_ef_piel(){
// 	var tesis = $("#inf_ef_piel").val();
// 	var antitesis = $("#sel_ef_piel").val();
// 	if (tesis.match(antitesis)) {
// 	  console.log("ya existefren");
// 	}else{
// 		console.log("no exiteloco");
// 	}
// function js_sel_head(){
// var tesis_value = extraer_value("inf_ef_head");
// var antitesis_value = extraer_value("sel_ef_head");
// var str = new RegExp(antitesis_value);
// 	if(str.test(tesis_value)){
// 		return false;
// 	}else{
// 		if(tesis_value==""){
// 			document.forms['pac_informe']['inf_ef_head'].value = antitesis_value;
// 		}else{
// 			document.forms['pac_informe']['inf_ef_head'].value = tesis_value+", "+antitesis_value;
// 		}
// 	}
// }
// function js_sel_orl(){
// var tesis_value = extraer_value("inf_ef_orl");
// var antitesis_value = extraer_value("sel_ef_orl");
// var str = new RegExp(antitesis_value);
// 	if(str.test(tesis_value)){
// 		return false;
// 	}else{
// 		if(tesis_value==""){
// 			document.forms['pac_informe']['inf_ef_orl'].value = antitesis_value;
// 		}else{
// 			document.forms['pac_informe']['inf_ef_orl'].value = tesis_value+", "+antitesis_value;
// 		}
// 	}
// }
// function js_sel_neck(){
// var tesis_value = extraer_value("inf_ef_neck");
// var antitesis_value = extraer_value("sel_ef_neck");
// var str = new RegExp(antitesis_value);
// 	if(str.test(tesis_value)){
// 		return false;
// 	}else{
// 		if(tesis_value==""){
// 			document.forms['pac_informe']['inf_ef_neck'].value = antitesis_value;
// 		}else{
// 			document.forms['pac_informe']['inf_ef_neck'].value = tesis_value+", "+antitesis_value;
// 		}
// 	}
// }
//
// function js_sel_torax(antitesis_value){
// var tesis_value = extraer_value("inf_ef_chest");
// var str = new RegExp(antitesis_value);
// 	if(str.test(tesis_value)){
// 		return false;
// 	}else{
// 		if(tesis_value==""){
// 			document.forms['pac_informe']['inf_ef_chest'].value = antitesis_value;
// 		}else{
// 			document.forms['pac_informe']['inf_ef_chest'].value = tesis_value+", "+antitesis_value;
// 		}
// 	}
// }
// function js_sel_respiratory(){
// var antitesis_value = extraer_value("sel_ef_respiratory");
// new js_sel_torax(antitesis_value);
// }
// function js_sel_cardiac(){
// var antitesis_value = extraer_value("sel_ef_cardiac");
// new js_sel_torax(antitesis_value);
// }
//
//
// function js_sel_abdomen(antitesis_value){
// var tesis_value = document.forms['pac_informe']['inf_ef_abdomen'].value;
// var str = new RegExp(antitesis_value);
// 	if(str.test(tesis_value)){
// 		return false;
// 	}else{
// 		if(tesis_value==""){
// 			document.forms['pac_informe']['inf_ef_abdomen'].value = antitesis_value;
// 		}else{
// 			document.forms['pac_informe']['inf_ef_abdomen'].value = tesis_value+", "+antitesis_value;
// 		}
// 	}
// }
// function js_sel_abd_auscultation(){
// 	var antitesis_value = extraer_value("sel_ef_abd_auscultation");
// 	new js_sel_abdomen(antitesis_value);
// }
// function js_sel_abd_inspection(){
// 	var antitesis_value = extraer_value("sel_ef_abd_inspection");
// 	new js_sel_abdomen(antitesis_value);
// }
// function js_sel_abd_palpation(){
// 	var antitesis_value =  extraer_value("sel_ef_abd_palpation");
// 	new js_sel_abdomen(antitesis_value);
// }
//
// function js_sel_pelvis(){
// var tesis_value = extraer_value("inf_ef_pelvis");
// var antitesis_value = extraer_value("sel_ef_pelvis");
// var str = new RegExp(antitesis_value);
// 	if(str.test(tesis_value)){
// 		return false;
// 	}else{
// 		if(tesis_value==""){
// 			document.forms['pac_informe']['inf_ef_pelvis'].value = antitesis_value;
// 		}else{
// 			document.forms['pac_informe']['inf_ef_pelvis'].value = tesis_value+", "+antitesis_value;
// 		}
// 	}
// }
//
// function js_sel_motivo(){
// var tesis_value = extraer_value("inf_mc");
// var antitesis_value = extraer_value("sel_mc");
// var str = new RegExp(antitesis_value);
// 	if(str.test(tesis_value)){
// 		return false;
// 	}else{
// 		if(tesis_value==""){
// 			document.forms['pac_informe']['inf_mc'].value = antitesis_value;
// 		}else{
// 			document.forms['pac_informe']['inf_mc'].value = tesis_value+", "+antitesis_value;
// 		}
// 	}
// }
// function js_sel_ea(){
// var tesis_value = extraer_value("inf_ea");
// var antitesis_value = extraer_value("sel_ea");
// var str = new RegExp(antitesis_value);
// 	if(str.test(tesis_value)){
// 		return false;
// 	}else{
// 		if(tesis_value==""){
// 			document.forms['pac_informe']['inf_ea'].value = antitesis_value;
// 		}else{
// 			document.forms['pac_informe']['inf_ea'].value = tesis_value+", "+antitesis_value;
// 		}
// 	}
// }
// function js_sel_antecedente(){
// var tesis_value = extraer_value("inf_antecedente");
// var antitesis_value = extraer_value("sel_antecedente");
// var str = new RegExp(antitesis_value);
// 	if(str.test(tesis_value)){
// 		return false;
// 	}else{
// 		if(tesis_value==""){
// 			document.forms['pac_informe']['inf_antecedente'].value = antitesis_value;
// 		}else{
// 			document.forms['pac_informe']['inf_antecedente'].value = tesis_value+", "+antitesis_value;
// 		}
// 	}
// }
// function js_sel_dx(){
// var tesis_value = extraer_value("inf_dx");
// var antitesis_value = extraer_value("sel_dx");
// var str = new RegExp(antitesis_value);
// 	if(str.test(tesis_value)){
// 		return false;
// 	}else{
// 		if(tesis_value==""){
// 			document.forms['pac_informe']['inf_dx'].value = antitesis_value;
// 		}else{
// 			document.forms['pac_informe']['inf_dx'].value = tesis_value+", "+antitesis_value;
// 		}
// 	}
// }
// function js_sel(){
// var tesis_value = extraer_value("inf_plan");
// var antitesis_value = document.forms[0]["sel_tto"].options[document.forms[0]["sel_tto"].selectedIndex].text;
// var str = new RegExp(antitesis_value);
// 	if(str.test(tesis_value)){
// 		return false;
// 	}else{
// 		if(tesis_value==""){
// 			document.forms['pac_informe']['inf_plan'].value = antitesis_value;
// 		}else{
// 			document.forms['pac_informe']['inf_plan'].value = tesis_value+", "+antitesis_value;
// 		}
// 	}
// 	setFocus("inf_plan");
// }
//









function js_save_hm(){
	new chk_hm();
}
function chk_hm(){
	var no_empty_field = [];
	var no_empty_field = ['inf_ef_fc','inf_ef_fr','sel_condition','inf_mc','inf_ea','inf_antecedente','inf_ef','inf_dx','inf_plan'];
	var num_empty_field = no_empty_field.length;
	for(it=0; it<num_empty_field; it++){
		if(isEmpty(no_empty_field[it])){
			setFocus(no_empty_field[it]);
			alert("Hay campos que no pueden estar vacio");
					return false;
		}
	}
new ins_add_hm();
}




function js_save_ef(){
	if(chk_ef()){
		alert("s true");
	}
}
function chk_ef(){
	var no_empty_field = [];
	var no_empty_field = ['inf_ef_fc','inf_ef_fr','sel_condition'];
	var num_empty_field = no_empty_field.length;
	for(it=0; it<num_empty_field; it++){
		if(isEmpty(no_empty_field[it])){
			setFocus(no_empty_field[it]);
			alert("Hay campos que no pueden estar vacio");
			return false();
		}
	}
	new js_paste_ef();
}
function js_paste_ef(){
	var tesis_value = extraer_value("inf_ef");
	var field = new Array()
	var field = ['inf_ef_fc','inf_ef_tas','inf_ef_tad','inf_ef_fr','inf_ef_temp','inf_ef_gc','sel_condition','inf_ef_respiracion','inf_ef_hidratacion','inf_ef_fiebre','inf_ef_pupila','inf_ef_head','inf_ef_piel','inf_ef_orl','inf_ef_neck','inf_ef_chest','inf_ef_abdomen','inf_ef_pelvis'];
	var num_field = field.length;
	for(it=0; it<num_field; it++){
		if(isSet(field[it])){
			if(it==0){
				var antitesis_value = "FC: "+extraer_value(field[it]);
			}
				else if(it==1){
				var antitesis_value = antitesis_value+" TA: "+extraer_value(field[it]);
			}
				else if(it==2){
				var antitesis_value = antitesis_value+"/"+extraer_value(field[it]);
			}
				else if(it==3){
				var antitesis_value = antitesis_value+" FR: "+extraer_value(field[it]);
			}
				else if(it==4){
				var antitesis_value = antitesis_value+" Temp: "+extraer_value(field[it]);
			}
				else if(it==5){
				var antitesis_value = antitesis_value+" GC: "+extraer_value(field[it]);
			}
				else if(it==6){
				var antitesis_value = antitesis_value+"\n"+extraer_value(field[it]);
			}
				else if(it==12){
				var antitesis_value = antitesis_value+", Piel: "+extraer_value(field[it]);
			}
				else if(it==13){
				var antitesis_value = antitesis_value+", ORL: "+extraer_value(field[it]);
			}
				else if(it==14){
				var antitesis_value = antitesis_value+", Cuello: "+extraer_value(field[it]);
			}
				else if(it==15){
				var antitesis_value = antitesis_value+", Torax: "+extraer_value(field[it]);
			}
				else if(it==16){
				var antitesis_value = antitesis_value+", Abdomen: "+extraer_value(field[it]);
			}
				else if(it==17){
				var antitesis_value = antitesis_value+", Pelvis: "+extraer_value(field[it]);
			}
				else{
				var antitesis_value = antitesis_value+", "+extraer_value(field[it]);
			}
		}
	}

	var str = new RegExp(antitesis_value);
	var result = str.test(tesis_value);
	if(result==true){
		return false;
	}else{
		if(tesis_value==""){
			document.getElementById("inf_ef").value = antitesis_value;
		}else{
			document.getElementById("inf_ef").value = tesis_value+", "+antitesis_value;
		}
	}
	new ins_add_hm();
}

function js_clear_hm(){
	var sel=['sel_condition','inf_ef_respiracion','inf_ef_hidratacion','inf_ef_fiebre','inf_ef_pupila'];
	var field = ['inf_ef_fc','inf_ef_tas','inf_ef_tad','inf_ef_fr','inf_ef_temp','inf_ef_gc','inf_ef_head','inf_ef_piel','inf_ef_orl','inf_ef_neck','inf_ef_chest','inf_ef_abdomen','inf_ef_pelvis','inf_mc','inf_ea','inf_antecedente','inf_ef','inf_dx','inf_plan'];
var num_field=field.length;
	for(it=0;it<num_field;it++){
		setEmpty(field[it]);
	}
var num_sel=sel.length;
for(ita=0;ita<num_sel;ita++){
	document.forms[0][sel[ita]].selectedIndex=0;
}
}
function openPopup_printhm(field){
	var field_print=[];
	var field_print=['inf_mc','inf_ea','inf_antecedente','inf_ef','inf_dx','inf_plan'];
	for(it=0;it<6;it++){
	if(isEmpty(field_print[it])){
			return false;
		}
	}
	popup_printinf = window.open(field.href, field.name, 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=215');
}

function openPopup_ordenes(field){
	popup_ordenes = window.open(field.href, field.name,         'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=0,alwaysRaised=1,dependent,titlebar=no,width=450,         height=567');
}


//////////////############################          FUNCIONES INSIDER          #############////////////////////////////////////

function js_sel_laboratory(){
var tesis_value = extraer_value("laboratory_value");
var antitesis_value = extraer_value("sel_laboratory");
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['form_laboratory']['laboratory_value'].value = antitesis_value+": ";
		}else{
			document.forms['form_laboratory']['laboratory_value'].value = tesis_value+"\n"+cap_fl(antitesis_value)+": ";
		}
	}
}

//#################### FUNCION CHECK LABHEMOGLOBINA ####################
content_labhemoglobina = "";
limit_labhemoglobina = 7;

function chk_labhemoglobina(f){
	var num_char = f.value.length;
	if(num_char > limit_labhemoglobina){
		f.value = content_labhemoglobina;
	}else{
		return content_labhemoglobina = f.value
	}
}

//#################### FUNCION CHECK LABHEMATOCRITO ####################
content_labhematocrito = "";
limit_labhematocrito = 7;

function chk_labhematocrito(f){
	var num_char = f.value.length;
	if(num_char > limit_labhematocrito){
		f.value = content_labhematocrito;
	}else{
		return content_labhematocrito = f.value
	}
}

//#################### FUNCION CHECK PLAQUETA ####################
content_labplaqueta = "";
limit_labplaqueta = 7;

function chk_labplaqueta(f){
	var num_char = f.value.length;
	if(num_char > limit_labplaqueta){
		f.value = content_labplaqueta;
	}else{
		return content_labplaqueta = f.value
	}
}

//#################### FUNCION CHECK GLOBULOS ROJOS ####################
content_labgrojo = "";
limit_labgrojo = 7;

function chk_labgrojo(f){
	var num_char = f.value.length;
	if(num_char > limit_labgrojo){
		f.value = content_labgrojo;
	}else{
		return content_labgrojo = f.value
	}
}

//#################### FUNCION CHECK UREA ####################
content_laburea = "";
limit_laburea = 7;

function chk_laburea(f){
	var num_char = f.value.length;
	if(num_char > limit_laburea){
		f.value = content_laburea;
	}else{
		return content_laburea = f.value
	}
}

//#################### FUNCION CHECK CREATININA ####################
content_labcreatinina = "";
limit_labcreatinina = 7;

function chk_labcreatinina(f){
	var num_char = f.value.length;
	if(num_char > limit_labcreatinina){
		f.value = content_labcreatinina;
	}else{
		return content_labcreatinina = f.value
	}
}

//#################### FUNCION CHECK GLOBULOS BLANCOS ####################
content_labgblanco = "";
limit_labgblanco = 7;

function chk_labgblanco(f){
	var num_char = f.value.length;
	if(num_char > limit_labgblanco){
		f.value = content_labgblanco;
	}else{
		return content_labgblanco = f.value
	}
}

//#################### FUNCION CHECK NEUTROFILO ####################
content_labneutrofilo = "";
limit_labneutrofilo = 7;

function chk_labneutrofilo(f){
	var num_char = f.value.length;
	if(num_char > limit_labneutrofilo){
		f.value = content_labneutrofilo;
	}else{
		return content_labneutrofilo = f.value
	}
}

//#################### FUNCION CHECK LINFOCITO ####################
content_lablinfocito = "";
limit_lablinfocito = 7;

function chk_lablinfocito(f){
	var num_char = f.value.length;
	if(num_char > limit_lablinfocito){
		f.value = content_lablinfocito;
	}else{
		return content_lablinfocito = f.value
	}
}

//#################### FUNCION CHECK MONOCITO ####################
content_labmonocito = "";
limit_labmonocito = 7;

function chk_labmonocito(f){
	var num_char = f.value.length;
	if(num_char > limit_labmonocito){
		f.value = content_labmonocito;
	}else{
		return content_labmonocito = f.value
	}
}

//#################### FUNCION CHECK EOSINOFILO ####################
content_labeosinofilo = "";
limit_labeosinofilo = 7;

function chk_labeosinofilo(f){
	var num_char = f.value.length;
	if(num_char > limit_labeosinofilo){
		f.value = content_labeosinofilo;
	}else{
		return content_labeosinofilo = f.value
	}
}

//#################### FUNCION CHECK BASOFILO ####################
content_labbasofilo = "";
limit_labbasofilo = 7;

function chk_labbasofilo(f){
	var num_char = f.value.length;
	if(num_char > limit_labbasofilo){
		f.value = content_labbasofilo;
	}else{
		return content_labbasofilo = f.value
	}
}
