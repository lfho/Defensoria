<li class="nav-item {{ Request::is('home*') ? 'active' : '' }}">
    <a href="{{ url('/dashboard') }}" class="nav-link">
        <i class="nav-icon fas fa-arrow-left"></i>
        <span>@lang('back')</span>
    </a>
</li>

{{-- Los siguientes accesos solo puede acceder un usuario que tenga el rol Administrador intranet --}}
<li class="nav-item {{ Request::is('configuracion/documentos-ayudas-publico*') ? 'active' : '' }}">
    <a href="{{ url('configuracion/documentos-ayudas-publico') }}" class="nav-link">
        <i class="nav-icon fas fa-file-alt"></i>
        <span>Documentos de ayuda públicos</span>
    </a>
</li>


