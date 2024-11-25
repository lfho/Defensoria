<?php

namespace Modules\Visit\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UtilController extends AppBaseController {

	/**
    * Elimina las listas vacias de un objeto o las vuelve nulas
	 *
	 * @author Jhoan Sebastian Chilito S. - Jul. 07 - 2020
	 * @version 1.0.0
	 *
	 * @param Object $object objeto contenedor de propiedades
	 * @param Boolean $nulleable valida si el areglo debe hacerce nulo
	 * @param Array $properties propiedades de objeto
	 */
	public static function dropNullEmptyList($object, $nulleable, ...$properties) {
		// Recorre las propiedades a verificar
		foreach ($properties as $prop) {
			// Valida si la lista no tiene elementos
			if (count($object[$prop]) === 0) {
				// Valida si se debe asignar el valor nulo o se debe eliminar el arreglo vacio
				if ($nulleable === true) {
					// Asigna valor nulo
					$object[$prop] = null;
				} else {
					// Elimina la propiedad vacia
					unset($object[$prop]);
				}
			}
		}
		return $object;
	}

	/**
     * Obtiene los datos de una constante dependiendo del nombre
     *
     * @author Carlos Moises Garcia T. - Oct. 27 - 2020
     * @version 1.0.0
     *
	  * @param nameConstant nombre de la constante a obtener
	  * 
     * @return Response
     */
	public function getConstants($nameConstant) {
		$dateConstant = config('visit.'.$nameConstant);
		return $this->sendResponse($dateConstant, trans('data_obtained_successfully'));
	}

	
}