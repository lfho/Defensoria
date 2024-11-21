<table border="1">
    <thead>
       <tr>
         <td>Fecha de creación del registro</td>
          <td>Nombre</td>
          <td>Tipo documento</td>
          <td>Número documento</td>
          <td>Género</td>
          <td>Ciclo vital</td>
          <td>Tipo victima</td>
          <td>Número celular</td>

       </tr>
    </thead>
    <tbody>
      @foreach ($data as $key => $item)
      <tr>            
         <td>{!! $item['created_at'] !!}</td>
         <td>{!! $item['nombres'] !!}</td>
         <td>{!! $item['tipo_documento'] !!}</td>
         <td>{!! $item['numero_documento']  !!}</td>
         <td>{!! $item['genero'] !!}</td>
         <td>{!! $item['ciclo_vital'] !!}</td>
         <td>{!! $item['numero_celular'] !!}</td>
      </tr> 
      @endforeach
      
    </tbody>
 </table>