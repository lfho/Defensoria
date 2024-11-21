{{-- Componente para cargar los valores del tablero inicial --}}
<dashboard :data-form="dataForm"></dashboard>

@php
    $userRoles = auth()->user()->roles->pluck('name')->toArray();
    // Usuario actual en sesión
    $usuario = Auth::user();
    // Obtiene el número de expedientes disponibles de consulta, según la depedencia y el id del usuario
    $expediente = null;
@endphp

<li class="nav-item">
    <a href="{{ route('citizens.index') }}" class="nav-link {{ Request::is('citizens*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Formulario</p>
    </a>
</li>


{{-- Valida los roles para mostrar el módulo de IntrEstructura Organizacionalanet --}}
@if(in_array('Administrador intranet', $userRoles))
    {{-- Estructura Organizacional --}}
    <li style="display: flex;">
        <a href="{{ route('users.index') }}" style="display: flex; width: -webkit-fill-available;">
            <i class="fa fa-user"></i>
            <span>Estructura Organizacional</span>
        </a>
    </li>

@endif

{{-- Valida los roles para mostrar el módulo de Configuración --}}
@if(in_array('Administrador intranet', $userRoles))
    {{-- Configuración --}}
    <li style="display: flex;">
        <a href="{{ route('configuration-generals.index') }}" style="display: flex; width: -webkit-fill-available;">
            <i class="fa fa-cog"></i>
            <span>Configuración</span>
        </a>
    </li>
@endif


@push('scripts')
<script>
    // Obtiene el evento de la flecha de los items de menú del lado izquierdo
    // $("li").click(function () {
    //     // Itera entre el icon de la flecha hacia abajo y hacia arriba según sea el caso
    //     $(this).children('.fa').toggleClass('fa-chevron-down fa-chevron-up');
    // });

    // Función para alternar entre expandido y colapsado los menús de la barra lateral
    function toggleContent(container, target) {
        // Alterna la clase expanded al element <i> del menú principal
        container.classList.toggle('expanded');

        // Cambia la dirección de la flecha
        if (container.classList.contains('expanded')) {
            container.classList.remove('fa-chevron-down');
            container.classList.add('fa-chevron-up');
            // Obtiene el contenedor a desplegar por medio del target recibido por parámetro
            var arrowIcon = document.querySelector(target);
            // Espera 2 segundos para añadir la clase show al contenedor submenú
            setTimeout(() => {
                arrowIcon.classList.add('show');
            }, 200);
        } else {
            container.classList.remove('fa-chevron-up');
            container.classList.add('fa-chevron-down');
            // Obtiene el contenedor a colapsar por medio del target recibido por parámetro
            var arrowIcon = document.querySelector(target);
            // Espera 2 segundos para remover la clase show al contenedor submenú
            setTimeout(() => {
                arrowIcon.classList.remove('show');
            }, 200);
        }
    }
</script>
@endpush
