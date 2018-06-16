<?php

// echo phpinfo();
// var_dump(printer_list(PRINTER_ENUM_LOCAL | PRINTER_ENUM_SHARED));
//$handle = printer_open("HP LaserJet Pro MFP M127fn");
// $handle = printer_open("PDF Complete");
// $handle = printer_open("\\\\TRIILLI-CAJA\\HP LaserJet Pro MFP M127-M128 PCLmS");
// $handle = printer_open("HP LaserJet Pro MFP M127fn");
if (!$handle || $handle == NULL) { echo "no conecto"; }else{ echo "si conecto";}
    printer_start_doc($handle, "");
    printer_start_page($handle);
    //
    // $font = printer_create_font("Arial", 100, 50, PRINTER_FW_MEDIUM, false, false, false, 0);
    // printer_select_font($handle, $font);
    printer_set_option($handle, PRINTER_MODE, 'raw');
    printer_draw_text($handle, "PHP is simply cool", 400, 400);
    // printer_delete_font($font);
// $handle = printer_open();
// $text="chr(27).'p025'";
// $text="Texto a imprimir";
// $ejecuta = printer_write($handle, $text);
// if($ejecuta){echo "its all ok";}else{echo "too bad";};
//
// printer_close($handle);


printer_end_page($handle);
printer_end_doc($handle);
printer_close($handle);
?>
