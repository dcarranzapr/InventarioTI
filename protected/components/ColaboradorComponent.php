<?php
/**
 * @author Francisco Mahay fmahay@palaceresorts.com
 * 
 * @abstract Obtiene información relacionada con un colaborador de la empresa.
 *
 * @date: 22-NOV-2014
 *
 */
class ColaboradorComponent extends CApplicationComponent
{
	public function init() {}

	/**
	 * @abstract Realiza la búsqueda de un colaborador a través de su Número de colaborador.
	 * 
	 * @param int $id_colaborador Número de colaborador.
	 *
	 * @return array() Información de la búsqueda.
	 *
	 */
	public function buscar($id_colaborador)
	{
		$data = array();
		$data['error'] = false;
		$data['colaborador'] = null;
		$data['msg'] = '';
		$transaction = Yii::app()->db->beginTransaction();

		try {
			$internal = $this->getColaboradorInterno($id_colaborador);
			if (!is_null($internal)) {
				$data['colaborador'] = $internal;
			
			} else {
				#$wsCountry = Yii::app()->params['defaultCountry'];
				$proxyEmpleados = new ProxyPeopleSoft();
				$response = $proxyEmpleados->sendRequest($id_colaborador);
				$colaboradorWS = $proxyEmpleados->getDataConsult($response);

				if ($colaboradorWS != null) {
					if (in_array($colaboradorWS["HR_STATUS"], array('T', 'A'))) {
						$colaborador = $this->getColaboradorPSById($id_colaborador, $colaboradorWS);
						$data['colaborador'] = $colaborador;
						$transaction->commit();	
					}
				} else {
					$data['msg'] = 'El colaborador ha sido dado de baja.';
					$data['error'] = true;
				}
			}
		} catch (Exception $e) {
            $data['msg'] = $e->getMessage();
            $data['error'] = true;
            $transaction->rollBack();
		}

		return $data;
	}

	public function getColaboradorPSById($id, $colaboradorWS)
	{		
		$colaborador = Colaborador::model()->findByPk($id);

        if (is_null($colaborador)) {
            $colaborador = new Colaborador;
            $colaborador->id_usuario = $colaboradorWS["EMPLID"];
		}

		if (empty($colaboradorWS["LOCATION"])) {
			throw new CHttpException(500, 'El colaborador que intenta registrar no pertenece a ningún Hotel. Favor de registrar manualmente.');
		}

        $iddepart = trim($colaboradorWS["DEPTID"], " \t\n\r\0\x0B");
		$hotel = $this->getHotelByName($colaboradorWS["LOCATION"]);
		$departamento = $this->getDepartamentoById($iddepart, $colaboradorWS["DESCR1"], $hotel->id);
		
        $colaborador->usuarioNombre = $colaboradorWS["NAME_DISPLAY"];
        $colaborador->numeroColaborador = $colaboradorWS["EMPLID"];
        $colaborador->hotel_id = $hotel->id;
        $colaborador->departamento_iddepartamento = $departamento->iddepartamento;
        $colaborador->save();

        return $colaborador;
	}

	/**
	 * @abstract Verifica si existe el colaborador a buscar o lo crea.
	 * 
	 * @param int $id Número de colaborador.
	 * @param Object $colaboradorWS Response de WS.
	 *
	 * @return Colaborador Información del colaborar encontrado o creado.
	 *
	 */
	private function getColaboradorById($id, $colaboradorWS) {
		$colaborador = Colaborador::model()->findByPk($id);

        if (is_null($colaborador)) {
        	$iddepart = trim($colaboradorWS->DEPTID, " \t\n\r\0\x0B");
        	
        	$hotel = $this->getHotelByid($colaboradorWS->HOTEL, $colaboradorWS->LOCATION);
        	$departamento = $this->getDepartamentoById($iddepart, $colaboradorWS->DESCR, $hotel->id);

        	$colaborador = new Colaborador;
            $colaborador->id_usuario = $colaboradorWS->EMPLID;
            $colaborador->usuarioNombre = $colaboradorWS->NOMBRE;
            $colaborador->numeroColaborador = $colaboradorWS->NUMERO;
            $colaborador->hotel_id = $hotel->id;
            $colaborador->departamento_iddepartamento = $departamento->iddepartamento;

            $colaborador->save();
        }

        return $colaborador;
	}

	/**
	 * @abstract Verifica si existe el hotel a buscar o lo crea.
	 * 
	 * @param int $id ID del hotel.
	 * @param string $nombre Nombre del hotel.
	 *
	 * @return Hotel Información del hotel encontrado o creado.
	 *
	 */
	private function getHotelByid($id, $nombre) {
		$hotel = Hotel::model()->find('nombreHotel = :_Nombre', array(':_Nombre'=>$nombre));
                
     	if (!is_null($hotel)) {
        	$hotel->nombreHotel = $nombre;
	        $hotel->save();

	    } else {
	    	if ($id == 0 || is_null($id)) {
	    		throw new CHttpException(500, 'El colaborador que intenta registrar no pertenece a ningún Hotel. Favor de registrar manualmente.');
	    	} else {
		    	$hotel = new Hotel;
	            $hotel->id = $id;
	            $hotel->nombreHotel = $nombre;
	            $hotel->save();
	    	}
	    }

	    return $hotel;
	}

	/**
	 * @abstract Verifica si existe el hotel a buscar o lo crea.
	 * 
	 * @param string $nombre Nombre del hotel.
	 *
	 * @return Hotel Información del hotel encontrado o creado.
	 *
	 */
	private function getHotelByName($nombre) {
		$hotel = Hotel::model()->find('nombreHotel = :_Nombre', array(':_Nombre'=>$nombre));
                
     	if (!is_null($hotel)) {
			return $hotel;
		}
		
		$hotel = new Hotel;
		#$hotel->id = $id;
		$hotel->nombreHotel = $nombre;
		$hotel->save();

	    return $hotel;
	}

	/**
	 * @abstract Verifica si existe el departamento a buscar o lo crea.
	 * 
	 * @param int $id ID del departamento.
	 * @param string $nombre Nombre del departamento.
	 * @param int $idHotel ID del hotel al que pertenece.
	 *
	 * @return Departamento Información del departamento encontrado o creado.
	 *
	 */
	private function getDepartamentoById($id, $nombre, $idHotel) {
		$departamento = Departamento::model()->findByPk($id);

		if (!is_null($departamento)) {
            $departamento->nombredepartamento = $nombre;
            $departamento->save();

        } else {
            $departamento = new Departamento;
            $departamento->iddepartamento = $id;
            $departamento->nombredepartamento = $nombre;
            $departamento->descripciondepartamento = "";
            $departamento->gerencia_id = '1';
            $departamento->save();

            $deptoHotel = new Hotelhasdepartamento();
            $deptoHotel->departamento_iddepartamento = $id;
            $deptoHotel->hotel_id = $idHotel;
            $deptoHotel->save();
        }

        return $departamento;
	}

	private function getColaboradorInterno($id)
	{
		if (in_array($id, Colaborador::$internalEmployees)) {
			$colaborador = Colaborador::model()->findByPk($id);

			if (!is_null($colaborador)) {
				return $colaborador;
			}
		}

		return null;
	}
	
	private function getIdHotel($clave)
	{
		switch ($clave) {
			case 'MX':
			return 28;
			case 'JM':
			return 28;
			case 'PA':
			return 290;
		}

		return 0;
	}
}
