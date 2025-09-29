<?php

/**
 * This is the model class for table "prestamos".
 *
 * The followings are the available columns in table 'prestamos':
 * @property integer $id
 * @property integer $idColaboradorEmpleado
 * @property integer $estatus_idEstatus
 * @property string $fecha_devolucion
 * @property string $nombreEquipo
 * @property string $fecha_prestamo
 * @property integer $proroga
 * @property string $descripcion
 * @property integer $capturaUser
 * @property integer $Plataforma_idPlataforma
 *
 * The followings are the available model relations:
 * @property Colaborador $colaboradorIdUsuario
 * @property Equipogeneral[] $equipogenerals
 * @property Estatus $estatusIdEstatus
 * @property Plataforma $plataformaIdPlataforma
 * @property Colaborador $idColaboradorEmpleado0
 */
class Prestamos extends CActiveRecord {

    public $numeroColaborador;
    public $nombre_usuario;
    public $estatus;
    public $dias;
    public $usuarioNombre;
    public $numero_de_serie;
    public $nombre_plataforma;
    public $nombre_hotel;
    public $departamento;
    public $nombreResponsable;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Prestamos the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'prestamos';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('idColaboradorEmpleado, fecha_devolucion, fecha_prestamo,nombreEquipo,proroga', 'required'),
            array('Plataforma_idPlataforma', 'required', 'message' => 'Favor de escoger una plataforma'),
            array('idColaboradorEmpleado,proroga,numeroColaborador, estatus_idEstatus,Plataforma_idPlataforma', 'numerical', 'integerOnly' => true),
            array('descripcion', 'safe'),
            array('nombreEquipo', 'length', 'max' => 35),
            array('nombreEquipo', 'unique'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id,usuarioApellidoPat,nombre_hotel,departamento,nombre_plataforma,Plataforma_idPlataforma, idColaboradorEmpleado, estatus_idEstatus, nombreEquipo,fecha_devolucion, fecha_prestamo, proroga, descripcion,numeroColaborador,nombre_usuario', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'colaboradorIdUsuario' => array(self::BELONGS_TO, 'Colaborador', 'idColaboradorEmpleado'),
            'equipoGeneral' => array(self::BELONGS_TO, 'Equipogeneral', 'equipoGeneral_id'),
            'estatusIdEstatus' => array(self::BELONGS_TO, 'Estatus', 'estatus_idEstatus'),
            'plataformaIdPlataforma' => array(self::BELONGS_TO, 'Plataforma', 'Plataforma_idPlataforma'),
            'equipogeneralsCount' => array(self::STAT, 'Equipogeneral', 'idPrestamo'),
            'capturaUser0' => array(self::BELONGS_TO, 'Responsable', 'capturaUser'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'colaborador_id_usuario' => 'Colaborador Id Usuario',
            'estatus_idEstatus' => 'Estatus Id Estatus',
            'fecha_devolucion' => 'Fecha Devoluci&oacuten',
            'fecha_prestamo' => 'Fecha Prestamo',
            'proroga' => 'Prorroga',
            'descripcion' => 'Descripcion',
            'estatus' => 'Estado',
            'usuarioNombre' => 'Nombre',
            'nombre_usuario' => 'Nombre',
            'nombreEquipo' => 'Nombre del equipo',
            'nombre_plataforma' => 'Nombre de la plataforma',
            'nombreResponsable' => 'Nombre ingeniero soporte',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->with = array('colaboradorIdUsuario', 'colaboradorIdUsuario.hotel', 'colaboradorIdUsuario.departamentoIddepartamento');
        $criteria->compare('id', $this->id);
        $criteria->compare('estatus_idEstatus', $this->estatus_idEstatus);
        $criteria->compare('fecha_devolucion', $this->fecha_devolucion, true);
        $criteria->compare('fecha_prestamo', $this->fecha_prestamo, true);
        $criteria->compare('nombreEquipo', $this->nombreEquipo, true);
        $criteria->compare('proroga', $this->proroga, true);
        $criteria->compare('descripcion', $this->descripcion, true);
        $criteria->compare('usuarioNombre', $this->usuarioNombre, true);
        $criteria->compare('nombreHotel', $this->nombre_hotel, true);
        $criteria->compare('nombredepartamento', $this->departamento, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'attributes' => array(
                    'usuarioNombre' => array(
                        'asc' => 'usuarioNombre',
                        'desc' => 'usuarioNombre DESC',
                    ),
                    'nombre_hotel' => array(
                        'asc' => 'nombreHotel',
                        'desc' => 'nombreHotel DESC',
                    ),
                    'departamento' => array(
                        'asc' => 'nombredepartamento',
                        'desc' => 'nombredepartamento DESC',
                    ),
                    '*',
                ),
            ),
        ));
    }

    public static function getListHotel() {
        return CHtml::listData(Hotel::model()->findAll(), 'nombreHotel', 'nombreHotel');
    }

    public static function getListDepartamento() {
        $depart = CHtml::listData(Departamento:: model()->findAll(), 'nombredepartamento', 'nombredepartamento');

        return $depart;
    }

    public function searchPrestamos() {
        $criteria = new CDbCriteria;
        $criteria->select = array('colaboradorIdUsuario.usuarioNombre as nombre_usuario',
            'capturaUser0.nombre as nombreResponsable', 'descripcion', 'fecha_prestamo', 'proroga',
            'fecha_devolucion', '(fecha_devolucion < now()) as estatus', 'DATEDIFF(fecha_devolucion, now())+proroga as dias');
        $criteria->condition = 'fecha_devolucion < ADDDATE(NOW(), 31)';
        $criteria->with = array('colaboradorIdUsuario', 'capturaUser0');
        $criteria->compare('nombreEquipo', $this->nombreEquipo, true);
        $criteria->compare('nombre_usuario', $this->nombre_usuario, true);
        $criteria->compare('fecha_devolucion', $this->fecha_devolucion, true);
        $criteria->compare('fecha_prestamo', $this->fecha_prestamo, true);
        $criteria->compare('proroga', $this->proroga, true);
        $criteria->compare('descripcion', $this->descripcion, true);
        $criteria->compare('estatus', $this->estatus);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array('pageSize' => 8),
            'sort' => array(
                'attributes' => array(
                    'nombre_usuario' => array(
                        'asc' => 'colaboradorIdUsuario.nombre_usuario',
                        'desc' => 'colaboradorIdUsuario.nombre_usuario DESC',
                    ),
                    'nombreResponsable' => array(
                        'asc' => 'capturaUser0.nombre_usuario',
                        'desc' => 'capturaUser0.nombre_usuario DESC',
                    ),
                    'estatus' => array(
                        'asc' => 'estatus',
                        'desc' => 'estatus DESC',
                    ),
                    '*',
                ),
            ),
        ));
    }

}
