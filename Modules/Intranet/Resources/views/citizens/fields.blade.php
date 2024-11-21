<div class="row">
    <!--  Type Person Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('type_person', trans('Type Person').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            <select-check
                css-class="form-control"
                name-field="type_person"
                reduce-label="name"
                reduce-key="id"
                name-resource="get-constants/type_person"
                :value="dataForm"
                :is-required="true">
            </select-check>
            <small>@lang('Select the') el tipo de persona</small>
            <div class="invalid-feedback" v-if="dataErrors.type_person">
                <p class="m-b-0" v-for="error in dataErrors.type_person">@{{ error }}</p>
            </div>
        </div>
    </div>

    <!--  Type Document Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('type_document', trans('Type Document').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            <select-check
                css-class="form-control"
                name-field="type_document"
                reduce-label="name"
                reduce-key="id"
                name-resource="get-constants/type_document"
                :value="dataForm"
                :is-required="true">
            </select-check>
            <small>@lang('Select the') el tipo de documento</small>
            <div class="invalid-feedback" v-if="dataErrors.type_document">
                <p class="m-b-0" v-for="error in dataErrors.type_document">@{{ error }}</p>
            </div>
        </div>
    </div>

    
    <!-- Document Number Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('document_number', trans('Document Number').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            {!! Form::text('document_number', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.document_number }", 'v-model' => 'dataForm.document_number', 'required' => true]) !!}
            <small>@lang('Enter the') @{{ `@lang('First Name')` | lowercase }}</small>
            <div class="invalid-feedback" v-if="dataErrors.document_number">
                <p class="m-b-0" v-for="error in dataErrors.document_number">@{{ error }}</p>
            </div>
        </div>
    </div>

    <!-- Email Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('email', trans('Email').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            {!! Form::email('email', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.email }", 'v-model' => 'dataForm.email', 'required' => true]) !!}
            <small>@lang('Enter the') @{{ `@lang('Email')` | lowercase }}</small>
            <div class="invalid-feedback" v-if="dataErrors.email">
                <p class="m-b-0" v-for="error in dataErrors.email">@{{ error }}</p>
            </div>
        </div>
    </div>

    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('password', trans('Contraseña').':', ['class' => 'col-form-label col-md-3']) !!}
        <div class="col-md-9{{ $errors->has('password') ? ' has-error' : '' }}">
            <input type="password" class="form-control" name="password" v-model="dataForm.password">
            <small>Ingrese una contraseña que tenga mínimo 12 caracteres, debe contener al menos una letra mayúscula, una minúscula, un número y un símbolo - /; 0) & @.?! %*.</small><br />
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('password', trans('Confirmar contraseña').':', ['class' => 'col-form-label col-md-3']) !!}
        <div class="col-md-9{{ $errors->has('password') ? ' has-error' : '' }}">
            <input type="password" name="password_confirmation" class="form-control" v-model="dataForm.password_confirmation">
            <small>Por favor confirme la contraseña que ingresó.</small>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>

            @if ($errors->has('password_confirmation'))
                <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <!-- First Name Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('first_name', trans('First Name').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            {!! Form::text('first_name', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.first_name }", 'v-model' => 'dataForm.first_name', 'required' => true]) !!}
            <small>@lang('Enter the') @{{ `@lang('First Name')` | lowercase }}</small>
            <div class="invalid-feedback" v-if="dataErrors.first_name">
                <p class="m-b-0" v-for="error in dataErrors.first_name">@{{ error }}</p>
            </div>
        </div>
    </div>

    <!-- Second Name Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('second_name', trans('Second Name').':', ['class' => 'col-form-label col-md-3']) !!}
        <div class="col-md-9">
            {!! Form::text('second_name', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.second_name }", 'v-model' => 'dataForm.second_name', 'required' => false]) !!}
            <small>@lang('Enter the') @{{ `@lang('Second Name')` | lowercase }}</small>
            <div class="invalid-feedback" v-if="dataErrors.second_name">
                <p class="m-b-0" v-for="error in dataErrors.second_name">@{{ error }}</p>
            </div>
        </div>
    </div>

    <!-- First Surname Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('first_surname', trans('First Surname').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            {!! Form::text('first_surname', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.first_surname }", 'v-model' => 'dataForm.first_surname', 'required' => true]) !!}
            <small>@lang('Enter the') @{{ `@lang('First Surname')` | lowercase }}</small>
            <div class="invalid-feedback" v-if="dataErrors.first_surname">
                <p class="m-b-0" v-for="error in dataErrors.first_surname">@{{ error }}</p>
            </div>
        </div>
    </div>

    <!-- Second Surname Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('second_surname', trans('Second Surname').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            {!! Form::text('second_surname', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.second_surname }", 'v-model' => 'dataForm.second_surname', 'required' => true]) !!}
            <small>@lang('Enter the') @{{ `@lang('Second Surname')` | lowercase }}</small>
            <div class="invalid-feedback" v-if="dataErrors.second_surname">
                <p class="m-b-0" v-for="error in dataErrors.second_surname">@{{ error }}</p>
            </div>
        </div>
    </div>

    <!-- States Id Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('states_id', trans('Department').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            <select-check
                css-class="form-control"
                name-field="states_id"
                reduce-label="name"
                reduce-key="id"
                name-resource="/get-states-by-country/48"
                :value="dataForm"
                :is-required="true"
                :enable-search="true">
            </select-check>
            <small class="form-text text-muted">@lang('Select the') el @{{ `@lang('Department')` | lowercase }}</small>
            <div class="invalid-feedback" v-if="dataErrors.states_id">
                <p class="m-b-0" v-for="error in dataErrors.states_id">@{{ error }}</p>
            </div>
        </div>
    </div>

    <!-- City Id Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('city_id', trans('City').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            <select-check
                css-class="form-control"
                name-field="city_id"
                reduce-label="name"
                reduce-key="id"
                :name-resource="'/get-cities-by-state/'+dataForm.states_id"
                :value="dataForm"
                :key="dataForm.states_id"
                :is-required="true"
                :enable-search="true">
            </select-check>
            <small class="form-text text-muted">@lang('Select the') la @{{ `@lang('City')` | lowercase }}</small>
            <div class="invalid-feedback" v-if="dataErrors.city_id">
                <p class="m-b-0" v-for="error in dataErrors.city_id">@{{ error }}</p>
            </div>
        </div>
    </div>

    <!-- Address Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('address', trans('Address').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            {!! Form::text('address', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.address }", 'v-model' => 'dataForm.address', 'required' => true]) !!}
            <small>@lang('Enter the') @{{ `@lang('Address')` | lowercase }}</small>
            <div class="invalid-feedback" v-if="dataErrors.address">
                <p class="m-b-0" v-for="error in dataErrors.address">@{{ error }}</p>
            </div>
        </div>
    </div>

    <!-- Phone Field -->
    <div class="form-group row m-b-15 col-sm-6">
        {!! Form::label('phone', trans('Phone').':', ['class' => 'col-form-label col-md-3 required']) !!}
        <div class="col-md-9">
            {!! Form::text('phone', null, [':class' => "{'form-control':true, 'is-invalid':dataErrors.phone }", 'v-model' => 'dataForm.phone', 'required' => true]) !!}
            <small>@lang('Enter the') @{{ `@lang('Phone')` | lowercase }}</small>
            <div class="invalid-feedback" v-if="dataErrors.phone">
                <p class="m-b-0" v-for="error in dataErrors.phone">@{{ error }}</p>
            </div>
        </div>
    </div>
</div>

