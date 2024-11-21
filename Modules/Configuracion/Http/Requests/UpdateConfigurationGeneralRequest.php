<?php

namespace Modules\Configuracion\Http\Requests;

use Modules\Configuracion\Models\ConfigurationGeneral;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigurationGeneralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = ConfigurationGeneral::$rules;
        
        return $rules;
    }
}