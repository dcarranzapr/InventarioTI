<?php

/**
 * Clase Web Service Empleados
 *
 * Clase encargada de Gestionar los Web Services de los Empleados o Colaboradores
 * 
 * @package [Web Services SISTUR]
 */
class ProxyEmpleados {

    /**
     * [$clientWS Almacena el objeto resultado de SoapClient()]
     * @var SoapClient::SoapClient()
     */
    public $clientWS = "";

    /**
     * [ProxyEmpleados Constructor de la Clase]
     */
    public function ProxyEmpleados() {
        try {
            $this->clientWS = @new SoapClient(Yii::app()->params['webServiceEmpleados']);
        } catch (SoapFault $e) {
            $re = new ReportaError($e);
            $re->reportar();
        }
    }

    /**
     * [ObtenerEmpleadoporNumero Obtiene el empleado por el numero de colaborador]
     * @param [string] $numero [Numero de empleado o colaborador]
     * @return mixed[] [Regresa un objeto del empleado]
     */
    public function ObtenerEmpleadoporNumero($numero) {
        $empleadosRequest = new EmpleadosRequest();
        $empleadosRequest->numeroEmpleado = $numero;
        try {
            $client = new SoapClient(Yii::app()->params['webServiceEmpleados'], array(
                'exceptions' => 0, //activamos las excepciones
                'classmap' => array(// mapeamos las clases a utilizar
                    'EmpleadosRequest' => 'EmpleadosRequest',
                ),
                'encoding'=>'ISO-8859-1',
                'trace' => 1,
            ));
            $result = $client->__call('ObtenerEmpleados', array('EmpleadosRequest' => $empleadosRequest));
        } catch (SoapFault $e) {
            $result = null;
        }
        return $result;
    }

}
