@extends('layouts.default')

@section('title', 'Tablero de control')

@section('section_img', '/assets/img/components/estadistica.png')

@section('menu')
    @if(Auth::user()->accept_service_terms)
        @include('layouts.menu_dashboard')
    @endif
@endsection

@section('content')

    @if(Auth::user()->accept_service_terms)
        <!-- begin widget -->
        <div class="row mt-2" v-show="!query">

            <widget-counter-v2
                icon="fa fa-file-import"
                bg-color-icon="#000080bd"
                {{-- class-css-color="bg-warning" --}}
                :qty="dataForm.total_corr_enviada ?? 0"
                status="redirect"
                title="Correspondencias Enviadas"
                name-field="state"
                :value="searchFields"
                url-redirect="/correspondence/externals"
                title-link-see-more="Ver listado"
            ></widget-counter-v2>

            <widget-counter-v2
                icon="fa fa-sticky-note"
                bg-color-icon="#28a749"
                {{-- class-css-color="bg-blue" --}}
                :qty="dataForm.total_pqrs ?? 0"
                status="redirect"
                title="Total PQRS"
                name-field="state"
                :value="searchFields"
                url-redirect="/pqrs/p-q-r-s"
                title-link-see-more="Ver listado"
            ></widget-counter-v2>

            <widget-counter-v2
                icon="fa fa-file-signature"
                bg-color-icon="#20a6bf"
                {{-- class-css-color="bg-green" --}}
                :qty="dataForm.total_corr_interna_cp ?? 0"
                status="redirect"
                title="Cero Papeles Internas"
                name-field="state"
                :value="searchFields"
                url-redirect="{{ '/correspondence/internals?qd='.base64_encode('DIGITAL') }}"
                title-link-see-more="Ver listado"
            ></widget-counter-v2>

            <widget-counter-v2
                icon="fa fa-file-word"
                bg-color-icon="#ffc433"
                {{-- class-css-color="bg-secondary" --}}
                :qty="dataForm.total_corr_enviada_cp ?? 0"
                status="redirect"
                title="Cero Papeles Enviadas"
                name-field="state"
                :value="searchFields"
                url-redirect="{{ '/correspondence/externals?qd='.base64_encode('DIGITAL') }}"
                title-link-see-more="Ver listado"
            ></widget-counter-v2>

            <fieldset class="card shadow col-md-12 mt-3 mb-4" style="'border: 1px solid rgb(0 0 0 / 6%); border-radius: 5px; padding: 10px; margin: auto; max-width: 90%;">
                <div style="display: flex;">
                    <legend style="max-width: 50px; top: -20px; position: absolute;">
                        <div style="text-align: center; border-radius: 3px; height: 50px; background-color: #2196f3e8">
                            <i class="fa fa-clock" aria-hidden="true" style="vertical-align: bottom; color: white;"></i>
                        </div>
                    </legend>
                    <h5 class="text-black-transparent-6" style="margin-left: 80px; margin-bottom: 20px;">Últimos registros modificados</h5>
                </div>
                <div class="table-responsive">
                    <table-component
                        id="entradas-recientes"
                        :data="dataList"
                        sort-by="recientes"
                        sort-order="asc"
                        table-class="table table-hover m-b-0"
                        :show-filter="false"
                        :show-caption="false"
                        filter-placeholder="@lang('Quick filter')"
                        filter-no-results="@lang('No recent records')"
                        filter-input-class="form-control col-md-4"
                        :cache-lifetime="0"
                        >
                        <table-column show="updated_at" label="@lang('Updated_at')"></table-column>

                        <table-column show="consecutive" label="@lang('Consecutive')"></table-column>

                        <table-column show="title" label="@lang('Título/Asunto')"></table-column>

                        <table-column show="state" label="@lang('State')">
                            <template slot-scope="row">

                                {{-- Documentos Electronicos --}}
                                <div class="text-white text-center p-4 bg-blue states_style" v-html="row.state" v-if="row.state=='Elaboración' && row.module == 'Documentos electrónicos'"></div>
                                <div class="text-black text-center p-4 bg-yellow states_style" v-html="row.state" v-else-if="row.state=='Revisión' || row.state == 'Revisión (Editado por externo)' && row.module == 'Documentos electrónicos'"></div>
                                <div class="text-white text-center p-4 bg-orange states_style" v-html="row.state" v-else-if="row.state=='Pendiente de firma' && row.module == 'Documentos electrónicos'"></div>
                                <div class="text-white text-center p-4 bg-red states_style" v-html="row.state" v-else-if="row.state=='Devuelto para modificaciones' && row.module == 'Documentos electrónicos'"></div>
                                <div class="text-white text-center p-4 bg-green states_style" v-html="row.state" v-else-if="row.state=='Público' && row.module == 'Documentos electrónicos'"></div>

                                {{-- Externa recibida --}}
                                <div class="text-white text-center p-2 bg-orange states_style" v-html="row.state" v-else-if="row.state=='Devuelto' && row.module == 'Externa recibida'"></div>
                                <div class="text-white text-center p-2 bg-green states_style" v-html="row.state" v-else-if="row.state=='Público' && row.module == 'Externa recibida'"></div>
                                <div class="text-white text-center p-4 bg-blue states_style" v-html="row.state" v-else-if="row.state=='Elaboración' && row.module == 'Externa recibida'"></div>

                                {{-- Externa enviada --}}
                                <div class="text-white text-center p-2 bg-orange states_style" v-html="row.state" v-else-if="row.state=='Devuelto' && row.module == 'Externa'"></div>
                                <div class="text-white text-center p-2 bg-green states_style" v-html="row.state" v-else-if="row.state=='Público' && row.module == 'Externa'"></div>
                                <div class="text-white text-center p-4 bg-blue states_style" v-html="row.state" v-else-if="row.state=='Elaboración' && row.module == 'Externa'"></div>
                                <div class="text-black text-center p-4 bg-yellow states_style" v-html="row.state" v-else-if="row.state=='Revisión' && row.module == 'Externa'"></div>
                                <div class="text-white text-center p-4 bg-cyan states_style" v-html="row.state" v-else-if="row.state=='Aprobación' && row.module == 'Externa'"></div>
                                <div class="text-white text-center p-4 bg-orange states_style" v-html="row.state" v-else-if="row.state=='Pendiente de firma' && row.module == 'Externa'"></div>
                                
                                {{-- Interna --}}
                                <div class="text-white text-center p-2 bg-orange states_style" v-html="row.state" v-else-if="row.state=='Devuelto' && row.module == 'Interna'"></div>
                                <div class="text-white text-center p-2 bg-green states_style" v-html="row.state" v-else-if="row.state=='Público' && row.module == 'Interna'"></div>
                                <div class="text-white text-center p-4 bg-blue states_style" v-html="row.state" v-else-if="row.state=='Elaboración' && row.module == 'Interna'"></div>
                                <div class="text-black text-center p-4 bg-yellow states_style" v-html="row.state" v-else-if="row.state=='Revisión' && row.module == 'Interna'"></div>
                                <div class="text-white text-center p-4 bg-cyan states_style" v-html="row.state" v-else-if="row.state=='Aprobación' && row.module == 'Interna'"></div>
                                <div class="text-white text-center p-4 bg-orange states_style" v-html="row.state" v-else-if="row.state=='Pendiente de firma' && row.module == 'Interna'"></div>

                                {{-- Pqrs --}}
                                <div class="text-center estado_a_tiempo" v-html="row.state" v-else-if="row.state=='Asignado' && row.module == 'PQRS'"></div>
                                <div class="text-center estado_a_tiempo" v-html="row.state" v-else-if="row.state=='En trámite' && row.module == 'PQRS'"></div>
                                <div class="text-center estado_a_tiempo" v-html="row.state" v-else-if="row.state=='Esperando respuesta del ciudadano' && row.module == 'PQRS'"></div>
                                <div class="text-center estado_a_tiempo" v-html="row.state" v-else-if="row.state=='Respuesta parcial' && row.module == 'PQRS'"></div>
                                <div class="text-center estado_finalizado_a_tiempo" v-html="row.state" v-else-if="row.state=='Finalizado' && row.module == 'PQRS'"></div>
                                <div class="text-center estado_a_tiempo" v-html="row.state" v-else-if="row.state=='Devuelto' && row.module == 'PQRS'"></div>
                                <div class="text-center estado_cancelado" v-html="row.state" v-else-if="row.state=='Cancelado' && row.module == 'PQRS'"></div>

                                {{-- Estado por defecto --}}
                                <div class="text-center estado_cancelado" v-html="row.state" v-else></div>

                            </template>
                        </table-column>

                        <table-column show="module" label="Módulo"></table-column>

                        <table-column label="@lang('crud.action')" :sortable="false" :filterable="false">
                            <template slot-scope="row">

                                <a :href="row.link" class="btn btn-white btn-icon btn-md" data-toggle="tooltip" data-placement="top" v-if="row.module != 'PQRS'" :title="(row.module != 'Interna' && row.state != 'Público' && row.state != 'Pendiente de firma' && row.permission_edit) || (row.module == 'Interna' && row.permission_edit) ? 'Tramitar' : 'Ver Detalles'"><i :class="(row.module != 'Interna' && row.state != 'Público' && row.state != 'Pendiente de firma' && row.permission_edit) || (row.module == 'Interna' && row.permission_edit) ? 'fas fa-pencil-alt' : 'fas fa-search'"></i></a>

                                <a :href="row.link" class="btn btn-white btn-icon btn-md" data-toggle="tooltip" data-placement="top" v-else :title="(row.module == 'PQRS' && row.elaborated_now == {{ Auth::user()->id }} && state != 'Finalizado') ? 'Tramitar' : 'Ver Detalles'">
                                    <i :class="(row.elaborated_now == {{ Auth::user()->id }} && state != 'Finalizado') ? 'fas fa-pencil-alt' : 'fas fa-search'"></i>
                                </a>

                            </template>
                        </table-column>

                    </table-component>
                </div>
            </fieldset>
        </div>
        <!-- end widget -->

        <search-result-universal v-show="query" ref="resultado" ruta-get-datos="consulta-buscador-dashboard"></search-result-universal>

    @else
        <h1 class="page-header text-center m-b-40 m-t-30">Terminos del servicio</h1>

        <p style="text-align: justify;">

            La empresa <strong>{{ env("APP_NAME") }}</strong> ofrece un servicio de publicación de documentos electrónicos en la {{ env("APP_NAME") }}. Este servicio permite a los usuarios publicar documentos en formato PDF o DOCX para que sean accesibles por otros usuarios de la {{ env("APP_NAME") }}.

            Al utilizar el servicio de publicación de documentos electrónicos en la {{ env("APP_NAME") }}, usted acepta los siguientes términos y condiciones:<br />
            <ul>
                <li>Usted es responsable del contenido de los documentos que publica. La empresa no se responsabiliza de la exactitud, integridad o legalidad del contenido de los documentos publicados.</li>
                <li>Los documentos publicados deben cumplir con las políticas y procedimientos de la empresa. La empresa se reserva el derecho de eliminar cualquier documento que no cumpla con estas políticas y procedimientos.</li>
                <li>La empresa puede cambiar estos términos y condiciones en cualquier momento. Usted será notificado de cualquier cambio por correo electrónico o a través de la {{ env("APP_NAME") }}.</li>
            </ul>
        </p>

        <hr>

        <p class="mb-4">Adicional a la aceptación de términos del servicio, debe cambiar la contraseña, ya que es primer vez que ingresa al sistema de {{ env("APP_NAME") }}.</p>

        <form action="{{ route('service-terms-change-password') }}" method="POST">
            @csrf
            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }} row m-b-15 col-sm-6">
                {!! Form::label('password', trans('Nueva contraseña').':', ['class' => 'col-form-label col-md-4 required']) !!}
                <div class="col-md-6{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input required="required" type="password" class="form-control" name="password">
                    <small>Ingrese una contraseña que tenga mínimo 8 caracteres, debe contener al menos una letra mayúscula, una minúscula, un número y un símbolo - /; 0) & @.?! %*.</small><br />
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong class="text-danger">{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }} row m-b-15 col-sm-6">
                {!! Form::label('password', trans('Confirmar nueva contraseña').':', ['class' => 'col-form-label col-md-4 required']) !!}
                <div class="col-md-6{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input required="required" type="password" name="password_confirmation" class="form-control">
                    <small>Por favor confirme la contraseña que ingresó.</small>
                    @if ($errors->has('password_confirmation'))
                        <span class="help-block">
                            <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-md-12" style="display: flex; justify-content: center;">
                <button type="submit" class="btn btn-success"><i class="fa fa-check mr-2"></i>Aceptar términos y continuar</button>
            </div>
        </form>



    @endif



@endsection
