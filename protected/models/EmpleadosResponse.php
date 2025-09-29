<?php

/**
 * User: jvalderrabano
 * Date: 19/12/13
 * Time: 12:15 PM
 * Recupera una coleccion del empleado encontrado en AsistenciaSQL.dbo.RHEMPDEP
 */
class EmpleadosResponse {

    /**
     * @var string id de la tabla
     * @soap
     */
    public $EMPLID;

    /**
     * @var integer
     * @soap
     */
    public $EMPL_RCD;

    /**
     * @var string Nombre del empleado
     * @soap
     */
    public $NOMBRE;

    /**
     * @var string Id del Departamento
     * @soap
     */
    public $DEPTID;

    /**
     * @var string Descripcion del Departamento
     * @soap
     */
    public $DESCR;

    /**
     * @var datetime Fecha de Ingreso
     * @soap
     */
    public $ENTRY_DATE;

    /**
     * @var string
     * @soap
     */
    public $JOBCODE;

    /**
     * @var string Descripcion del puesto
     * @soap
     */
    public $JOBDESCR;

    /**
     * @var integer Numero de empleado
     * @soap
     */
    public $NUMERO;

    /**
     * @var string Clasificacion del Empleado
     * @soap
     */
    public $EMPLCLASS;

    /**
     * @var string Estado del empleado
     * @soap
     */
    public $EMPL_STATUS;

    /**
     * @var string Compañia
     * @soap
     */
    public $COMPANY;

    /**
     * @var string Locacion
     * @soap
     */
    public $LOCATION;

    /**
     * @var integer Vacaciones
     * @soap
     */
    public $VACACIONES;

    /**
     * @var integer Hotel
     * @soap
     */
    public $HOTEL;

    /**
     * @var string RFC
     * @soap
     */
    public $RFC;

    /**
     * @var datetime Fecha Efectiva
     * @soap
     */
    public $FECHAEFE;

    /**
     * @var datetime Fecha de Terminacion
     * @soap
     */
    public $TERMINATION_DT;

    /**
     * @var string Posicion
     * @soap
     */
    public $POSITION_NBR;

    /**
     * @var boolean
     * @soap
     */
    public $hasErrors;

    /**
     * @var string
     * @soap
     */
    public $errorInfo;

    public function __construct() {
        
    }

}