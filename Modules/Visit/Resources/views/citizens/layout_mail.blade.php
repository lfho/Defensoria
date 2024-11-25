@component('mail::layout')
   {{-- Header --}}
   @slot('header')
      @component('mail::header', ['url' => config('app.url')])
         {{ config('app.name') }}
      @endcomponent
   @endslot

    Datos de visita. <br><br>
    <strong> Nombres: </strong> {{ $data['nombres'] }} <br>
    <strong> Apellidos: </strong> {{ $data['apellidos'] }} <br>
    <strong> Tipo Documento: </strong> {{ $data['tipo_documento'] }} <br>
    <strong> Número de documento: </strong> {{ $data['numero_documento'] }} <br>
    <strong> Género: </strong> {{ $data['genero'] }} <br>
    <strong> Ciclo Vital: </strong> {{ $data['ciclo_vital'] }} <br>
    <strong> Tipo Victima: </strong> {{ $data['tipo_victima'] }} <br>
    <strong> Número celular: </strong> {{ $data['numero_celular'] }} <br>
    <strong> Otras Victima: </strong> {{ $data['otras_victimas'] }} <br><br>
    @if ($data['otras_victimas'] == 'Si')<strong>Otras victimas </strong>
<table border="1" style=" border-collapse: collapse; /* Combina los bordes para evitar líneas dobles */
width: 100%;">
    <thead>
    <tr>
    <th style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">Nombres</th>
    <th style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">Apellidos</th>
    <th style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">Ciclo vital</th>
    <th style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">Tipo documento</th>
    <th style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">Número documento</th>
    <th style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">Parentezco</th>
    <th style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">Tipo víctima</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($data['otrasVictimasList'] as $item)
    <tr>
    <td style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">{{ $item->nombres ?? 'NA' }}</td>
    <td style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">{{ $item->apellidos ?? 'NA' }}</td>
    <td style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">{{ $item->ciclo_vital ?? 'NA' }}</td>
    <td style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">{{ $item->tipo_documento ?? 'NA' }}</td>
    <td style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">{{ $item->numero_documento ?? 'NA' }}</td>
    <td style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">{{ $item->parentezco ?? 'NA' }}</td>
    <td style="border: 1px solid black; /* Define los bordes de las celdas */
    padding: 8px; /* Opcional: espacio interno en las celdas */
    text-align: left;">{{ $item->tipo_victima ?? 'NA' }}</td>
    @endforeach
    </tbody>
</table>
@endif
<br><br><strong> Tipo declaración: </strong> {{ $data['cuestionario'][0]->tipo_declaracion  ?? 'NA'}} <br><br>
<strong> Hechos: </strong> {{ $data['cuestionario'][0]->hechos  ?? 'NA'}} <br><br>
<strong> Impactos y/o secuelas generadas por los hechos: </strong> {{ $data['cuestionario'][0]->secuelas_generadas  ?? 'NA'}} <br><br>
<strong> Patrimonios que se vieron afectados: </strong> {{ $data['cuestionario'][0]->patrimonios_afectados  ?? 'NA'}} <br><br>
<strong> Lesiones fisicas: </strong> {{ $data['cuestionario'][0]->lesiones_fisicass  ?? 'NA'}} <br><br>
<strong> Lesiones psicologicas: </strong> {{ $data['cuestionario'][0]->lesiones_fisicass  ?? 'NA'}} <br><br>
<strong> Descripción: </strong> {{ $data['cuestionario'][0]->descripcion  ?? 'NA'}} <br><br>
<strong> Tiempo en el que actuo: </strong> {{ $data['cuestionario'][0]->tiempo_acto  ?? 'NA'}} <br><br>
<strong> Descripción de los hechos principales: </strong> {{ $data['cuestionario'][0]->descripcio_hecho_principal  ?? 'NA'}} <br><br>

    @slot('footer')
        @component('mail::footer')
            Este correo es de tipo informativo y por lo tanto, le pedimos no responda a éste mensaje..
        @endcomponent
    @endslot
@endcomponent
