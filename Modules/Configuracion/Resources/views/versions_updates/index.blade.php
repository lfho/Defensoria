@extends('layouts.default')

@section('title', 'Lanzamientos')

@section('section_img', '')

@section('menu')
    @include('configuracion::layouts.menu')
@endsection

@section('content')

<crud
    name="versions-updates"
    :resource="{default: 'versions-updates', get: 'get-versions-updates'}"
    inline-template>
    <div>
        <!-- begin breadcrumb -->
        <ol class="breadcrumb m-b-10">
            <li class="breadcrumb-item"><a href="{!! url('/dashboard') !!}">@lang('home')</a></li>
            <li class="breadcrumb-item active">lanzamientos</li>
        </ol>
        <!-- end breadcrumb -->

        <!-- begin page-header -->
        <h1 class="page-header text-center m-b-35">@{{ '@lang('main_view_to_manage') de lanzamientos'}}</h1>
        <!-- end page-header -->

        <!-- begin main buttons -->
        <div class="m-t-20">
            <button @click="add()" type="button" class="btn btn-primary m-b-10" data-backdrop="static" data-target="#modal-form-versions-updates" data-toggle="modal">
                <i class="fa fa-plus mr-2"></i>Crear version
            </button>
        </div>
        <!-- end main buttons -->

        <!-- begin panel -->
        <div class="panel panel-default">
            <div class="panel-heading border-bottom">
                <div class="panel-title">
                    <h5 class="text-center"> @{{ `@lang('total_registers') versiones de lanzamiento: ${dataPaginator.total}` | capitalize }}</h5>
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
                                {!! Form::text('numero_version', null, ['v-model' => 'searchFields.numero_version', 'class' => 'form-control', 'placeholder' => trans('crud.filter_by', ['name' => "Número de versión"]) ]) !!}
                            </div>
                            <div class="col-md-4">
                                {!! Form::text('descripcion', null, ['v-model' => 'searchFields.descripcion', 'class' => 'form-control', 'placeholder' => trans('crud.filter_by', ['name' => "Descripción"]) ]) !!}
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" v-model="searchFields.tipo_de_cambio" name="tipo_de_cambio">
                                    <option value="Mejora Estructural">Mejora Estructural</option>
                                    <option value="Mejora Mediana">Mejora Mediana</option>
                                    <option value="Mejora Pequeña">Mejora Pequeña</option>
                                </select>
                                <small>Filtrar por tipo de cambio</small>
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

                <!-- end buttons action table -->
                @include('configuracion::versions_updates.table')
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

        <!-- begin #modal-view-versions-updates -->
        <div class="modal fade" id="modal-view-versions-updates">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0">
                    <div class="modal-header bg-blue">
                        <h4 class="modal-title text-white">@lang('info_of') lanzamiento de versiones</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times text-white"></i></button>
                    </div>
                    <div class="modal-body">
                       @include('configuracion::versions_updates.show_fields')
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-white" data-dismiss="modal"><i class="fa fa-times mr-2"></i>@lang('crud.close')</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end #modal-view-versions-updates -->

        <!-- begin #modal-form-versions-updates -->
        <div class="modal fade" id="modal-form-versions-updates">
            <div class="modal-dialog modal-lg">
                <form @submit.prevent="save()" id="form-versions-updates">
                    <div class="modal-content border-0">
                        <div class="modal-header bg-blue">
                            <h4 class="modal-title text-white">@lang('form_of') lanzamiento de versiones</h4>
                            <button @click="clearDataForm()" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times text-white"></i></button>
                        </div>
                        <div class="modal-body" v-if="openForm">
                            @include('configuracion::versions_updates.fields')
                        </div>
                        <div class="modal-footer">
                            <button @click="clearDataForm()" class="btn btn-white" data-dismiss="modal"><i class="fa fa-times mr-2"></i>@lang('crud.close')</button>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-2"></i>@lang('crud.save')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- end #modal-form-versions-updates -->
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
    $('#modal-form-versions-updates').on('keypress', ':input:not(textarea):not([type=submit])', function (e) {
        if ( e.keyCode === 13 ) { e.preventDefault(); }
    });
</script>
@endpush
