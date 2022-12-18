<ul class="nav nav-tabs">
					  <li class="active"><a data-toggle="tab" href="#delivered_ff">Factura</a></li>
						<li><a data-toggle="tab" href="#delivered_client">Cliente</a></li>
						<li><a data-toggle="tab" href="#delivered_product">Producto</a></li>
					</ul>
					<div class="tab-content">
						<!-- ###########################    FILTRAR POR FACTURA   #######################  -->
						<div id="delivered_ff" class="container-fluid no_padding tab-pane fade in active">
							<div id="container_leftside" class="col-xs-12 col-sm-12 col-md-4 col-lg-4 no_padding br_1 px_7 pt_14">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding py_7">
									<div id="container_txtdateinitial" class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pl_0 pr_7">
										<label class="label label_blue_sky" for="txt_date_initial_deliveredff">F. Inicio</label>
										<input type="text" id="txt_date_initial_deliveredff" class="form-control" readonly="readonly" value="<?php echo $fecha_i; ?>" />
									</div>
									<div id="container_txtdatefinal" class="col-xs-6 col-sm-6 col-md-6 col-lg-6 px_0">
										<label class="label label_blue_sky" for="txt_date_final_deliveredff">F. Final</label>
										<input type="text" id="txt_date_final_deliveredff" class="form-control" readonly="readonly" value="<?php echo $fecha_f; ?>" />
									</div>
								</div>
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding">
									<input type="text" id="txt_filter_deliveredff" class="form-control" value="" placeholder="No. de Factura o Cliente">
								</div>
								<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 no_padding side-btn-md px_7">
									<button type="button" id="btn_filter_deliveredff" class="btn btn-success" onclick="filter_deliveredff()"><i class="fa fa-search"></i></button>
								</div>
								<div id="container_rlimit" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 px_0">
									<label class="label label_blue_sky" for="txt_rlimit">Mostrar:</label><br />
									<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="20"  checked="checked" /> 20</label>
									<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="50" /> 50</label>
								</div>
								<div id="container_tbl_deliveredff" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" >
									&nbsp;
								</div>
							</div>
							<div id="container_rightside" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 no_padding br_1 px_7 pt_14">
								<div id="tbl_delivered_facturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table">
									<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-primary">
											<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1"						>CODIGO</div>
											<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 cell no_padding br_1"	>DESCRIPCION</div>
											<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1"	>CANTIDAD</div>
											<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding"			>ENTREGADO</div>
										</div>
									</div>
									<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-primary">&nbsp;</div>
							 	</div>
								<div id="tbl_delivered_previus_facturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7">
									<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg_red">
											<div id="span_count_delivered" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 cell al_left">Entregas Previas ( )</div>
										</div>
									</div>
									<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">&nbsp;</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg_red">&nbsp;</div>
								</div>
							</div>
						</div>
						<!-- ########################       ENTREGADOS POR CLIENTE      ####################### -->
						<div id="delivered_client" class="container-fluid no_padding tab-pane fade  " >
							<div id="container_leftside" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding br_1 px_7 pt_14">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									<label for="txt_filter_deliveredclient" class="label label_blue_sky">Buscar</label>
									<input type="text" id="txt_filter_deliveredclient" class="form-control" placeholder="Nombre del Cliente" />
								</div>
								<div id="container_delivered_client_recall" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
								<div id="tbl_delivered_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7 no_padding">
									<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-primary">
											<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 cell br_1">FACTURA</div>
											<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1">FECHA</div>
											<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1">C&Oacute;DIGO</div>
											<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 cell br_1">DESCRIPCI&Oacute;N</div>
											<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 cell br_1 px_0">CANTIDAD</div>
											<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 cell br_1 px_0">ENTREGA</div>
										</div>
									</div>
									<div id="delivered_client_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 border_1 no_padding">&nbsp;</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-primary">&nbsp;</div>
								</div>
								<div id="tbl_delivered_previus_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7 px_0">
									<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg_red">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 cell al_left">Entregas Previas</div>
										</div>
									</div>
									<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg_red">&nbsp;</div>
								</div>
							</div>
						</div>
						<div id="delivered_product" class="container-fluid no_padding tab-pane fade  " >
							<div id="container_leftside" class="col-xs-12 col-sm-12 col-md-4 col-lg-4 no_padding br_1 px_7 pt_14">
								<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 no_padding">
									<label for="txt_filterproduct_delivered" class="label label_blue_sky">Buscar Producto</label>
									<input type="text" class="form-control" id="txt_filterproduct_delivered" value="" placeholder="Descripci&oacute;n o C&oacute;digo del Producto">
								</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding side-btn-md px_7 pt_14">
									<button type="button" id="btn_filter_product_delivered" class="btn btn-success" onclick="filter_product_delivered()"><i class="fa fa-search"></i></button>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									<div id="tbl_product_delivered" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7 px_0">
										<div id="content_caption" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding al_left">&nbsp;</div>
										<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-info">
												<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell al_center">CODIGO</div>
												<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell al_center">DESCRIPCION</div>
											</div>
										</div>
										<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">&nbsp;</div>
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-info">&nbsp;</div>
									</div>
								</div>
							</div>
							<div id="container_rightside" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 no_padding br_1 px_7 pt_14">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									<div id="tbl_delivered_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7 px_0">
										<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-primary">
												<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell al_center">CLIENTE</div>
												<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell al_center">FACTURA</div>
												<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell al_center">FECHA/HORA</div>
												<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell al_center">CANTIDAD</div>
												<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell al_center">ENTREGADO</div>
											</div>
										</div>
										<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">&nbsp;</div>
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-primary">&nbsp;</div>
									</div>
								</div>
								<div id="tbl_delivered_previus_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7 px_0">
									<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg_red">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 cell al_left">Entregas Previas</div>
										</div>
									</div>
									<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg_red">&nbsp;</div>
								</div>
							</div>
						</div>
					</div>