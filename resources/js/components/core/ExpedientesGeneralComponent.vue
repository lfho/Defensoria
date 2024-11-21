<template>
   <div class="container">
      <!-- Modal que se va a abrir -->
      <b-modal v-model="mostrarModal" title="Vincular documento a expediente electrónico" @hide="cerrarModal" size="completo" header-class="bg-blue text-white" hide-footer>
         <div class="info-box" v-if="dataForm.consecutivo_expediente">
            <p><strong>Este registro ya está asociado a un expediente.</strong></p>
            <p><strong>Consecutivo del expediente: <a :href="'/expedientes-electronicos/expedientes?qderExpediente='+dataForm.id_expediente_info">{{ dataForm.consecutivo_expediente }}</a></strong></p>
         </div>
         <div style="display: flex;" v-else>
            <div class="modal-body column col-md-6">
               <div class="form-group row m-b-15" v-if="!dataForm.que_desea_hacer">
                  <label class="col-form-label col-md-3">¿Qué desea hacer?:</label>
                  <div class="col-md-6">
                     <select class="form-control" name="que_desea_hacer"
                        id="que_desea_hacer" v-model="dataForm.que_desea_hacer" required>
                        <option value="1">Incluir documento a un expediente</option>
                        <option value="2">Crear expediente e incluir documento</option>
                     </select>
                  </div>
               </div>
               <div v-if="dataForm.que_desea_hacer == '1'">
                  <div class="panel" data-sortable-id="ui-general-1">
                     <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Detalle del documento que se vinculará al expediente</strong></h4>
                     </div>
                     <div class="panel-body">
                        <viewer-attachement :list="valorRegistro.document_pdf" :key="valorRegistro.document_pdf"></viewer-attachement>
                     </div>
                  </div>
                  <div class="panel panel-default">
                     <div class="panel-heading" style="background-color: #8ED6F0; padding: 10px; border-radius: 5px;">
                        <h4 class="panel-title">
                           <a data-toggle="collapse" href="#collapseFilters" aria-expanded="true" style="color: black; text-decoration: none;">
                              <strong>Filtros de búsqueda</strong>
                           </a>
                        </h4>
                     </div>
                     <div id="collapseFilters" class="panel-collapse collapse in">
                        <div class="panel-body">
                           <!-- Campo de texto y botón para la búsqueda -->
                           <div class="form-group row m-b-15">
                              <label class="col-form-label col-md-3">Nombre expediente:</label>
                              <div class="col-md-6">
                                 <input v-model="camposFiltros.nombre_tipo_expediente" type="text" class="form-control" placeholder="Escribe el nombre" />
                              </div>
                           </div>
                           <div class="form-group row m-b-15">
                              <label class="col-form-label col-md-3">Usuario responsable:</label>
                              <div class="col-md-6">
                                 <input v-model="camposFiltros.vigencia" type="text" class="form-control" placeholder="Escribe el responsable" />
                              </div>
                           </div>
                           <div class="form-group row m-b-15">
                              <label class="col-form-label col-md-3">Fecha de inicio:</label>
                              <div class="col-md-6">
                                 <date-picker
                                    :value="camposFiltros"
                                    name-field="fecha_inicio_expediente"
                                    mode="range"
                                    :input-props="{required: false}">
                                 </date-picker>
                              </div>
                           </div>
                           <div class="form-group row m-b-15">
                              <label class="col-form-label col-md-3">Oficina productora:</label>
                              <div class="col-md-6">
                                 <select-check css-class="form-control" name-field="classification_production_office" reduce-label="nombre" name-resource="/intranet/get-dependencies" :is-required="true" :value="camposFiltros" :enable-search="true" :ids-to-empty="['classification_serie','classification_subserie']"></select-check>
                              </div>
                           </div>
                           <div class="form-group row m-b-15">
                              <label class="col-form-label col-md-3">Serie:</label>
                              <div class="col-md-6">
                                 <select-check css-class="form-control" name-field="classification_serie" reduce-label="name" reduce-key="id_series_subseries" :is-required="false" :name-resource="'/documentary-classification/get-inventory-documentals-serie-dependency/'+ camposFiltros.classification_production_office"
                                 :value="camposFiltros" :enable-search="true" :key="camposFiltros.classification_production_office"></select-check>
                              </div>
                           </div>
                           <div class="form-group row m-b-15">
                              <label class="col-form-label col-md-3">Subserie:</label>
                              <div class="col-md-6">
                                 <select-check css-class="form-control" name-field="classification_subserie" reduce-label="name_subserie" :name-resource="'/documentary-classification/get-subseries-clasificacion?serie='+camposFiltros.classification_serie" :is-required="false" :value="camposFiltros" :key="camposFiltros.classification_serie" :enable-search="true"></select-check>
                              </div>
                           </div>
                           <div class="form-group row m-b-15">
                              <div class="col-md-3">
                                 <button @click="buscarExpediente" class="btn btn-primary">Buscar</button>
                              </div>
                              <div class="col-md-3">
                                 <button @click="limpiarFiltros" class="btn btn-primary">Limpiar</button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="panel" data-sortable-id="ui-general-1">
                     <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Detalles del expediente electrónico a vincular</strong></h4>
                     </div>
                     <div class="panel-body">
                        <div class="alert alert-success" role="alert">
                           Para vincular el documento, es crucial ingresar el número consecutivo del expediente.
                        </div>
                        <div class="form-group row m-b-15">
                           <div class="col-form-label col-md-3"><label class="required">Consecutivo:</label></div>
                           <div class="col-md-9">
                              <autocomplete
                                 name-prop="consecutivo"
                                 :name-field="moduloConsecutivo"
                                 :value='dataForm'
                                 :is-required="true"
                                 name-resource="/expedientes-electronicos/obtener-expediente"
                                 css-class="form-control"
                                 :name-labels-display="['consecutivo']"
                                 :fields-change-values="['document_pdf:document_pdf', 'id:id']"
                                 :function-change="obtenerDocumento"
                                 reduce-key="consecutivo"
                                 name-field-edit="moduloConsecutivo">
                              </autocomplete>
                           </div>
                        </div>
                        <div class="form-group row m-b-15" v-if="dataForm.id">
                           <div class="col-form-label col-md-3"><label class="required">Tipo documental:</label></div>
                           <div class="col-md-9">
                              <select-check
                                 css-class="form-control"
                                 name-field="ee_tipos_documentales_id"
                                 reduce-label="name"
                                 :name-resource="getEncryptedResourceUrl()"
                                 :value="dataForm"
                                 :is-required="true"
                                 ref-select-check="tipos_documentales_ref"
                                 :enable-search="true">
                              </select-check>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="panel" data-sortable-id="ui-general-1">
                     <div class="panel-body">
                        <div class="form-group row m-b-15">
                           <div>
                              <table class="table">
                                 <thead>
                                    <tr>
                                       <th>Consecutivo</th>
                                       <th>Serie</th>
                                       <th>Subserie</th>
                                       <th>Fecha inicio expediente</th>
                                       <th>Estado</th>
                                       <th>Acción</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr v-for="(resultado, index) in resultados" :key="index">
                                       <td>{{ resultado.consecutivo }}</td>
                                       <td>{{ resultado.classification_serie }}</td>
                                       <td>{{ resultado.classification_subserie }}</td>
                                       <td>{{ resultado.fecha_inicio_expediente }}</td>
                                       <td>{{ resultado.estado }}</td>
                                       <td>
                                          <a
                                             @click="seleccionarExpediente(resultado.ee_documentos_expedientes[0].adjunto, resultado.consecutivo);"
                                             class="btn btn-white btn-icon btn-md"
                                             data-toggle="tooltip"
                                             data-placement="top"
                                             title="Seleccionar">
                                             <i class="far fa-caret-square-up"></i>
                                          </a>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div v-if="dataForm.que_desea_hacer == '2'">
                  <div class="panel" data-sortable-id="ui-general-1">
                     <!-- begin panel-heading -->
                     <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Nuevo expediente</strong></h4>
                     </div>
                     <!-- end panel-heading -->
                     <!-- begin panel-body -->
                     <div class="panel-body">
                        <!-- classification_production_office Field -->
                        <div class="form-group row m-b-15">
                              <!-- Campo Oficina Productora -->
                              <div class="col-form-label col-md-3"><label class="required">Oficina productora:</label></div>
                              <div class="col-md-9">
                                 <select-check 
                                    css-class="form-control" 
                                    name-field="classification_production_office" 
                                    reduce-label="nombre" 
                                    name-resource="/intranet/get-dependencies" 
                                    :is-required="true" 
                                    :value="dataForm" 
                                    :enable-search="true" 
                                    :ids-to-empty="['classification_serie','classification_subserie']">
                                 </select-check>
                                 <small>Seleccione una oficina productora para obtener las series relacionadas.</small>
                                 <div class="invalid-feedback" v-if="dataErrors.classification_production_office">
                                    <p class="m-b-0" v-for="error in dataErrors.classification_production_office">
                                          @{{ error }}
                                    </p>
                                 </div>
                              </div>
                        </div>
                        
                        <div class="form-group row m-b-15">
                           <!-- Campo Serie -->
                           <div class="col-form-label col-md-3"><label class="required">Serie:</label></div>
                           <div class="col-md-9">
                              <select-check 
                                 css-class="form-control" 
                                 name-field="classification_serie" 
                                 reduce-label="name" 
                                 reduce-key="id_series_subseries" 
                                 :is-required="true" 
                                 :name-resource="'/documentary-classification/get-inventory-documentals-serie-dependency/' + dataForm.classification_production_office"
                                 :value="dataForm" 
                                 :enable-search="true" 
                                 name-field-object="serie_clasificacion_documental"
                                 :key="dataForm.classification_production_office">
                              </select-check>
                              <small>Seleccione una serie documental, ejemplo: Contratos.</small>
                              <div class="invalid-feedback" v-if="dataErrors.classification_serie">
                                 <p class="m-b-0" v-for="error in dataErrors.classification_serie">
                                       @{{ error }}
                                 </p>
                              </div>
                           </div>
                        </div>
                        
                        <div class="form-group row m-b-15">
                           <!-- Campo Subserie -->
                           <div class="col-form-label col-md-3"><label class="required">Subserie:</label></div>
                           <div class="col-md-9">
                              <select-check 
                                 css-class="form-control" 
                                 name-field="classification_subserie" 
                                 reduce-label="name_subserie" 
                                 :name-resource="'/documentary-classification/get-subseries-clasificacion?serie=' + dataForm.classification_serie" 
                                 :is-required="false" 
                                 :value="dataForm" 
                                 :key="dataForm.classification_serie" 
                                 name-field-object="subserie_clasificacion_documental"
                                 :enable-search="true">
                              </select-check>
                              <small>Seleccione una sub-serie documental, ejemplo: Contratos de trabajo.</small>
                              <div class="invalid-feedback" v-if="dataErrors.classification_subserie">
                                 <p class="m-b-0" v-for="error in dataErrors.classification_subserie">
                                       @{{ error }}
                                 </p>
                              </div>
                           </div>
                        </div>
                        

                        <div class="form-group row m-b-15">
                           <div class="col-form-label col-md-3"><label class="required">Tipo de expediente:</label></div>
                              <div class="col-md-9">
                                 <select-check
                                 css-class="form-control"
                                 name-field="ee_tipos_expedientes_id"
                                 reduce-label="nombre"
                                 :fields-change-values="['nombre_expediente:nombre']"
                                 name-resource="/expedientes-electronicos/get-tipo-expediente"
                                 name-field-object="tipo_expediente_datos"
                                 :value="dataForm" 
                                 :is-required="true" 
                                 name-field-edit="ee_tipos_expedientes_id"
                                 :enable-search="true"></select-check>
                                 <small>Seleccione el tipo del expediente.</small>
                                 <div class="invalid-feedback" v-if="dataErrors.ee_tipos_expedientes_id">
                                    <p class="m-b-0" v-for="error in dataErrors.ee_tipos_expedientes_id">@{{ error }}</p>
                                 </div>
                              </div>
                        </div>

                        <div class="form-group row m-b-15">
                           <div class="col-form-label col-md-3"><label class="required">Fecha inicio del expediente:</label></div>
                              <div class="col-md-9">
                                 <input type="date" name="fecha_inicio_expediente" id="fecha_inicio_expediente" v-model="dataForm.fecha_inicio_expediente">
                                 <small>Ingrese la fecha inicial</small>
                                 <div class="invalid-feedback" v-if="dataErrors.fecha_inicio_expediente">
                                    <p class="m-b-0" v-for="error in dataErrors.fecha_inicio_expediente">@{{ error }}</p>
                                 </div>
                              </div>
                        </div>

                        <div class="form-group row m-b-15">
                           <div class="col-form-label col-md-3"><label class="required">Descripción:</label></div>
                              <div class="col-md-9">
                                 <textarea cols="50" rows="10" class="form-control" v-model="dataForm.descripcion"></textarea>
                                 <small>Ingrese una descripción.</small>
                                 <div class="invalid-feedback" v-if="dataErrors.descripcion">
                                    <p class="m-b-0" v-for="error in dataErrors.descripcion">
                                          @{{ error }}</p>
                                 </div>
                              </div>
                        </div>

                        <div class="form-group row m-b-15">
                           <div class="col-form-label col-md-3"><label class="required">Responsable:</label></div>
                              <div class="col-md-9">
                                 <select-check css-class="form-control" name-field="id_responsable" reduce-label="fullname" name-resource="/expedientes-electronicos/obtener-responsable" :value="dataForm" :is-required="true" :enable-search="true"></select-check>
                                 <small>Ingrese el funcionario destinatario</small>
                                 <div class="invalid-feedback" v-if="dataErrors.id_responsable">
                                    <p class="m-b-0" v-for="error in dataErrors.id_responsable">@{{ error }}</p>
                                 </div>
                              </div>
                        </div>
                     </div>
                  </div>
                  <div class="panel" data-sortable-id="ui-general-1" v-if="dataForm.subserie_clasificacion_documental?.criterios_busqueda.length > 0 && dataForm.subserie_clasificacion_documental">
                     <!-- begin panel-heading -->
                     <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Metadatos de la Subserie</strong></h4>
                     </div>
                     <!-- end panel-heading -->
                     <!-- begin panel-body -->
                     <div class="panel-body">
                        <div class="form-group row m-b-15" v-for="metadato in dataForm.subserie_clasificacion_documental?.criterios_busqueda">
                              <label for="nombre_metadato" class="col-form-label col-md-3" :class="{'required': metadato.requerido}">@{{ metadato.nombre }}:</label>
                              <div class="col-md-9">

                                 <input 
                                    v-if="metadato && metadato.tipo_campo !== 'Lista'" 
                                    :type="metadato.tipo_campo === 'Texto' ? 'text' : (metadato.tipo_campo === 'Número' ? 'number' : 'date')" 
                                    v-model="dataForm.metadatos[metadato.id]" 
                                    :name="metadato.id || ''" 
                                    :id="metadato.id || ''" 
                                    class="form-control" 
                                    :required="metadato.requerido">
                                 
                                    <select 
                                          v-else-if="metadato" 
                                          v-model="dataForm.metadatos[metadato.id]" 
                                          :name="metadato.id || ''" 
                                          :id="metadato.id || ''" 
                                          class="form-control" 
                                          :required="metadato.requerido">
                                          <option v-for="(value, key) in parseOpciones(metadato.opciones)" :value="key">@{{ value }}</option>
                                    </select>
                                 
                                 <small v-if="metadato">@{{ metadato.texto_ayuda }}</small>
                              </div>
                        </div>
                     </div>
                  </div>

                  <div class="panel" data-sortable-id="ui-general-1" v-else-if="dataForm.serie_clasificacion_documental?.series_osubseries?.criterios_busqueda.length > 0 && dataForm.serie_clasificacion_documental">
                     <!-- begin panel-heading -->
                     <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Metadatos</strong></h4>
                     </div>
                     <!-- end panel-heading -->
                     <!-- begin panel-body -->
                     <div class="panel-body">
                        <div class="form-group row m-b-15" v-for="metadato in dataForm.serie_clasificacion_documental?.series_osubseries?.criterios_busqueda">
                              <label for="nombre_metadato" class="col-form-label col-md-3" :class="{'required': metadato.requerido}">@{{ metadato.nombre }}:</label>
                              <div class="col-md-9">

                                 <input 
                                    v-if="metadato && metadato.tipo_campo !== 'Lista'" 
                                    :type="metadato.tipo_campo === 'Texto' ? 'text' : (metadato.tipo_campo === 'Número' ? 'number' : 'date')" 
                                    v-model="dataForm.metadatos[metadato.id]" 
                                    :name="metadato.id || ''" 
                                    :id="metadato.id || ''" 
                                    class="form-control" 
                                    :required="metadato.requerido">
                                 
                                    <select 
                                          v-else-if="metadato" 
                                          v-model="dataForm.metadatos[metadato.id]" 
                                          :name="metadato.id || ''" 
                                          :id="metadato.id || ''" 
                                          class="form-control" 
                                          :required="metadato.requerido">
                                          <option v-for="(value, key) in parseOpciones(metadato.opciones)" :value="key">@{{ value }}</option>
                                    </select>
                                 
                                 <small v-if="metadato">@{{ metadato.texto_ayuda }}</small>
                              </div>
                        </div>
                     </div>
                  </div>

                  <div class="panel" data-sortable-id="ui-general-1">
                     <!-- begin panel-heading -->
                     <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Información general</strong></h4>
                     </div>
                     <!-- end panel-heading -->
                     <!-- begin panel-body -->
                     <div class="panel-body">
                        <!--  Permiso Crear Documentos Todas Field -->
                        <div class="form-group row m-b-15">
                           <div class="col-form-label col-md-3"><label class="required">Existe físicamente:</label></div>
                              <div class="col-md-9">
                                 <select class="form-control" name="existe_fisicamente"
                                    id="existe_fisicamente" v-model="dataForm.existe_fisicamente" required>
                                    <option value="Si">Si</option>
                                    <option value="No">No</option>
                                 </select>
                                 <small>seleccione si el expediente existe físicamente</small>
                                 <div class="invalid-feedback" v-if="dataErrors.existe_fisicamente">
                                    <p class="m-b-0" v-for="error in dataErrors.existe_fisicamente">
                                          @{{ error }}</p>
                                 </div>
                              </div>
                        </div>

                        <div v-if="dataForm.existe_fisicamente == 'Si'">

                              <div class="form-group row m-b-15">
                                 <div class="col-form-label col-md-3"><label class="required">Ubicación:</label></div>
                                 <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="dataForm.ubicacion">
                                    <small>Ingresa la ubicación del expediente.</small>
                                 </div>
                              </div>

                              <div class="form-group row m-b-15">
                                 <div class="col-form-label col-md-3"><label class="required">Sede:</label></div>
                                 <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="dataForm.sede">
                                    <small>Ingresa la sede del expediente.</small>
                                 </div>
                              </div>

                              <!-- Dependencia Field -->
                              <div class="form-group row m-b-15">
                                 <div class="col-form-label col-md-3"><label class="required">Dependencia:</label></div>
                                 <div class="col-md-9">
                                    <select-check css-class="form-control" name-field="dependencias_id" reduce-label="nombre"
                                          :value="dataForm" :is-required="true" :enable-search="true" :is-multiple="false" name-resource="/intranet/get-dependencies">
                                    </select-check>
                                    <small>Seleccione la dependencia</small>
                                    <div class="invalid-feedback" v-if="dataErrors.dependencia">
                                          <p class="m-b-0" v-for="error in dataErrors.dependencia">@{{ error }}</p>
                                    </div>
                                 </div>
                              </div>

                              <div class="form-group row m-b-15">
                                 <div class="col-form-label col-md-3"><label class="required">Area de archivo:</label></div>
                                 <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="dataForm.area_archivo">
                                    <small>Ingresa la Area de archivo del expediente.</small>
                                 </div>
                              </div>

                              <div class="form-group row m-b-15">
                                 <div class="col-form-label col-md-3"><label class="required">Estante:</label></div>
                                 <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="dataForm.estante">
                                    <small>Ingresa la Estante del expediente.</small>
                                 </div>
                              </div>

                              <div class="form-group row m-b-15">
                                 <div class="col-form-label col-md-3"><label class="required">Módulo:</label></div>
                                 <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="dataForm.modulo">
                                    <small>Ingresa la Módulo del expediente.</small>
                                 </div>
                              </div>

                              <div class="form-group row m-b-15">
                              <div class="col-form-label col-md-3"><label class="required">Entrepaño:</label></div>
                                 <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="dataForm.entrepano">
                                    <small>Ingresa la Entrepaño del expediente.</small>
                                 </div>
                              </div>

                              <div class="form-group row m-b-15">
                                 <div class="col-form-label col-md-3"><label class="required">Caja:</label></div>
                                 <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="dataForm.caja">
                                    <small>Ingresa la Caja del expediente.</small>
                                 </div>
                              </div>

                              <div class="form-group row m-b-15">
                                 <div class="col-form-label col-md-3"><label class="required">Cuerpo:</label></div>
                                 <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="dataForm.cuerpo">
                                    <small>Ingresa la Cuerpo del expediente.</small>
                                 </div>
                              </div>

                              <div class="form-group row m-b-15">
                                 <div class="col-form-label col-md-3"><label class="required">Unidad de conservación:</label></div>
                                 <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="dataForm.unidad_conservacion">
                                    <small>Ingresa la Unidad de conservación del expediente.</small>
                                 </div>
                              </div>

                              <div class="form-group row m-b-15">
                                 <div class="col-form-label col-md-3"><label class="required">Fecha de archivo:</label></div>
                                 <div class="col-md-9">
                                    <input type="date" name="" id="" v-model="dataForm.fecha_archivo">
                                    <small>Ingrese la fecha del archivo</small>
                                    <div class="invalid-feedback" v-if="dataErrors.fecha_archivo">
                                          <p class="m-b-0" v-for="error in dataErrors.fecha_archivo">@{{ error }}</p>
                                    </div>
                                 </div>
                              </div>

                              <div class="form-group row m-b-15">
                                 <div class="col-form-label col-md-3"><label class="required">Número de inventario:</label></div>
                                 <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="dataForm.numero_inventario">
                                    <small>Ingresa la Unidad de conservación del expediente.</small>
                                 </div>
                              </div>
                        </div>
                     </div>
                  </div>

                  <div class="panel" data-sortable-id="ui-general-1">
                     <!-- begin panel-heading -->
                     <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Dependencias o funcionarios autorizados para incluir y editar documentos en el expediente</strong></h4>
                     </div>
                     <!-- end panel-heading -->
                     <!-- begin panel-body -->
                     <div class="panel-body">
                        <!--  Permiso Crear Documentos Todas Field -->
                        <div class="form-group row m-b-15">
                           <div class="col-form-label col-md-3"><label class="required">¿Todas las dependencias pueden incluir y editar documentos en el expediente?:</label></div>
                              <div class="col-md-9">
                                 <select class="form-control" name="permiso_usar_expedientes_todas"
                                    id="permiso_usar_expedientes_todas" v-model="dataForm.permiso_usar_expedientes_todas" required>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                 </select>
                                 <small>Seleccione si todas las dependencias pueden incluir y editar documentos en el expediente</small>
                                 <div class="invalid-feedback" v-if="dataErrors.permiso_usar_expedientes_todas">
                                    <p class="m-b-0" v-for="error in dataErrors.permiso_usar_expedientes_todas">
                                          @{{ error }}</p>
                                 </div>
                              </div>
                        </div>

                        <div class="form-group row m-b-15" v-if="dataForm.permiso_usar_expedientes_todas == 0">
                              <h6 class="col-form-label">Defina las dependencias o usuarios con permiso de incluir y editar documentos en el expediente</h6>
                              <dynamic-list label-button-add="Agregar dependencia/usuario"
                                 :data-list.sync="dataForm.ee_permiso_usar_expedientes" :is-remove="true"
                                 niveles-dataform="3"
                                 :data-list-options="[
                                    { label: 'Dependencia/Funcionario', name: 'nombre', nameObjectKey: ['recipient_datos', 'nombre'], isShow: true, refList: 'dependencia_ref' }
                                 ]"
                                 class-container="col-md-12" class-table="table table-bordered">
                                 <template #fields="scope">
                                    <div class="form-group row m-b-15">
                                       <div class="col-form-label col-md-3"><label class="required">Nombre del funcionario o dependencia:</label></div>
                                          <div class="col-md-9">
                                             <autocomplete
                                                :value-default="scope.dataForm.id"
                                                name-prop="nombre"
                                                name-field="dependencia_usuario_id"
                                                :value='scope.dataForm'
                                                name-resource="/expedientes-electronicos/get-usuarios-autorizados"
                                                css-class="form-control"
                                                :name-labels-display="['tipo','nombre']"
                                                :fields-change-values="['nombre:nombre','tipo:tipo']"
                                                reduce-key="id"
                                                :is-required="true"
                                                name-field-object="recipient_datos"
                                                ref="dependencia_ref"
                                                name-field-edit="nombre"
                                                :ids-to-empty="['dependencia_informacion']"
                                                >
                                             </autocomplete>
                                             <small>Ingrese y seleccione el nombre de la dependencia o usuario para añadirlo.</small>
                                             <div class="invalid-feedback" v-if="dataErrors.name">
                                                <p class="m-b-0" v-for="error in dataErrors.name">@{{ error }}</p>
                                             </div>
                                          </div>
                                    </div>
                                 </template>
                              </dynamic-list>
                        </div>
                     </div>
                  </div>

                  <div class="panel" data-sortable-id="ui-general-1">
                     <!-- begin panel-heading -->
                     <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Dependencias o funcionarios autorizados para ver información y documentos del expediente</strong></h4>
                     </div>
                     <!-- end panel-heading -->
                     <!-- begin panel-body -->
                     <div class="panel-body">
                        <!--  Permiso Crear Documentos Todas Field -->
                        <div class="form-group row m-b-15">
                           <div class="col-form-label col-md-3"><label class="required">¿Todas las dependencias están autorizadas para ver información y documentos del expediente?:</label></div>
                              <div class="col-md-9">
                                 <select class="form-control" name="permiso_consultar_expedientes_todas"
                                    id="permiso_consultar_expedientes_todas" v-model="dataForm.permiso_consultar_expedientes_todas" required>
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                 </select>
                                 <small>Seleccione si las dependencias están autorizadas para ver información y documentos del expediente</small>
                                 <div class="invalid-feedback" v-if="dataErrors.permiso_consultar_expedientes_todas">
                                    <p class="m-b-0" v-for="error in dataErrors.permiso_consultar_expedientes_todas">
                                          @{{ error }}</p>
                                 </div>
                              </div>
                        </div>

                        <div class="form-group row m-b-15" v-if="dataForm.permiso_consultar_expedientes_todas == 0">
                              <h6 class="col-form-label">Defina las dependencias o usuarios con permiso de ver información y documentos del expediente</h6>
                              <dynamic-list label-button-add="Agregar dependencia/usuario"
                                 :data-list.sync="dataForm.ee_permiso_consultar_expedientes" :is-remove="true"
                                 niveles-dataform="3"
                                 :data-list-options="[
                                    { label: 'Dependencia/Funcionario', name: 'nombre', nameObjectKey: ['recipient_datos', 'nombre'], isShow: true, refList: 'dependencia_ref' }
                                 ]"
                                 class-container="col-md-12" class-table="table table-bordered">
                                 <template #fields="scope">
                                    <div class="form-group row m-b-15">
                                       <div class="col-form-label col-md-3"><label class="required">Nombre del funcionario o dependencia:</label></div>
                                          <div class="col-md-9">
                                             <autocomplete
                                                :value-default="scope.dataForm.id"
                                                name-prop="nombre"
                                                name-field="dependencia_usuario_id"
                                                :value='scope.dataForm'
                                                name-resource="/expedientes-electronicos/get-usuarios-autorizados"
                                                css-class="form-control"
                                                :name-labels-display="['tipo','nombre']"
                                                :fields-change-values="['nombre:nombre','tipo:tipo']"
                                                reduce-key="id"
                                                :is-required="true"
                                                name-field-object="recipient_datos"
                                                ref="dependencia_ref"
                                                name-field-edit="nombre"
                                                :ids-to-empty="['dependencia_informacion']"
                                                >
                                             </autocomplete>
                                             <small>Ingrese y seleccione el nombre de la dependencia o usuario para añadirlo.</small>
                                             <div class="invalid-feedback" v-if="dataErrors.name">
                                                <p class="m-b-0" v-for="error in dataErrors.name">@{{ error }}</p>
                                             </div>
                                          </div>
                                    </div>
                                 </template>
                              </dynamic-list>
                        </div>
                     </div>
                  </div>

                  <div class="panel" data-sortable-id="ui-general-1" v-if="isUpdate">
                     <!-- begin panel-heading -->
                     <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Detalles de la modificación aplicada al formulario</strong></h4>
                     </div>
                     <!-- end panel-heading -->
                     <!-- begin panel-body -->
                     <div class="panel-body">
                        <div class="form-group row m-b-15">
                           <div class="col-form-label col-md-3"><label class="required">Descripción del cambio:</label></div>
                              <div class="col-md-9">
                                 <textarea cols="50" rows="10" class="form-control" v-model="dataForm.detalle_modificacion"></textarea>
                                 <small>Describa la modificación realizada en el formulario.</small>
                                 <div class="invalid-feedback" v-if="dataErrors.detalle_modificacion">
                                    <p class="m-b-0" v-for="error in dataErrors.detalle_modificacion">
                                          @{{ error }}</p>
                                 </div>
                              </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-body column col-md-6" style="background-color:#e0e0e0!important" v-if="dataForm.que_desea_hacer == '1'">
               <viewer-attachement v-if="valorDocumento" :open-default="true" :list="valorDocumento" :key="valorDocumento"></viewer-attachement>
            </div>
            <div class="modal-body column col-md-6" style="background-color:#e0e0e0!important" v-if="dataForm.que_desea_hacer == '2'">
               <viewer-attachement v-if="valorRegistro.document_pdf" :list="valorRegistro.document_pdf" :open-default="true" :key="valorRegistro.document_pdf"></viewer-attachement>
            </div>
         </div>
         <div class="modal-footer">
            <button @click="asociarDocumento" v-if="dataForm.que_desea_hacer == '1'" class="btn btn-primary"><i class="fa fa-save mr-2"></i>Asociar documento</button>
            <button @click="crearExpedienteAsociar" v-if="dataForm.que_desea_hacer == '2'" class="btn btn-primary"><i class="fa fa-save mr-2"></i>Crear expediente y asociar documento</button>
            <button class="btn btn-white" @click="cerrarModal"><i class="fa fa-times mr-2"></i>Cerrar</button>
         </div>
      </b-modal>
   </div>
