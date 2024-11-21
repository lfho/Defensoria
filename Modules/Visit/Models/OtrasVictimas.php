<?php

namespace Modules\visit\Models;

use Illuminate\Database\Eloquent\Model;

class OtrasVictimas extends Model
{
    public $table = 'vs_otras_victimas';

    public $fillable = [
        'ciudadano_id',
        'nombre_referido',
        'nombres',
        'apellidos',
        'ciclo_vital',
        'tipo_documento',
        'numero_documento',
        'parentezco',
        'tipo_victima'
    ];

    protected $casts = [
        'nombre_referido' => 'string',
        'nombres' => 'string',
        'apellidos' => 'string',
        'ciclo_vital' => 'string',
        'tipo_documento' => 'string',
        'numero_documento' => 'string',
        'parentezco' => 'string',
        'tipo_victima' => 'string'
    ];

    public static array $rules = [
        'ciudadano_id' => 'required',
        'nombre_referido' => 'nullable|string|max:45',
        'nombres' => 'nullable|string|max:45',
        'apellidos' => 'nullable|string|max:45',
        'ciclo_vital' => 'nullable|string|max:45',
        'tipo_documento' => 'nullable|string|max:45',
        'numero_documento' => 'nullable|string|max:45',
        'parentezco' => 'nullable|string|max:45',
        'tipo_victima' => 'nullable|string|max:45',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function ciudadano(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Modules\visit\Models\Ciudadano::class, 'ciudadano_id');
    }
}
