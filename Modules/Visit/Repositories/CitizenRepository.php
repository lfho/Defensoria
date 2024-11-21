<?php

namespace Modules\visit\Repositories;

use Modules\visit\Models\Citizen;
use App\Repositories\BaseRepository;

class CitizenRepository extends BaseRepository
{
    protected $fieldSearchable = [
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

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Citizen::class;
    }
}
