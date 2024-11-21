<?php

namespace Modules\visit\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\visit\Models\Cuestionario;


class Citizen extends Model
{
    public $table = 'vs_ciudadano';

    public $fillable = [
        'users_id',
        'nombre_usuario',
        'nombres',
        'apellidos',
        'tipo_documento',
        'numero_documento',
        'numero_celular',
        'genero',
        'ciclo_vital',
        'tipo_victima'
    ];

    protected $casts = [
        'nombre_usuario' => 'string',
        'nombres' => 'string',
        'apellidos' => 'string',
        'tipo_documento' => 'string',
        'numero_documento' => 'string',
        'numero_celular' => 'string',
        'genero' => 'string',
        'ciclo_vital' => 'string',
        'tipo_victima' => 'string'
    ];

    public static array $rules = [
        'users_id' => 'nullable',
        'nombre_usuario' => 'nullable|string|max:100',
        'nombres' => 'nullable|string|max:100',
        'apellidos' => 'nullable|string|max:100',
        'tipo_documento' => 'nullable|string|max:30',
        'numero_documento' => 'nullable|string|max:30',
        'numero_celular' => 'nullable|string|max:12',
        'genero' => 'nullable|string|max:45',
        'ciclo_vital' => 'nullable|string|max:45',
        'tipo_victima' => 'nullable|string|max:50',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    protected $appends = ['tipo_declaracion','hechos','secuelas_generadas','lesiones_psicologicas','lesiones_fisicass','patrimonios_afectados', 'descripcion','tiempo_acto','descripcio_hecho_principal' ];

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Modules\visit\Models\User::class, 'users_id');
    }

    public function cuestionario(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\Modules\visit\Models\Cuestionario::class, 'ciudadano_id');
    }

    public function otrasVictimasList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\Modules\visit\Models\OtrasVictimas::class, 'ciudadano_id');
    }

    public function getTipoDeclaracionAttribute()
    {
        $tipo_declaracion = Cuestionario::select('tipo_declaracion')
            ->where('ciudadano_id', $this->id)
            ->first()
            ->tipo_declaracion ?? '';
        return $tipo_declaracion;
    }
    
    public function getHechosAttribute()
    {
        $hechos = Cuestionario::select('hechos')
            ->where('ciudadano_id', $this->id)
            ->first()
            ->hechos ?? '';
        return $hechos;
    }
    
    public function getSecuelasGeneradasAttribute()
    {
        $secuelas_generadas = Cuestionario::select('secuelas_generadas')
            ->where('ciudadano_id', $this->id)
            ->first()
            ->secuelas_generadas ?? '';
        return $secuelas_generadas;
    }
    
    public function getLesionesPsicologicasAttribute()
    {
        $lesiones_psicologicas = Cuestionario::select('lesiones_psicologicas')
            ->where('ciudadano_id', $this->id)
            ->first()
            ->lesiones_psicologicas ?? '';
        return $lesiones_psicologicas;
    }
    
    public function getLesionesFisicassAttribute()
    {
        $lesiones_fisicass = Cuestionario::select('lesiones_fisicass')
            ->where('ciudadano_id', $this->id)
            ->first()
            ->lesiones_fisicass ?? '';
        return $lesiones_fisicass;
    }
    
    public function getPatrimoniosAfectadosAttribute()
    {
        $patrimonios_afectados = Cuestionario::select('patrimonios_afectados')
            ->where('ciudadano_id', $this->id)
            ->first()
            ->patrimonios_afectados ?? '';
        return $patrimonios_afectados;
    }
    
    public function getDescripcionAttribute()
    {
        $descripcion = Cuestionario::select('descripcion')
            ->where('ciudadano_id', $this->id)
            ->first()
            ->descripcion ?? '';
        return $descripcion;
    }
    
    public function getTiempoActoAttribute()
    {
        $tiempo_acto = Cuestionario::select('tiempo_acto')
            ->where('ciudadano_id', $this->id)
            ->first()
            ->tiempo_acto ?? '';
        return $tiempo_acto;
    }
    
    public function getDescripcioHechoPrincipalAttribute()
    {
        $descripcio_hecho_principal = Cuestionario::select('descripcio_hecho_principal')
            ->where('ciudadano_id', $this->id)
            ->first()
            ->descripcio_hecho_principal ?? '';
        return $descripcio_hecho_principal;
    }
    

}
