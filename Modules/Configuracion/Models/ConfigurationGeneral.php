<?php

namespace Modules\Configuracion\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigurationGeneral extends Model
{
    public $table = 'configuration_general';

    public $fillable = [
        'logo',
        'color_barra',
        'color_modal',
        'imagen_fondo',
        'nombre_entidad',
        'horario'
    ];

    protected $casts = [
        'logo' => 'string',
        'color_barra' => 'string',
        'color_modal' => 'string',
        'imagen_fondo' => 'string',
        'nombre_entidad' => 'string'
    ];

    public static array $rules = [
        
    ];

    
}
