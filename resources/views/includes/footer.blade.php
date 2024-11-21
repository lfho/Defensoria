@php
$ultima_configuracion = DB::table('configuration_general')->latest()->first();
$ultima_version = DB::table('intraweb_version_update')->latest()->first();
$historial_versiones = DB::table('intraweb_version_update')->latest()->get();
@endphp
<!-- begin #footer -->
<div id="footer" class="footer" style="{{ Request::is('*modules') || Request::is('*components*') ? 'margin-left: 5px; margin-right: 5px; bottom: 0px; position: absolute; width: 99%; background-color: '.$ultima_configuracion->color_barra.'; color: white; padding-left: 10px;' : 'bottom: -2%; position: absolute; width: 100%; max-width: -webkit-fill-available;' }}">
	&copy; {{ date("Y") }} <a href="https://web.whatsapp.com/send/?phone=573243018787" target="_blank">Intraweb</a> - Todos los derechos reservados. <a href="#" data-toggle="modal" data-target="#terminos_condiciones_modal">Términos, Condiciones de uso y Aviso Legal.</a><a> Versión: </a><a href="#" data-toggle="modal" data-target="#modal_lanzamiento">{{$ultima_version->numero_version}}</a>
</div>
<!-- end #footer -->

<!-- Modal -->
<div class="modal fade" id="terminos_condiciones_modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h3 class="modal-title">TÉRMINOS Y CONDICIONES DE USO</h3>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	</div>
</div>

<!-- Modal de lanzamientos -->
<div class="modal fade" id="modal_lanzamiento" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h3 class="modal-title">Intraweb {{$ultima_version->numero_version}}</h3>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<h5>Descripción del lanzamiento</h5>
			<pre style="white-space: pre-wrap;">{{$ultima_version->descripcion}}</pre>
			<hr>
			<p>
				<a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
					Historial de lanzamientos
				</a>
			</p>
			<div class="collapse" id="collapseExample">
				<div class="card card-body">
					<div>
						<table class="table text-center mt-2" border="1">
							<thead>
								<tr class="font-weight-bold" style="background-color: #00bcd47d">
									<td>Versión</td>
									<td>Descripción</td>
									<td>Fecha de actualización</td>
								</tr>
							</thead>
							<tbody>
								@foreach($historial_versiones as $historial)
								<tr>
									<td>{{ $historial->numero_version }}</td>
									<td><pre style="white-space: pre-wrap; text-align: left;">{{ $historial->descripcion }}</pre></td>
									<td>{{ $historial->created_at }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	</div>
</div>
<img ref="image" style="display: none;" src="/assets/img/loadingintraweb.gif" alt="Imagen">
