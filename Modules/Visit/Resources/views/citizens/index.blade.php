@extends('layouts.default')

@section('title', 'Formulario')

@section('section_img', '')

@section('menu')
    @include('visit::layouts.menu')
@endsection

@section('content')

<crud
    name="citizens"
    :resource="{default: 'citizens', get: 'get-citizens'}"
    :init-values="{otras_victimas_list:[] , email : '{{ $email }}' }"
    inline-template>
    <div>
        <!-- begin breadcrumb -->
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{!! url('/dashboard') !!}">@lang('home')</a></li>
            <li class="breadcrumb-item active">Formulario</li>
        </ol>
        <!-- end breadcrumb -->

        <!-- begin page-header -->
        <h1 class="page-header text-center m-b-35">Vista para administrar formulario de visita</h1>
        <!-- end page-header -->

        <!-- begin main buttons -->
        <div class="m-t-20">
            <button @click="add()" type="button" class="btn btn-primary m-b-10" data-backdrop="static" data-target="#modal-form-citizens" data-toggle="modal">
                <i class="fa fa-plus mr-2"></i>Crear formulario
            </button>

            <button  type="button" class="btn btn-primary m-b-10" 
                data-target="#configurate_mail"  data-toggle="modal">
                <i class="fas fa-cogs"></i>  Configurar correo del defensor
            </button>
        </div>
        <!-- end main buttons -->

        <!-- begin panel -->
        <div class="panel panel-default">
            <div class="panel-heading border-bottom">
                <div class="panel-title">
                    <h5 class="text-center"> @{{ `@lang('total_registers') formularios: ${dataPaginator.total}` | capitalize }}</h5>
                </div>
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                </div>
            </div>
            <!-- begin #accordion search-->
            <div id="accordion" class="accordion">
                <!-- begin card search -->
                <div @click="toggleAdvanceSearch()" class="cursor-pointer card-header bg-white pointer-cursor d-flex align-items-center" data-toggle="collapse" data-target="#collapseOne">
                    <i class="fa fa-search fa-fw mr-2 f-s-12"></i> <b>@{{ (showSearchOptions)? 'trans.hide_search_options' : 'trans.show_search_options' | trans }}</b>
                </div>
                <div id="collapseOne" class="collapse border-bottom p-l-40 p-r-40" data-parent="#accordion">
                    <div class="card-body">
                        <label class="col-form-label"><b>@lang('quick_search')</b></label>
                        <!-- Campos de busqueda -->
                        <div class="row form-group">
                            <div class="col-md-4">
                                <date-picker
                                    :value="searchFields"
                                    name-field="created_at"
                                    mode="range">
                                </date-picker>
                                <small>Filtrar por la fecha de visita en un rango de fechas</small>
                            </div>

                            <div class="col-md-4">
                                <select class="form-control" name="	Rango edad" v-model="searchFields.ciclo_vital">
                                    <option value="" disabled selected>Seleccione un ciclo vital</option>
                                    <option value="0-5 años primera infancia">0-5 años primera infancia</option>
                                    <option value="6-11 años niños niñas">6-11 años niños niñas</option>
                                    <option value="12-17 adolescentes">12-17 adolescentes</option>
                                    <option value="18-28 jóvenes">18-28 jóvenes</option>
                                    <option value="29-59 adultos">29-59 adultos</option>
                                    <option value="mayor de 60 adulto mayor">mayor de 60 adulto mayor</option>
                                </select>
                                <small>Filtrar por ciclo vital</small> 
                            </div> 

                            <div class="col-md-4">
                                {!! Form::text('nombre_ciudadano', null, [
                                    'v-model' => 'searchFields.citizen_name',
                                    'class' => 'form-control',
                                    'placeholder' => trans('crud.filter_by', ['name' => 'Nombre ciudadano']),
                                ]) !!}
                            </div> 
                         
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4">
                                {!! Form::text('numero_documento', null, [
                                    'v-model' => 'searchFields.numero_documento',
                                    'class' => 'form-control',
                                    'placeholder' => trans('crud.filter_by', ['name' => 'numero documento']),
                                ]) !!}
                            </div> 

                            <div class="col-md-4">
                                <button @click="clearDataSearch()" class="btn btn-md btn-primary">@lang('clear_search_fields')</button>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- end card search -->
            </div>
            <!-- end #accordion search -->
            <div class="panel-body">
                <!-- begin buttons action table -->
                <div class="float-xl-right m-b-15">
                    <!--Solo al que tiene el rol de secretaaria de salud puede exportar la tabla -->
                    <!-- Acciones para exportar datos de tabla-->
                    <div class="btn-group">
                        <a href="javascript:;" data-toggle="dropdown" class="btn btn-primary"><i class="fa fa-download mr-2"></i> @lang('export_data_table')</a>
                        <a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle" aria-expanded="false"><b class="caret"></b></a>
                        <div class="dropdown-menu dropdown-menu-right bg-blue">
                            <a href="javascript:;" @click="exportDataTableAvanzado('xlsx')" class="dropdown-item text-white no-hover"><i class="fa fa-file-excel mr-2"></i> EXCEL</a>
                            <a  href="javascript:;" @click="exportDataTableIndicators('xlsx')" class="dropdown-item text-white no-hover"><i class="fa fa-file-excel mr-2"></i>Generar excel estadistico</a>

                        </div>
                    </div>
                </div>
                <!-- end buttons action table -->
                @include('visit::citizens.table')
            </div>
            <div class="p-b-15 text-center">
                <!-- Cantidad de elementos a mostrar -->
                <div class="form-group row m-5 col-md-3 text-center d-sm-inline-flex">
                    <label for="show_qty" class="col-form-label col-md-7">Cantidad a mostrar:</label>
                    <div class="col-md-5">
                        <select class="form-control" v-model="dataPaginator.pagesItems" name="Cantidad a mostrar"><option value="5" selected="selected">5</option><option value="10">10</option><option value="15">15</option><option value="20">20</option><option value="25">25</option><option value="30">30</option><option value="50">50</option><option value="75">75</option></select>
                    </div>
                </div>
                <!-- Paginador de tabla -->
                <div class="col-md-12">
                    <paginate
                        v-model="dataPaginator.currentPage"
                        :page-count="dataPaginator.numPages"
                        :click-handler="pageEvent"
                        :prev-text="'Anterior'"
                        :next-text="'Siguiente'"
                        :container-class="'pagination m-10'"
                        :page-class="'page-item'"
                        :page-link-class="'page-link'"
                        :prev-class="'page-item'"
                        :next-class="'page-item'"
                        :prev-link-class="'page-link'"
                        :next-link-class="'page-link'"
                        :disabled-class="'ignore disabled'">
                    </paginate>
                </div>
            </div>
        </div>
        <!-- end panel -->

        <!-- begin #modal-view-citizens -->
        <div class="modal fade" id="modal-view-citizens">
            <div class="modal-dialog modal-xl">
                <div class="modal-content border-0">
                    <div class="modal-header bg-blue">
                        <h4 class="modal-title text-white">@lang('info_of') formulario</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times text-white"></i></button>
                    </div>
                    <div class="modal-body">
                       @include('visit::citizens.show_fields')
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-white" data-dismiss="modal"><i class="fa fa-times mr-2"></i>@lang('crud.close')</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end #modal-view-citizens -->

        <!-- begin #modal-form-citizens -->
        <div class="modal fade" id="modal-form-citizens">
            <div class="modal-dialog modal-xl">
                <form @submit.prevent="save()" id="form-citizens">
                    <div class="modal-content border-0">
                        <div class="modal-header bg-blue">
                            <h4 class="modal-title text-white">Formulario</h4>
                            <button @click="clearDataForm()" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times text-white"></i></button>
                        </div>
                        <div class="modal-body" v-if="openForm">
                            @include('visit::citizens.fields')
                        </div>
                        <div class="modal-footer">
                            <button @click="clearDataForm()" class="btn btn-white" data-dismiss="modal"><i class="fa fa-times mr-2"></i>@lang('crud.close')</button>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-2"></i>@lang('crud.save')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- end #modal-form-citizens -->

        <!-- begin  #modal-form-report dimamyc-modal -->
        <dynamic-modal-form modal-id="configurate_mail" size-modal="lg"
        confirmation-message-saved="Confirmar correo"
        title="Configuración de correo defensor" :data-form.sync="dataForm" endpoint="get-configurate-email"
        {{-- :is-update="false" --}}
        >

            <template #fields="scope">
                <div class="panel" data-sortable-id="ui-general-1">
                    <!-- begin panel-body -->
                    <div class="panel-body ">

                        <!-- Status Event Field -->
                        <div class="form-group row m-b-15">
                            {!! Form::label('email','Correo electrónico del defensor encargado:', ['class' => 'col-form-label col-md-3 required']) !!}
                            <div class="col-md-9">
                            {!! Form::email('email', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.email }", 'v-model' => 'scope.dataForm.email', 'required' => true]) !!}
                                
                                <small>Ingrese el correo del defensor. </small>
                            <div class="invalid-feedback" v-if="dataErrors.email">
                            <p class="m-b-0" v-for="error in dataErrors.email">@{{ error }}</p>
                        </div>
                    </div>
                    
                    <!-- end panel-body -->
                </div>
            </template>
        </dynamic-modal-form>
        <!-- end #modal-form-report dimamyc-modal -->
    </div>
</crud>
@endsection

@push('css')
{!!Html::style('assets/plugins/gritter/css/jquery.gritter.css')!!}
@endpush

@push('scripts')
{!!Html::script('assets/plugins/gritter/js/jquery.gritter.js')!!}
<script>
    // detecta el enter para no cerrar el modal sin enviar el formulario
    $('#modal-form-citizens').on('keypress', ':input:not(textarea):not([type=submit])', function (e) {
        if ( e.keyCode === 13 ) { e.preventDefault(); }
    });
</script>
@endpush
