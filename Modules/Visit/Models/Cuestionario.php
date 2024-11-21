<?php

namespace Modules\visit\Models;

use Illuminate\Database\Eloquent\Model;

class Cuestionario extends Model
{
    public $table = 'vs_cuestionario';

    public $fillable = [
        'ciudadano_id',
        'tipo_declaracion',
        'hechos',
        'secuelas_generadas',
        'lesiones_fisicass',
        'lesiones_psicologicas',
        'patrimonios_afectados',
        'descripcion',
        'tiempo_acto',
        'descripcio_hecho_principal'
    ];

    protected $casts = [
        'tipo_declaracion' => 'string',
        'hechos' => 'string',
        'secuelas_generadas' => 'string',
        'lesiones_fisicass' => 'string',
        'lesiones_psicologicas' => 'string',
        'patrimonios_afectados' => 'string',
        'descripcion' => 'string',
        'tiempo_acto' => 'string',
        'descripcio_hecho_principal' => 'string'
    ];

    public static array $rules = [
        'ciudadano_id' => 'required',
        'tipo_declaracion' => 'nullable|string',
        'hechos' => 'nullable|string',
        'secuelas_generadas' => 'nullable|string',
        'lesiones_fisicass' => 'nullable|string|max:45',
        'lesiones_psicologicas' => 'nullable|string|max:45',
        'patrimonios_afectados' => 'nullable|string|max:45',
        'descripcion' => 'nullable|string',
        'tiempo_acto' => 'nullable|string',
        'descripcio_hecho_principal' => 'nullable|string',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function ciudadano(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Modules\visit\Models\VsCiudadano::class, 'ciudadano_id');
    }
}
