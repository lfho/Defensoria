<?php

namespace Modules\Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Intranet\Models\Poll;

class UpdatePollRequest extends FormRequest
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
        $rules = Poll::$rules;
        
        return $rules;
    }
}
