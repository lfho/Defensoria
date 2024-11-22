<?php

namespace Modules\visit\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigurationMail extends Model
{
    public $table = 'vs_mail_configuration';

    public $fillable = [
        'email'
    ];

    protected $casts = [
        'email' => 'string'
    ];

    public static array $rules = [
        'email' => 'nullable|string',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    
}
