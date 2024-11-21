<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Login | {{ config('app.name') }}</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />

	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
	<link rel="icon" href="{{ asset('assets/img/favicon.ico') }}">

	{!!Html::style('assets/css/default/app.min.css')!!}
	<!-- ================== END BASE CSS STYLE ================== -->
</head>
<body class="pace-top">
	<!-- begin #page-loader -->
	<div id="page-loader" class="fade show">
		<span class="spinner"></span>
	</div>
	<!-- end #page-loader -->

	<!-- begin #page-container -->
	<div id="page-container" class="fade">
		<!-- begin login -->
		<div class="login login-with-news-feed">
			<!-- begin news-feed -->
			@php
			$ultima_configuracion = DB::table('configuration_general')->latest()->first();
			@endphp
			<div class="news-feed">
				{{-- <div class="news-image" style="background-image: url(../assets/img/default/login_banner_vuv.png)"></div> --}}
				<div class="news-image" style="background-image: url({{ asset('storage')}}/{{ $ultima_configuracion->imagen_fondo }})"></div>

				<div class="news-caption">
					<h4 class="caption-title"><b></b> </h4>
					<p>
					
					</p>
				</div>
			</div>
			<!-- end news-feed -->
			<!-- begin right-content -->
			<div class="right-content">
				<!-- begin login-header -->
				<div class="login-header">
					<div class="brand">
                        <img src="{{ asset('assets/img/default/icon_login_vuv.png') }}" style="width:30px;height:auto">
						<!--<span class="logo"></span>--> <b>{{ config('app.name') }}</b>
						<small>Servicio de autenticación para ciudadanos y funcionarios</small>
						
					</div>
				</div>
				<!-- end login-header -->
				<!-- begin login-content -->
				<div class="login-content">
					<form method="post" action="{{ url('/login') }}" class="margin-bottom-0">
                        @csrf
						<div class="form-group m-b-15 has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
							<input type="email" class="form-control form-control-lg @if ($errors->has('email')) is-invalid @endif" placeholder="@lang('Email')" name="email" value="{{ old('email') }}" required />
                            @if ($errors->has('email'))
                                <span class="invalid-feedback">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
						</div>
						<div class="form-group m-b-15 has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
							<input type="password" class="form-control form-control-lg @if ($errors->has('password')) is-invalid @endif" placeholder="@lang('Password')" name="password" required />
                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
						</div>
						<div class="login-buttons row">
							<div class="col-6 pr-1">
								<button  type="submit" class="btn btn-primary btn-lg w-100">
									<i class="fa fa-sign-in-alt mr-2"></i>@lang('Login')
								</button>
							</div>
							<div class="col-6 pl-1">
								<a href="{{ url('/register') }}" class="btn btn-link btn-lg w-100">
									<i class="fa fa-user fa-lg mr-2"></i>Registrarme
								</a>
							</div>
						</div>
						
						<div class="m-t-20 m-b-20 text-inverse">
                            Si no recuerda su contraseña haga click <a href="{{ url('/password/reset') }}">Aquí</a><br>
						</div>

						<div class="list-group w-100">
							<div class="list-group-item list-group-item-action disabled">
								<b>Acciones de PQRS</b> <br>
								<small>Haga clic en la opción que desea realizar.</small>
							</div>
							<a href="{{ url('/correspondence/search-pqrs-ciudadano') }}" class="list-group-item list-group-item-action btn">
								<i class="fa fa-search fa-lg mr-2"></i> Consultar PQRS de correspondencias recibidas.
							</a>
							<a href="{{ url('/register') }}" class="list-group-item list-group-item-action btn">
								<i class="fa fa-user fa-lg mr-2"></i> Regístrese como ciudadano para crear y hacer seguimiento de sus PQRS.
							</a>
							<a href="{{ url('/pqrs/p-q-r-s-ciudadano-anonimo') }}" class="list-group-item list-group-item-action btn">
								<i class="fa fa-user-secret fa-lg mr-2"></i> Si es un ciudadano anónimo, ingrese aquí para crear y consultar PQRS.
							</a>
						</div>
						

						<hr />
						<p class="text-center text-grey-darker mb-0">
							{{ config('app.name') }}
						</p>
					</form>
					<!-- Horarios de Atención -->
					
					@if (!empty($ultima_configuracion->horario))
					<div class="text-center mt-4">
						<p class="text-center text-grey-darker mb-0">{{ $ultima_configuracion->horario ?? null }}</p>
					</div>
					@else
					<div class="text-center mt-4">
						<hr/>
						&copy; {{ date("Y") }} <a href="https://web.whatsapp.com/send/?phone=573243018787" target="_blank">Intraweb</a> - Todos los derechos reservados. <a href="#" data-toggle="modal" data-target="#terminos_condiciones_modal">Términos, Condiciones de uso y Aviso Legal</a>
					</div>
					@endif
					
					
				</div>
				<!-- end login-content -->
			</div>
			<!-- end right-container -->
		</div>
		<!-- end login -->

		<!-- begin scroll to top btn -->
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
		<!-- end scroll to top btn -->
	</div>
	<!-- end page container -->

	<!-- ================== BEGIN BASE JS ================== -->
	{!!Html::script('assets/js/app.min.js')!!}
	{!!Html::script('assets/js/theme/default.min.js')!!}
	
	<!-- ================== END BASE JS ================== -->
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>    
    
    <script>
        function funcionSwal() {
            Swal.fire({
                text: 'Ocurrió un problema al conectarse al servidor. Intente más tarde o contacte al soporte técnico.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        // Aquí verificas si hay errores y llamas a la función
        @if ($errors->has('server'))
            funcionSwal();
        @endif
    </script>

</html>