</template>

   <script lang="ts">
   import { Component, Prop, Vue } from "vue-property-decorator";
   import axios from "axios";
   import { jwtDecode } from 'jwt-decode';

   @Component
   export default class ExpedientesGeneralComponent extends Vue {

      public mostrarModal = false;
      public valorRegistro: any;
      public valorDocumento: any;
      public valorExpediente: any;
      public dataForm: any;
      public consecutivoQuery: string = ''; // Campo para el consecutivo
      public ordenQuery: string = ''; // Campo para la orden
      public resultados: any[] = [];
      public camposFiltros: any = [];
      public crudComponent: Vue;
      public isUpdate: boolean;

      /**
       * Errores del formulario
       */
       public dataErrors: any;

      @Prop({ type: String, default: 'consecutivo' }) public moduloConsecutivo: string;

      @Prop({ type: String }) public campoConsecutivo: string;

      @Prop({ type: String }) public modulo: string;

      /**
       * Constructor de la clase
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
      constructor() {
         super();
         this.valorRegistro = {};
         this.dataForm = {ee_permiso_usar_expedientes : []};
         this.valorDocumento = '';  
         this.valorExpediente = {};
         this.crudComponent = this.$parent as Vue;
         this.dataErrors = {};
      }


      /**
       * Abre el modal al momento de accionar el boton
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
       public abrirModal(row: any): void {
         // Abre y muestra el modal
         this.mostrarModal = true;
         this.valorRegistro = row;
         this.correspondenciaRelacionada(row[this.campoConsecutivo]);

      }

      /**
       * Cierra el modal al momento de darle clic
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
      public correspondenciaRelacionada(campo): void {
         this.showLoadingGif("Cargando información");
         const queryExpeEncript = btoa(campo);
         axios.get(`/expedientes-electronicos/get-correspondencia-relacionada/${queryExpeEncript}`)
         .then((res) => {
            let dataPayload = res.data.data ? jwtDecode(res.data.data) : null;
            const dataDecrypted = Object.assign({}, dataPayload);


            if(dataDecrypted["data"].length > 0){
               this.$swal.close();
               this.$set(this.dataForm, "consecutivo_expediente",dataDecrypted["data"][0]["ee_expediente"]["consecutivo"]);
               const encryptedIdExpediente = btoa(dataDecrypted["data"][0]["ee_expediente_id"]);
               this.$set(this.dataForm, "id_expediente_info",encryptedIdExpediente);
            } else {
               this.$swal.close();
               this.$set(this.dataForm, "consecutivo_expediente",null);
               this.$set(this.dataForm, "id_expediente_info",null);
            }
         })
            .catch((err) => {
            console.error('Error al realizar la consulta de la correspondencia relacionada:', err);
         });
      }

      
      /**
       * Cierra el modal al momento de darle clic
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
      public cerrarModal(): void {
         // Cerrar el modal
         this.mostrarModal = false;
         this.$set(this, 'dataForm', {});
         this.$set(this, 'valorDocumento', '');
         this.$set(this, 'resultados', []);
      }


      /**
       * Obtiene la caratula del expediente
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
      public obtenerDocumento(data){
         this.valorExpediente = data;
         this.valorDocumento = data.ee_documentos_expedientes[0].adjunto;
      }

      /**
       * Asocia el documento al expediente electronico
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
      public async asociarDocumento(): Promise<void> {
         if (typeof this.dataForm[this.moduloConsecutivo] !== 'undefined' && this.dataForm[this.moduloConsecutivo] !== null && this.dataForm[this.moduloConsecutivo].trim() !== '') {
         // Mostrar swal de confirmación
         this.$swal({
               title: "¿Desea asociar el documento?",
               text: "Esta acción asociará el documento al expediente seleccionado.",
               icon: "warning",
               showCancelButton: true,
               confirmButtonText: "Asociar",
               cancelButtonText: "Cancelar",
         }).then(async (result) => {
               if (result.isConfirmed) {
                  try {
                     // Mostrar el indicador de carga
                     this.showLoadingGif("Asociando documento");
                     // Convierte el id del expediente en una cadena
                     const idExpediente = this.valorExpediente.id.toString();
                     // Lo encripta para mandarlo como parametro
                     const idExpeEncript = btoa(idExpediente);
                     // Crear el formulario de datos
                     const formData = new FormData();
                     formData.append(this.moduloConsecutivo, this.dataForm[this.moduloConsecutivo]);
                     formData.append("adjunto", this.valorRegistro.document_pdf);
                     formData.append("modulo_consecutivo", this.valorRegistro[this.campoConsecutivo]);
                     formData.append("modulo_intraweb", this.modulo);
                     formData.append("ee_tipos_documentales_id", this.dataForm.ee_tipos_documentales_id);

                     // Realizar la petición para guardar o actualizar el documento
                     const res = await axios.post(`/expedientes-electronicos/asociar-expediente/${idExpeEncript}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                     if(res.data === 'existe'){
                        this.$swal({
                           title: "¡Documento ya existe!",
                           text: "Este documento ya se encuentra asociado a este expediente.",
                           icon: "info",
                           confirmButtonText: "Entendido",
                        });
                     } else if(res.data.type_message === 'info') { // Si se cumple la condición, hubo un error al guardar la información
                        // Abre el swal informando que no se guardaron los datos
                        this.$swal({
                            icon: res.data.type_message,
                            html: res.data.message,
                            allowOutsideClick: false,
                            confirmButtonText: "Entendido"
                        });
                     } else {
                        // Cerrar el indicador de carga
                        this.$swal.close();
                        // Cierra fomrulario modal
                        this.cerrarModal();
                        // Mostrar notificación de éxito
                        this._pushNotification("Guardado con éxito");
                     }
                  } catch (error) {
                     // Manejar errores de la petición
                     if (error.response && error.response.data && error.response.data.errors) {
                     console.log(error.response.data.errors);
                     }
                  }
               }
            });
         } else {
            this.$swal({
            title: "¡Ingrese consecutivo!",
            text: "Por favor, ingrese un consecutivo que sea valido.",
            icon: "info",
            confirmButtonText: "Entendido",
            });
         }

      }

      /**
       * Busca el expediente dependiendo de los filtros
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
      public async buscarExpediente(): Promise<void> {
         this.showLoadingGif("Buscando expediente");
         let query = '';
         Object.keys(this.camposFiltros).forEach((campo, index) => {
            if (this.camposFiltros[campo]) {
               if (index > 0) {
                  query += ' AND ';
               }
               query += `(${campo} like '%${this.camposFiltros[campo]}%')`;
            }
         });
         // Lo encripta para mandarlo como parametro
         const queryExpeEncript = btoa(query);
         axios.get(`/expedientes-electronicos/get-expediente-filtros/${queryExpeEncript}`)
         .then((res) => {
            let dataPayload = res.data.data ? jwtDecode(res.data.data) : null;
            const dataDecrypted = Object.assign({}, dataPayload);
            if(dataDecrypted["data"]){
               this.resultados = dataDecrypted["data"];
               // Cerrar el indicador de carga
               this.$swal.close();
            } else {
               // Cerrar el indicador de carga
               this.$swal.close();
               this.$swal({
                  title: "No hay coincidencias para los filtros seleccionados!",
                  text: "Por favor, Ingrese filtros validos.",
                  icon: "info",
                  confirmButtonText: "Entendido",
               });
            }
         })
            .catch((err) => {
            console.error('Error al realizar la consulta:', err);
         });

      }

      /**
       * Funcion del gif que se muestra de cargando
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
      private showLoadingGif(message: string): void {
         // Mostrar un indicador de carga
         this.$swal({
            html: '<img src="/assets/img/loadingintraweb.gif" alt="Cargando..." style="width: 100px;"><br><span>' + message + '.</span>',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
         });
      }

      /**
       *
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
      private _pushNotification(message: string, isPositive: boolean = true, title: string = '¡Éxito!'): void {
         // Mostrar una notificación
         const toastOptions = { closeButton: true, closeMethod: 'fadeOut', timeOut: 3000, tapToDismiss: false };
         if (isPositive) {
               toastr.success(message, title, toastOptions);
         } else {
               toastOptions.timeOut = 0;
               toastr.error(message, title, toastOptions);
         }
      }

      /**
       * Agrega datos a donde se necesita
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
      public seleccionarExpediente(adjunto: any, consecExpediente: string): void {
         this.$set(this, 'valorDocumento', adjunto);
         this.$set(this.dataForm, 'consecutivo', consecExpediente);
      }

      /**
       * Agrega datos a donde se necesita
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
      public limpiarFiltros(): void {
         this.$set(this, 'valorDocumento', '');
         this.$set(this, 'resultados', []);
         this.$set(this, 'camposFiltros', []);
      }

      /**
      * Inicializa campos por defecto del formulario de tipo de documento
      */
      public inicializarValoresTipoExpediente() {
         /*
         * Se inicializan los campos formato_consecutivo_value para ayudar al usuario a
         * escoger un formato por defecto para el consecutivo del documento
         */
         this.$set(this.crudComponent["dataForm"], "formato_consecutivo_value", ["vigencia_actual", "prefijo_documento", "consecutivo_documento"]);
      }

      // Método para encriptar el ID y generar la URL
      getEncryptedResourceUrl(): string {
         const encryptedId = btoa(this.dataForm.id.toString());
         return `/expedientes-electronicos/get-tipos-documentales/${encryptedId}`;
      }

      /**
       * Crea el expediente electronico y asocia el documento al expedinete
       *
       * @author Manuel Marin.  2024
       * @version 1.0.0
      */
      public async crearExpedienteAsociar(): Promise<void> {
         // Mostrar swal de confirmación
         this.$swal({
               title: "¿Desea crear y asociar el documento?",
               text: "Esta acción creara y asociara asociará el documento al expediente seleccionado.",
               icon: "warning",
               showCancelButton: true,
               confirmButtonText: "Crear y asociar",
               cancelButtonText: "Cancelar",
         }).then(async (result) => {
            if (result.isConfirmed) {
               try {
                  // Mostrar el indicador de carga
                  this.showLoadingGif("Creando y asociando documento");
                  this.$set(this.dataForm, "adjunto",this.valorRegistro.document_pdf);
                  this.$set(this.dataForm, "modulo_intraweb",this.modulo);
                  this.$set(this.dataForm, "modulo_consecutivo",this.valorRegistro[this.campoConsecutivo]);
                  // Realizar la petición para guardar o actualizar el documento
                  const res = await axios.post(`/expedientes-electronicos/asociar-expediente-asociar`, this.dataForm, { headers: { 'Content-Type': 'multipart/form-data' } });
                  if(res.data === 'existe'){
                     this.$swal({
                        title: "¡Documento ya existe!",
                        text: "Este documento ya se encuentra asociado a este expediente.",
                        icon: "info",
                        confirmButtonText: "Entendido",
                     });
                  } else if(res.data.type_message === 'info') { // Si se cumple la condición, hubo un error al guardar la información
                     // Abre el swal informando que no se guardaron los datos
                     this.$swal({
                           icon: res.data.type_message,
                           html: res.data.message,
                           allowOutsideClick: false,
                           confirmButtonText: "Entendido"
                     });
                  } else {
                     // Cerrar el indicador de carga
                     this.$swal.close();
                     // Cierra fomrulario modal
                     this.cerrarModal();
                     // Mostrar notificación de éxito
                     this._pushNotification("Guardado con éxito");
                  }
               } catch (error) {
                  // Manejar errores de la petición
                  if (error.response && error.response.data && error.response.data.errors) {
                  console.log(error.response.data.errors);
                  }
               }
            }
         });

      }

      created() {
         // Obtiene la instancia del crudComponent
         let crudComponent = (this.$parent as Vue);

            // recuperamos el querystring (parámetros enviados por URL)
            const querystring = window.location.search
            // usando el querystring, creamos un objeto del tipo URLSearchParams
            const params = new URLSearchParams(querystring)
            if(params.get('qderExpediente')) {
               axios.get('/expedientes-electronicos/get-informacion-expediente/'+params.get('qderExpediente'))
                  .then((res) => {
                     let dataPayload = res.data.data ? jwtDecode(res.data.data) : null;

                     const dataDecrypted = Object.assign({}, dataPayload);
                        // Envia elemento a mostrar a la función show (Ver Detalles)
                        crudComponent["show"](dataDecrypted["data"]);
                        $(`#modal-view-${crudComponent["name"]}`).modal('toggle');
                  })
                  .catch((err) => {
                     console.log("Error obteniendo información del registro desde el listado el dashboard")
                  });

            }
      }

   }
</script>
<style>
  .info-box {
    background-color: #e6fffa; /* Verde claro para indicar éxito */
    border: 1px solid #21ba46; /* Verde oscuro para el borde */
    padding: 10px;
    border-radius: 5px;
    text-align: center;
  }
</style>