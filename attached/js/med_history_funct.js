// JavaScript Document
//#################### FUNCION CHECK CEDULA ####################
content_cide = "";
limit_cide = 15;

function chk_cide(cide){
	cide.value = Solo_Numerico(cide.value);
	var num_char = cide.value.length;
	if(num_char > limit_cide){
		cide.value = content_cide;
	}else{
		return content_cide = cide.value	
	}
}
//#################### FUNCION CHECK NOMBRE ####################
content_nya = "";
limit_nya = 80;

function chk_nya(nya){
	var num_char = nya.value.length;
	if(num_char > limit_nya){
		nya.value = content_nya;
	}else{
		return content_nya = nya.value	
	}
}
//#################### FUNCION CHECK H ####################
content_h = "";
limit_h = 10;

function chk_h(h){
	var num_char = h.value.length;
	if(num_char > limit_h){
		h.value = content_h;
	}else{
		return content_h = h.value	
	}
}
//#################### FUNCION CHECK EDAD ####################
content_edad = "";
limit_edad = 3;

function chk_edad(edad){
	edad.value = Solo_Numerico(edad.value);
	var num_char = edad.value.length;
	if(num_char > limit_edad){
		edad.value = content_edad;
	}else{
		return content_edad = edad.value	
	}
}
//#################### FUNCION CHECK FC ####################
content_fc = "";
limit_fc = 3;

function chk_fc(fc){
	fc.value = Solo_Numerico(fc.value);
	var num_char = fc.value.length;
	if(num_char > limit_fc){
		fc.value = content_fc;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_fc = fc.value	
	}
}
//#################### FUNCION CHECK FR ####################
content_fr = "";
limit_fr = 2;

function chk_fr(fr){
	fr.value = Solo_Numerico(fr.value);
	var num_char = fr.value.length;
	if(num_char > limit_fr){
		fr.value = content_fr;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_fr = fr.value	
	}
}
//#################### FUNCION CHECK TA SISTOLICA ####################
content_tas = "";
limit_tas = 3;

function chk_tas(tas){
	tas.value = Solo_Numerico(tas.value);
	var num_char = tas.value.length;
	if(num_char == limit_tas){
		setFocus("inf_ef_tad");
	}
	if(num_char > limit_tas){
		tas.value = content_tas;
	}else{
		return content_tas = tas.value	
	}
}
//#################### FUNCION CHECK TA DIASTOLICA ####################
content_tad = "";
limit_tad = 3;

function chk_tad(tad){
	tad.value = Solo_Numerico(tad.value);
	var num_char = tad.value.length;
	if(num_char > limit_tad){
		tad.value = content_tad;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_tad = tad.value	
	}
}
//#################### FUNCION CHECK TEMPERATURA ####################
content_temp = "";
limit_temp = 2;

function chk_temp(temp){
	temp.value = Solo_Numerico(temp.value);
	var num_char = temp.value.length;
	if(num_char > limit_temp){
		temp.value = content_temp;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_temp = temp.value	
	}
}
//#################### FUNCION CHECK GLICEMIA ####################
content_gc = "";
limit_gc = 3;

function chk_gc(gc){
	gc.value = Solo_Numerico(gc.value);
	var num_char = gc.value.length;
	if(num_char > limit_gc){
		gc.value = content_gc;
//		alert("Ah Llegado al Maximo de Caracteres Permitidos.");
	}else{
		return content_gc = gc.value	
	}
}
//#################### FUNCION SELECTS ####################
function js_sel_piel(){
var tesis_value = extraer_value("inf_ef_piel");
var antitesis_value = extraer_value("sel_ef_piel");
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_ef_piel'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_ef_piel'].value = tesis_value+", "+antitesis_value;
		}
	}
}
function js_sel_head(){
var tesis_value = extraer_value("inf_ef_head");
var antitesis_value = extraer_value("sel_ef_head");
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_ef_head'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_ef_head'].value = tesis_value+", "+antitesis_value;
		}
	}
}
function js_sel_orl(){
var tesis_value = extraer_value("inf_ef_orl");
var antitesis_value = extraer_value("sel_ef_orl");
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_ef_orl'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_ef_orl'].value = tesis_value+", "+antitesis_value;
		}
	}
}
function js_sel_neck(){
var tesis_value = extraer_value("inf_ef_neck");
var antitesis_value = extraer_value("sel_ef_neck");
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_ef_neck'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_ef_neck'].value = tesis_value+", "+antitesis_value;
		}
	}
}

function js_sel_torax(antitesis_value){
var tesis_value = extraer_value("inf_ef_chest");
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_ef_chest'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_ef_chest'].value = tesis_value+", "+antitesis_value;
		}
	}
}
function js_sel_respiratory(){
var antitesis_value = extraer_value("sel_ef_respiratory");
new js_sel_torax(antitesis_value);
}
function js_sel_cardiac(){
var antitesis_value = extraer_value("sel_ef_cardiac");
new js_sel_torax(antitesis_value);
}


