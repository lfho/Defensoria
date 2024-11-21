
<li class="{{ Request::is('home*') ? 'active' : '' }}">
    <a href="{{ url('/dashboard') }}"><i class="fa fa-arrow-left"></i><span>@lang('back')</span></a>
</li>
<li class="{{ Request::is('notificaciones-mail-intrawebs*') ? 'active' : '' }}">
    <a href="{{ route('notificaciones-mail-intrawebs.index') }}">
        <i class="fas fa-envelope-open-text"></i>
        <p>Notificaciones intraweb</p>
    </a>
</li>

{{-- <li class="{{ Request::is('listadocorreoscheckeados/listado-correos-checkeos*') ? 'active' : '' }}">
    <a href="{{ route('listado-correos-checkeos.index') }}">
        <i class="fas fa-envelope-open-text"></i>
        <p>Listado de correos checkeados</p>
    </a>
</li> --}}
@role('Administrador intranet')
<li class="{{ Request::is('listado-correos-checkeos*') ? 'active' : '' }}">
    <a href="{{ route('listado-correos-checkeos.index') }}" class="nav-link {{ Request::is('listadoCorreosCheckeos*') ? 'active' : '' }}">
        <i class="fas fa-envelope"></i>
        <p>Listado de correos validados</p>
    </a>
</li>
@endrole
