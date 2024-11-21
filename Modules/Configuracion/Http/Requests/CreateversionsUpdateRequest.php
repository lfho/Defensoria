<?php

namespace Modules\Configuracion\Http\Requests;

use Modules\Configuracion\Models\versionsUpdate;
use Illuminate\Foundation\Http\FormRequest;

class CreateversionsUpdateRequest extends FormRequest
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
        return versionsUpdate::$rules;
    }
}
