<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">

   <!-- CSRF Token -->
   <meta name="csrf-token" content="{{ csrf_token() }}">

   <title>{{ config('app.name') }}</title>

   <!-- Hojas de estilo -->
   {!!Html::style('css/app.css')!!}
   {!!Html::style('css/front/estilos.css')!!}
   @yield('style')
</head>
<body>



   <div class="container-fluid contenido">
      @yield('contenido')
   </div>
@footerFront