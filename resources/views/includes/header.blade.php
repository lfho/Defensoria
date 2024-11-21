<!-- begin #header -->
@php
$ultima_configuracion = DB::table('configuration_general')->latest()->first();


@endphp
<div id="header" class="header navbar-default" style="background-color: {{ $ultima_configuracion->color_barra }}">
    <!-- begin navbar-header -->
    <div class="navbar-header">
    @if(empty($sidebarHide))
        <button type="button" class="navbar-toggle collapsed navbar-toggle-left text-white" data-click="sidebar-minify">
            <i class="fa fa-bars"></i>
        </button>
        <button type="button" class="navbar-toggle text-white" data-click="sidebar-toggled">
            <i class="fa fa-bars"></i>
        </button>
    @else
        <button onclick="window.location='/dashboard'" type="button" class="navbar-toggle collapsed navbar-toggle-left text-white">
            <i class="fa fa-chevron-left"></i>
        </button>
        <button onclick="window.location='/dashboard'" type="button" class="navbar-toggle text-white  pl-3">
            <i class="fa fa-chevron-left"></i>
        </button>
        

    @endif
        <a href="/dashboard" class="navbar-brand text-white mr-0">
            {{ config('app.name') }}
        </a>
        
        {{-- <img src="{{ asset('assets/img/logo_ceropapeles.png') }}" style="width: 120px;" alt="" /> --}}
        <img src="{{ asset('storage')}}/{{ $ultima_configuracion->logo }}" style="max-height: 60px; width: 90px;" alt="" />

    </div>
    
    <!-- end navbar-header --><!-- begin header-nav -->
    <div class="navbar-nav navbar-right" id="buscador_global">
        {{-- Buscador universal --}}
        <search-universal query="buscador" v-if="{{ Request::is('intranet*public') || Request::is('dashboard') ? 1 : 0 }}"></search-universal>
    </div>
    <!-- end navbar-header --><!-- begin header-nav -->
    <ul id="nav_ayudas" class="{{ Request::is('intranet*public') || Request::is('dashboard') ? 'navbar-nav' : 'navbar-nav navbar-right' }}">

      
        <li class="dropdown navbar-user" style="display: inline-flex;">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <span class="d-md-inline text-white">Bienvenido, {{ Auth::user() ? Auth::user()->name : 'Usuario(a)' }}</span>
            @if(!empty(Auth::user()->url_img_profile) && Auth::user()->url_img_profile != "users/avatar/default.png")
                <img src="{{ asset('storage')}}/{{ Auth::user()->url_img_profile }}" alt="" />
            @else
                <img src="{{ asset('assets/img/user/profile.png')}}" alt="" />
            @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                {{-- Si es un ciudadano, no se le muestra el item de editar perfil --}}
                @if(Auth::check() && !Auth::user()->hasRole('Ciudadano') && !Auth::user()->hasRole('Proveedor TIC'))
                    <a href="{{ url('/profile') }}" class="dropdown-item">@lang('edit_profle')</a>
                    <div class="dropdown-divider"></div>
                @endif
                {{-- Valida si tiene sesión el usuario para habilitar la acción de cerrar sesión --}}
                @if(Auth::check())
                    <a href="{{ url('/logout') }}" class="dropdown-item"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        @lang('Logout')
                    </a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endif
            </div>
        </li>
    </ul>
    <!-- end header navigation right -->
</div>
<!-- end #header -->