function js_sel_abdomen(antitesis_value){
var tesis_value = document.forms['pac_informe']['inf_ef_abdomen'].value;
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_ef_abdomen'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_ef_abdomen'].value = tesis_value+", "+antitesis_value;
		}
	}
}
function js_sel_abd_auscultation(){
	var antitesis_value = extraer_value("sel_ef_abd_auscultation");
	new js_sel_abdomen(antitesis_value);
}
function js_sel_abd_inspection(){
	var antitesis_value = extraer_value("sel_ef_abd_inspection");
	new js_sel_abdomen(antitesis_value);
}
function js_sel_abd_palpation(){
	var antitesis_value =  extraer_value("sel_ef_abd_palpation");
	new js_sel_abdomen(antitesis_value);
}

function js_sel_pelvis(){
var tesis_value = extraer_value("inf_ef_pelvis");
var antitesis_value = extraer_value("sel_ef_pelvis");
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_ef_pelvis'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_ef_pelvis'].value = tesis_value+", "+antitesis_value;
		}
	}
}







function js_sel_motivo(){
var tesis_value = extraer_value("inf_mc");
var antitesis_value = extraer_value("sel_mc");
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_mc'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_mc'].value = tesis_value+", "+antitesis_value;
		}
	}
}
function js_sel_ea(){
var tesis_value = extraer_value("inf_ea");
var antitesis_value = extraer_value("sel_ea");
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_ea'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_ea'].value = tesis_value+", "+antitesis_value;
		}
	}
}
function js_sel_antecedente(){
var tesis_value = extraer_value("inf_antecedente");
var antitesis_value = extraer_value("sel_antecedente");
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_antecedente'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_antecedente'].value = tesis_value+", "+antitesis_value;
		}
	}
}
function js_sel_dx(){
var tesis_value = extraer_value("inf_dx");
var antitesis_value = extraer_value("sel_dx");
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_dx'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_dx'].value = tesis_value+", "+antitesis_value;
		}
	}
}
function js_sel(){
var tesis_value = extraer_value("inf_plan");
var antitesis_value = document.forms[0]["sel_tto"].options[document.forms[0]["sel_tto"].selectedIndex].text;
var str = new RegExp(antitesis_value);
	if(str.test(tesis_value)){
		return false;
	}else{
		if(tesis_value==""){
			document.forms['pac_informe']['inf_plan'].value = antitesis_value;
		}else{
			document.forms['pac_informe']['inf_plan'].value = tesis_value+", "+antitesis_value;
		}
	}
	setFocus("inf_plan");
}





function js_save_pac_info(){
	new chk_pac_info();
}
function chk_pac_info(){	
	var no_empty_field = [];
	var no_empty_field = ['pac_ci','pac_nya','pac_edad','pac_h'];
	var num_empty_field = no_empty_field.length;
	for(it=0; it<num_empty_field; it++){ 
		if(isEmpty(no_empty_field[it])){
			setFocus(no_empty_field[it]);
			alert("Hay campos que no pueden estar vacio");
					return false;
		}
	}
	new add_pac_info();
}


function js_save_hm(){
	new chk_hm();
}
function chk_hm(){
	var no_empty_field = [];
	var no_empty_field = ['inf_ef_fc','inf_ef_fr','sel_condition','pac_ci','pac_nya','pac_edad','pac_h','inf_mc','inf_ea','inf_antecedente','inf_ef','inf_dx','inf_plan'];
	var num_empty_field = no_empty_field.length;
	for(it=0; it<num_empty_field; it++){ 
		if(isEmpty(no_empty_field[it])){
			setFocus(no_empty_field[it]);
			alert("Hay campos que no pueden estar vacio");
					return false;
		}	
	}
new add_hm();
}


function js_save_ef(){
	new chk_ef();
}
function chk_ef(){
	var no_empty_field = [];
	var no_empty_field = ['inf_ef_fc','inf_ef_fr','sel_condition','pac_ci','pac_nya','pac_edad','pac_h'];
	var num_empty_field = no_empty_field.length;
	for(it=0; it<num_empty_field; it++){ 
		if(isEmpty(no_empty_field[it])){
			setFocus(no_empty_field[it]);
			alert("Hay campos que no pueden estar vacio");
			return false();
		}
	}
	return true();
//	new js_paste_ef();
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
				var antitesis_value = antitesis_value+", Cuello: Se evidencia "+extraer_value(field[it]);
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
	new add_hm();
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
function js_imp_informe(){
	var field_print=[];
	var field_print=['pac_ci','fecha_actual','inf_mc','inf_ea','inf_antecedente','inf_ef','inf_dx','inf_plan'];
	for(it=0;it<8;it++){
		if(isEmpty(field_print[it])){
			return false;
		}
	}
	window.open('popup_printinf.php', 'Nombre de la ventana', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=215');
/*	location.href="informe_word.php?q="+ci+"&r="+fecha+"";
*/
}

function openPopup_lab(field){
	var name_popup = field.name;
	popup_lab = window.open(field.href, field.name,         'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=0,alwaysRaised=1,dependent,titlebar=no,width=400,         height=545');
}


//////////////############################          FUNCIONES INSIDER          #############////////////////////////////////////








