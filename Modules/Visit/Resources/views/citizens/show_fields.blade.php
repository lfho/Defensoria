<div  class="panel" data-sortable-id="ui-general-1">
    <div class="panel-body">
        <div class="row">

            <div class="row col-6">
                <!-- Nombres Field -->
                <dt class="text-inverse text-left col-4 ">@lang('Nombres'):</dt>
                <dd class="col-8 ">@{{ dataShow.nombres }}.</dd>
             </div>
             
            
            <div class="row col-6">
                <!-- Apellidos Field -->
                <dt class="text-inverse text-left col-4 ">@lang('Apellidos'):</dt>
                <dd class="col-8">@{{ dataShow.apellidos }}.</dd>
             </div>


  
            <div class="row col-6">
                <!-- Tipo Documento Field -->
                <dt class="text-inverse text-left col-4 ">@lang('Tipo Documento'):</dt>
                <dd class="col-8 ">@{{ dataShow.tipo_documento }}.</dd>
            </div>
            

            <div class="row col-6">
                <!-- Numero Documento Field -->
                <dt class="text-inverse text-left col-4 ">@lang('Numero Documento'):</dt>
                <dd class="col-8 ">@{{ dataShow.numero_documento }}.</dd>
            </div>


            <div class="row col-6">
                <!-- Numero Celular Field -->
                <dt class="text-inverse text-left col-4 ">@lang('Numero Celular'):</dt>
                <dd class="col-8 ">@{{ dataShow.numero_celular }}.</dd>
             </div>
             
            
            <div class="row col-6">
                <!-- Genero Field -->
                <dt class="text-inverse text-left col-4 ">@lang('Genero'):</dt>
                <dd class="col-8 ">@{{ dataShow.genero }}.</dd>
             </div>


            <div class="row col-6">
                <!-- Ciclo Vital Field -->
                <dt class="text-inverse text-left col-4 ">@lang('Ciclo Vital'):</dt>
                <dd class="col-8 ">@{{ dataShow.ciclo_vital }}.</dd>
             </div>
             
            
            <div class="row col-6">
                <!-- Tipo Victima Field -->
                <dt class="text-inverse text-left col-4 ">@lang('Tipo Victima'):</dt>
                <dd class="col-8 ">@{{ dataShow.tipo_victima }}.</dd>
             </div>

             <div  class="row col-6">
                <!-- Tipo Victima Field -->
                <dt class="text-inverse text-left col-4 ">@lang('Otras victimas'):</dt>
                <dd class="col-8 ">@{{ dataShow.otras_victimas }}.</dd>
             </div>

             <div v-if="dataShow.otras_victimas == 'Si'" class="panel-body col-12">
                <div class="table-responsive">
                    <table  class="table text-center mt-2" border="1">
                        <thead>
                        <tr class="font-weight-bold">
                            <td>Nombres</td>
                            <td>Apellidos</td>
                            <td>Ciclo vital	</td>
                            <td>Tipo documento</td>
                            <td>Número documento</td>
                            <td>Parentezco</td>
                            <td>Tipo víctima</td>

                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="victima in dataShow.otras_victimas_list">
                            <td>@{{ victima.nombres }}</td>
                            <td>@{{ victima.apellidos }}</td>
                            <td>@{{ victima.ciclo_vital }}</td>
                            <td>@{{ victima.tipo_documento }}</td>
                            <td>@{{ victima.numero_documento }}</td>
                            <td>@{{ victima.parentezco }}</td>
                            <td>@{{ victima.tipo_victima }}</td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div> 
            
    </div> 
</div>

<div  class="panel" data-sortable-id="ui-general-1">
    <div class="panel-body">
        <div class="row">

            <div class="row col-12">
                <!-- Nombres Field -->
                <dt class="text-inverse text-left col-4 "> Tipo declaración:</dt>
                <dd class="col-8 ">@{{ dataShow.cuestionario?.[0]?.tipo_declaracion }}.</dd>
             </div>

             <div class="row col-12">
                <!-- Nombres Field -->
                <dt class="text-inverse text-left col-4 ">  Hechos:</dt>
                <dd class="col-8 ">@{{ dataShow.cuestionario?.[0]?.hechos }}.</dd>
             </div>

             <div class="row col-12">
                <!-- Nombres Field -->
                <dt class="text-inverse text-left col-4 "> Impactos y/o secuelas generadas por los hechos:</dt>
                <dd class="col-8 ">@{{ dataShow.cuestionario?.[0]?.secuelas_generadas }}.</dd>
             </div>

             <div class="row col-12">
                <!-- Nombres Field -->
                <dt class="text-inverse text-left col-4 ">Patrimonios que se vieron afectados:</dt>
                <dd class="col-8 ">@{{ dataShow.cuestionario?.[0]?.patrimonios_afectados }}.</dd>
             </div>

             <div class="row col-12">
                <!-- Nombres Field -->
                <dt class="text-inverse text-left col-4 ">Lesiones fisicas:</dt>
                <dd class="col-8 ">@{{ dataShow.cuestionario?.[0]?.lesiones_fisicass }}.</dd>
             </div>

             <div class="row col-12">
                <!-- Nombres Field -->
                <dt class="text-inverse text-left col-4 ">lesiones psicologicas:</dt>
                <dd class="col-8 ">@{{ dataShow.cuestionario?.[0]?.lesiones_psicologicas }}.</dd>
             </div>

             <div class="row col-12">
                <!-- Nombres Field -->
                <dt class="text-inverse text-left col-4 ">Tiempo en el que actuo:</dt>
                <dd class="col-8 ">@{{ dataShow.cuestionario?.[0]?.tiempo_acto }}.</dd>
             </div>

             <div class="row col-12">
                <!-- Nombres Field -->
                <dt class="text-inverse text-left col-4 ">Descripción:</dt>
                <dd class="col-8 ">@{{ dataShow.cuestionario?.[0]?.descripcion }}.</dd>
             </div>

             <div class="row col-12">
                <!-- Nombres Field -->
                <dt class="text-inverse text-left col-4 ">Descripción de los hechos principales:</dt>
                <dd class="col-8 ">@{{ dataShow.cuestionario?.[0]?.descripcio_hecho_principal }}.</dd>
             </div>
             
            
        

        </div>
    </div> 
</div>


 


 

 


 


