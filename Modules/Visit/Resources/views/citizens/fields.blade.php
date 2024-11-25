<div  class="panel" data-sortable-id="ui-general-1">
    <div class="panel-body">
        <div class="row">
                <!-- Nombres Field -->
                <div class="form-group row m-b-15 col-sm-6">
                    {!! Form::label('nombres', 'Nombres:', ['class' => 'col-form-label col-md-3 required']) !!}
                    <div class="col-md-9">
                        {!! Form::text('nombres', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.nombres }", 'v-model' => 'dataForm.nombres', 'required' => true]) !!}
                        <small>@lang('Enter the') @{{ `@lang('Nombres')` | lowercase }}</small>
                        <div class="invalid-feedback" v-if="dataErrors.nombres">
                            <p class="m-b-0" v-for="error in dataErrors.nombres">@{{ error }}</p>
                        </div>
                    </div>
                </div>

                <!-- Apellidos Field -->
                <div class="form-group row m-b-15 col-sm-6">
                    {!! Form::label('apellidos', 'Apellidos:', ['class' => 'col-form-label col-md-3 required']) !!}
                    <div class="col-md-9">
                        {!! Form::text('apellidos', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.apellidos }", 'v-model' => 'dataForm.apellidos', 'required' => true]) !!}
                        <small>@lang('Enter the') @{{ `@lang('Apellidos')` | lowercase }}</small>
                        <div class="invalid-feedback" v-if="dataErrors.apellidos">
                            <p class="m-b-0" v-for="error in dataErrors.apellidos">@{{ error }}</p>
                        </div>
                    </div>
                </div>
        </div>

        <div class="row">
            <!-- Tipo Documento Field -->
            <div class="form-group row m-b-15 col-sm-6">
                {!! Form::label('tipo_documento', 'Tipo Documento:', ['class' => 'col-form-label col-md-3 required' ]) !!}
                <div class="col-md-9">
                    <select class="form-control" name="tipo_documento"  v-model="dataForm.tipo_documento"  required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Cédula de ciudadanía">Cédula de ciudadanía</option>
                        <option value="Cédula extranjera">Cédula extranjera</option>
                        <option value="Tarjeta de identidad">Tarjeta de identidad</option>
                        <option value="Pasaporte">Pasaporte</option>
                        <option value="Otro">Otro</option>
                    </select>
                    <small>@lang('Enter the') @{{ `@lang('Tipo Documento')` | lowercase }}</small>
                    <div class="invalid-feedback" v-if="dataErrors.tipo_documento">
                        <p class="m-b-0" v-for="error in dataErrors.tipo_documento">@{{ error }}</p>
                    </div>
                </div>
            </div>


            <!-- Numero Documento Field -->
            <div class="form-group row m-b-15 col-sm-6">
                {!! Form::label('numero_documento', 'Número de documento:', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    {!! Form::text('numero_documento', null, ['class' => 'form-control required', 'maxlength' => 30, "onkeypress"=>"return event.charCode >= 48 && event.charCode <= 57", 'title' => 'Solo se permiten números', 'v-model' => 'dataForm.numero_documento', 'required' => true]) !!}
                    <small>@lang('Enter the') @{{ `@lang('Numero Documento')` | lowercase }}</small>
                    <div class="invalid-feedback" v-if="dataErrors.numero_documento">
                        <p class="m-b-0" v-for="error in dataErrors.numero_documento">@{{ error }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Genero Field -->
            <div class="form-group row m-b-15 col-sm-6">
                {!! Form::label('genero', 'Género:', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    <select class="form-control" name="genero" v-model="dataForm.genero" required>
                        <option value="" disabled selected>Seleccione un género</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Intersexual">Intersexual</option>
                    </select>
                    <small>@lang('Enter the') @{{ `@lang('Genero')` | lowercase }}</small>
                    <div class="invalid-feedback" v-if="dataErrors.genero">
                        <p class="m-b-0" v-for="error in dataErrors.genero">@{{ error }}</p>
                    </div>
                </div>
            </div>

            <!-- Ciclo Vital Field -->
            <div class="form-group row m-b-15 col-sm-6">
                {!! Form::label('ciclo_vital', 'Ciclo Vital:', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    <select class="form-control" name="ciclo_vital" v-model="dataForm.ciclo_vital" required>
                        <option value="" disabled selected>Seleccione un ciclo vital</option>
                        <option value="0-5 años primera infancia">0-5 años primera infancia</option>
                        <option value="6-11 años niños niñas">6-11 años niños niñas</option>
                        <option value="12-17 adolescentes">12-17 adolescentes</option>
                        <option value="18-28 jóvenes">18-28 jóvenes</option>
                        <option value="29-59 adultos">29-59 adultos</option>
                        <option value="mayor de 60 adulto mayor">mayor de 60 adulto mayor</option>
                    </select>
                    <small>@lang('Enter the') @{{ `@lang('Ciclo Vital')` | lowercase }}</small>
                    <div class="invalid-feedback" v-if="dataErrors.ciclo_vital">
                        <p class="m-b-0" v-for="error in dataErrors.ciclo_vital">@{{ error }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Numero Celular Field -->
            <div class="form-group row m-b-15 col-sm-6">
                {!! Form::label('tipo_victima', trans('Tipo Victima').':', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    <select class="form-control" name="tipo_victima"  v-model="dataForm.tipo_victima" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Directa">Directa</option>
                        <option value="Indirecta">Indirecta</option>
                    </select>
                    <small>@lang('Enter the') @{{ `@lang('Tipo Victima')` | lowercase }}</small>
                    <div class="invalid-feedback" v-if="dataErrors.tipo_victima">
                        <p class="m-b-0" v-for="error in dataErrors.tipo_victima">@{{ error }}</p>
                    </div>
                </div>
            </div>

            <!-- Numero Celular Field -->
            <div class="form-group row m-b-15 col-sm-6">
                {!! Form::label('numero_celular', 'Número celular:', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    {!! Form::text('numero_celular', null, ['class' => 'form-control required', 'v-model' => 'dataForm.numero_celular',"onkeypress"=>"return event.charCode >= 48 && event.charCode <= 57" ,'maxlength' => 12, 'maxlength' => 12, 'required' => true ]) !!}
                    <small>@lang('Enter the') @{{ `@lang('Numero Celular')` | lowercase }}</small>
                    <div class="invalid-feedback" v-if="dataErrors.numero_celular">
                        <p class="m-b-0" v-for="error in dataErrors.numero_celular">@{{ error }}</p>
                    </div>
                </div>
            </div>

            
        </div>

        <div class="row">

           <!-- Etnia Field -->
    <div class="form-group row m-b-15 col-md-6">
        {!! Form::label('etnia', trans('Etnia').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            <select-check
                css-class="form-control"
                name-field="etnia"
                reduce-label="name"
                reduce-key="id"
                name-resource="get-constants/grupo_poblacional"
                :value="dataForm"
                :key="dataForm.states_id"
                :is-required="false"
                :enable-search="true">
            </select-check>
            <small>@lang('Enter the') @{{ `@lang('Etnia')` | lowercase }}</small>
            <div class="invalid-feedback" v-if="dataErrors.etnia">
                <p class="m-b-0" v-for="error in dataErrors.etnia">@{{ error }}</p>
            </div>
        </div>
    </div>
            

            <!-- Tipo Victima Field -->
            <div class="form-group row m-b-15 col-sm-6">
                {!! Form::label('otras_victimas', trans('Otras Victima').':', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    <select class="form-control" name="otras_victimas"  v-model="dataForm.otras_victimas" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Si">Sí</option>
                        <option value="No">No</option>
                    </select>
                    <small>@lang('Enter the') @{{ `@lang('Tipo Victima')` | lowercase }}</small>
                    <div class="invalid-feedback" v-if="dataErrors.otras_victimas">
                        <p class="m-b-0" v-for="error in dataErrors.otras_victimas">@{{ error }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div v-if="dataForm.otras_victimas == 'Si'"  class="panel" data-sortable-id="ui-general-1">
    <div class="panel-body">
        <dynamic-list label-button-add="Agregar destinatario" icon-button-add="fas fa-user-plus" :data-list.sync="dataForm.otras_victimas_list"
                    :data-list-options="[
                        { label: 'Nombre', name: 'nombres', isShow: true },
                        { label: 'Número de documento', name: 'numero_documento', isShow: true },
                        { label: 'Parentezco', name: 'parentezco', isShow: true },
                    ]"
                    class-container="col-md-12" class-table="table table-bordered">
            <template #fields="scope">

                <div class="row">
                    <!-- Nombres Field -->
                    <div class="form-group row m-b-15 col-sm-6">
                        {!! Form::label('nombres', 'Nombres:', ['class' => 'col-form-label col-md-3 required']) !!}
                        <div class="col-md-9">
                            {!! Form::text('nombres', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.nombres }", 'v-model' => 'scope.dataForm.nombres', 'required' => true]) !!}
                            <small>@lang('Enter the') @{{ `@lang('Nombres')` | lowercase }}</small>
                            <div class="invalid-feedback" v-if="dataErrors.nombres">
                                <p class="m-b-0" v-for="error in dataErrors.nombres">@{{ error }}</p>
                            </div>
                        </div>
                    </div>
    
                    <!-- Apellidos Field -->
                    <div class="form-group row m-b-15 col-sm-6">
                        {!! Form::label('apellidos', 'Apellidos:', ['class' => 'col-form-label col-md-3 required']) !!}
                        <div class="col-md-9">
                            {!! Form::text('apellidos', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.apellidos }", 'v-model' => 'scope.dataForm.apellidos', 'required' => true]) !!}
                            <small>@lang('Enter the') @{{ `@lang('Apellidos')` | lowercase }}</small>
                            <div class="invalid-feedback" v-if="dataErrors.apellidos">
                                <p class="m-b-0" v-for="error in dataErrors.apellidos">@{{ error }}</p>
                            </div>
                        </div>
                    </div>
            </div>
    
            <div class="row">
                <!-- Tipo Documento Field -->
                <div class="form-group row m-b-15 col-sm-6">
                    {!! Form::label('tipo_documento', 'Tipo Documento:', ['class' => 'col-form-label col-md-3 required' ]) !!}
                    <div class="col-md-9">
                        <select class="form-control" name="tipo_documento"  v-model="scope.dataForm.tipo_documento"  required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="Cédula de ciudadanía">Cédula de ciudadanía</option>
                            <option value="Cédula extranjera">Cédula extranjera</option>
                            <option value="Tarjeta de identidad">Tarjeta de identidad</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <small>@lang('Enter the') @{{ `@lang('Tipo Documento')` | lowercase }}</small>
                        <div class="invalid-feedback" v-if="dataErrors.tipo_documento">
                            <p class="m-b-0" v-for="error in dataErrors.tipo_documento">@{{ error }}</p>
                        </div>
                    </div>
                </div>
    
    
                <!-- Numero Documento Field -->
                <div class="form-group row m-b-15 col-sm-6">
                    {!! Form::label('numero_documento', 'Número de documento:', ['class' => 'col-form-label col-md-3 required']) !!}
                    <div class="col-md-9">
                        {!! Form::text('numero_documento', null, ['class' => 'form-control required', 'maxlength' => 30, "onkeypress"=>"return event.charCode >= 48 && event.charCode <= 57", 'title' => 'Solo se permiten números', 'v-model' => 'scope.dataForm.numero_documento', 'required' => true]) !!}
                        <small>@lang('Enter the') @{{ `@lang('Numero Documento')` | lowercase }}</small>
                        <div class="invalid-feedback" v-if="dataErrors.numero_documento">
                            <p class="m-b-0" v-for="error in dataErrors.numero_documento">@{{ error }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Genero Field -->
                <div class="form-group row m-b-15 col-sm-6">
                    {!! Form::label('parentezco', 'Parentezco:', ['class' => 'col-form-label col-md-3 required']) !!}
                    <div class="col-md-9">
                        {!! Form::text('parentezco', null, ['class' => 'form-control required',  'title' => 'Solo se permiten números', 'v-model' => 'scope.dataForm.parentezco', 'required' => true]) !!}
                        <small>@lang('Enter the') @{{ `@lang('parentezco')` | lowercase }}</small>
                        <div class="invalid-feedback" v-if="dataErrors.parentezco">
                            <p class="m-b-0" v-for="error in dataErrors.parentezco">@{{ error }}</p>
                        </div>
                    </div>
                </div>
    
                <!-- Ciclo Vital Field -->
                <div class="form-group row m-b-15 col-sm-6">
                    {!! Form::label('tipo_victima', 'Tipo de victima:', ['class' => 'col-form-label col-md-3 required']) !!}
                    <div class="col-md-9">
                        <select class="form-control" name="tipo_victima" v-model="scope.dataForm.tipo_victima" required>
                            <option value="" disabled selected>Seleccione un ciclo vital</option>
                            <option value="Drecta">Drecta</option>
                            <option value="Indirecta">Indirecta</option>
                        </select>
                        <small>@lang('Enter the') @{{ `@lang('Ciclo Vital')` | lowercase }}</small>
                        <div class="invalid-feedback" v-if="dataErrors.tipo_victima">
                            <p class="m-b-0" v-for="error in dataErrors.tipo_victima">@{{ error }}</p>
                        </div>
                    </div>
                </div>
            </div>
                
               
            </template>
        </dynamic-list>

    </div>
</div>


<div  class="panel" data-sortable-id="ui-general-1">
    <div class="panel-body">
        <div class="row">
            <!-- Tipo Documento Field -->
            <div class="form-group row m-b-15 col-sm-12">
                {!! Form::label('tipo_declaracion', 'Tipo declaración:', ['class' => 'col-form-label col-md-3 required' ]) !!}
                <div class="col-md-9">
                    <select class="form-control" name="tipo_declaracion"  v-model="dataForm.tipo_declaracion"  required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Homicidio">Homicidio</option>
                        <option value="Violencia sexual">Violencia Sexual</option>
                        <option value="Desplazamiento forzado">Desplazamiento Forzado</option>
                        <option value="Reclutamiento forzado">Reclutamiento Forzado</option>
                        <option value="Desaparición forzada">Desaparición Forzada</option>
                        <option value="Amenazas">Amenazas</option>
                        <option value="Utilización">Utilización</option>
                        <option value="Otro">Otro</option>
                    </select>
                    <small>@lang('Enter the') @{{ `@lang('Tipo declaracion')` | lowercase }}</small>
                    <div class="invalid-feedback" v-if="dataErrors.tipo_declaracion">
                        <p class="m-b-0" v-for="error in dataErrors.tipo_declaracion">@{{ error }}</p>
                    </div>
                </div>
            </div>

            <!-- Numero Documento Field -->
            <div class="form-group row m-b-15 col-sm-12">
                {!! Form::label('hechos', 'Hechos:', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    {!! Form::textarea('hechos', null, ['class' => 'form-control required', 'title' => 'Solo se permiten números', 'v-model' => 'dataForm.hechos', 'required' => true]) !!}
                    <small>Ingrese la descripción de los hechos</small>
                    <div class="invalid-feedback" v-if="dataErrors.hechos">
                        <p class="m-b-0" v-for="error in dataErrors.hechos">@{{ error }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>      


<div  class="panel" data-sortable-id="ui-general-1">
    <div class="panel-body">
        <div class="row">
            <!-- Numero Documento Field -->
            <div class="form-group row m-b-15 col-sm-12">
                {!! Form::label('secuelas_generadas', 'Impactos y/o secuelas generadas por los hechos:', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    {!! Form::textarea('secuelas_generadas', null, ['class' => 'form-control required', 'title' => 'Solo se permiten números', 'v-model' => 'dataForm.secuelas_generadas', 'required' => true]) !!}
                    <small>Ingrese la descripción de los impactos y/o secuelas generadas por los hechoss</small>
                    <div class="invalid-feedback" v-if="dataErrors.secuelas_generadas">
                        <p class="m-b-0" v-for="error in dataErrors.secuelas_generadas">@{{ error }}</p>
                    </div>
                </div>
            </div>


            <!-- Numero Documento Field -->
            <div class="form-group row m-b-15 col-sm-12">
            {!! Form::label('patrimonios_afectados', 'Patrimonios que se vieron afectados:', ['class' => 'col-form-label col-md-3 required']) !!}
            <div class="col-md-9">
                {!! Form::textarea('patrimonios_afectados', null, ['class' => 'form-control required', 'title' => 'Solo se permiten números', 'v-model' => 'dataForm.patrimonios_afectados', 'required' => true]) !!}
                <small>Ingrese la descripción de los patrimonios_afectados</small>
                <div class="invalid-feedback" v-if="dataErrors.patrimonios_afectados">
                    <p class="m-b-0" v-for="error in dataErrors.patrimonios_afectados">@{{ error }}</p>
                </div>
            </div>
            </div>
        </div>

        <div class="row">
            <!-- Numero Documento Field -->
            <div class="form-group row m-b-15 col-sm-6">
                {!! Form::label('lesiones_fisicass', 'Lesiones fisicas:', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    {!! Form::text('lesiones_fisicass', null, ['class' => 'form-control required', 'title' => 'Solo se permiten números', 'v-model' => 'dataForm.lesiones_fisicass', 'required' => true ]) !!}
                    <small>Ingrese la descripción de las lesiones ficicas</small>
                    <div class="invalid-feedback" v-if="dataErrors.lesiones_fisicass">
                        <p class="m-b-0" v-for="error in dataErrors.lesiones_fisicass">@{{ error }}</p>
                    </div>
                </div>
            </div>


            <!-- Numero Documento Field -->
            <div class="form-group row m-b-15 col-sm-6">
                {!! Form::label('lesiones_psicologicas', 'Lesiones psicologicas:', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    {!! Form::text('lesiones_psicologicas', null, ['class' => 'form-control required', 'title' => 'Solo se permiten números', 'v-model' => 'dataForm.lesiones_psicologicas', 'required' => true ]) !!}
                    <small>Ingrese la descripción de los lesiones psicologicas</small>
                    <div class="invalid-feedback" v-if="dataErrors.lesiones_psicologicas">
                        <p class="m-b-0" v-for="error in dataErrors.lesiones_psicologicas">@{{ error }}</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <!-- Numero Documento Field -->
            <div class="form-group row m-b-15 col-sm-12">
                {!! Form::label('descripcion', 'Descripción:', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    {!! Form::textarea('descripcion', null, ['class' => 'form-control required', 'title' => 'Solo se permiten números', 'v-model' => 'dataForm.descripcion', 'required' => true ]) !!}
                    <small>Ingrese la descripción de las lesiones ficicas</small>
                    <div class="invalid-feedback" v-if="dataErrors.descripcion">
                        <p class="m-b-0" v-for="error in dataErrors.descripcion">@{{ error }}</p>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
</div>  


<div  class="panel" data-sortable-id="ui-general-1">
    <div class="panel-body">
        <div class="row">
        

            <!-- Numero Documento Field -->
            <div class="form-group row m-b-15 col-sm-12">
                {!! Form::label('tiempo_acto', 'Tiempo en el que actuo:', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    {!! Form::textarea('tiempo_acto', null, ['class' => 'form-control required',  'v-model' => 'dataForm.tiempo_acto', 'required' => true ]) !!}
                    <small>Ingrese el tiempo en el que actuo</small>
                    <div class="invalid-feedback" v-if="dataErrors.tiempo_acto">
                        <p class="m-b-0" v-for="error in dataErrors.tiempo_acto">@{{ error }}</p>
                    </div>
                </div>
            </div>

            <!-- Numero Documento Field -->
            <div class="form-group row m-b-15 col-sm-12">
                {!! Form::label('descripcio_hecho_principal', 'Descripción de los hechos principales:', ['class' => 'col-form-label col-md-3 required']) !!}
                <div class="col-md-9">
                    {!! Form::textarea('descripcio_hecho_principal', null, ['class' => 'form-control required',  'v-model' => 'dataForm.descripcio_hecho_principal', 'required' => true ]) !!}
                    <small>Ingrese el tiempo en el que actuo</small>
                    <div class="invalid-feedback" v-if="dataErrors.descripcio_hecho_principal">
                        <p class="m-b-0" v-for="error in dataErrors.descripcio_hecho_principal">@{{ error }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>      


