@extends('administrator.layouts.header')

@section('contenido')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Roles</div>

                <div class="panel-body">                    
                    {{ Form::open(['route' => 'roles.store']) }}

                        @include('administrator.roles.partials.form')
                        
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection