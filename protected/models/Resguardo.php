<?php

/**
 * This is the model class for table "resguardo".
 *
 * The followings are the available columns in table 'resguardo':
 * @property integer $id_resguardo
 * @property string $comentarios
 * @property string $fechaCaptura
 * @property string $nombreEquipo
 * @property integer $capturaUser
 * @property integer $iddepartamento
 * @property integer $idColaboradorEmpleado
 * @property integer $Plataforma_idPlataforma
 *
 * The followings are the available model relations:
 * @property Equipogeneral[] $equipogenerals
 * @property Plataforma $plataformaIdPlataforma
 * @property Colaborador $idColaboradorEmpleado0
 */
class Resguardo extends CActiveRecord {

    public $nombre_colaborador;
    public $departamento;
    public $nombre_hotel;
    public $numeroColaborador;
    public $usuarioNombre;
    public $date_first;
    public $date_last;
    public $nombre_gerencia;
    public $nombre_direccion;
    public $id_equipo;
    public $nombre_PC;
    public $nombre_plataforma;
    public $ape_pat;
    public $total;
    
    public $gerencia;
    public $direccion;

    /**
     * @var string Id del Departamento
     * @soap
     */
    public $DEPTID;

    /**
     * @var string Descripcion del Departamento
     * @soap
     */
    public $DEPDESCR;

    /**
     * @var string Id del Hotel
     * @soap
     */
    public $IDHOTEL;

    /**
     * @var string Descripcion del Hotel
     * @soap
     */
    public $LOCATION;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'resguardo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nombreEquipo,numeroColaborador', 'required'),
            array('Plataforma_idPlataforma', 'required', 'message' => 'Favor de escoger una plataforma'),
            array('nombreEquipo', 'unique'),
            array('capturaUser, idColaboradorEmpleado,numeroColaborador,Plataforma_idPlataforma', 'numerical', 'integerOnly' => true),
            array('nombreEquipo', 'length', 'max' => 35),
            array('comentarios, fechaCaptura', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id_resguardo,total,nombre_plataforma,id_equipo,Plataforma_idPlataforma,nombre_gerencia,nombre_direccion,date_first,date_last, comentarios,nombre_hotel, fechaCaptura,departamento, nombreEquipo,nombre_colaborador,capturaUser, idColaboradorEmpleado', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'equipogenerals' => array(self::HAS_MANY, 'Equipogeneral', 'resguardo_idresguardo'),
            'idColaboradorEmpleado0' => array(self::BELONGS_TO, 'Colaborador', 'idColaboradorEmpleado'),
            'plataformaIdPlataforma' => array(self::BELONGS_TO, 'Plataforma', 'Plataforma_idPlataforma'),
            'equipogeneralsCount' => array(self::STAT, 'Equipogeneral', 'resguardo_idresguardo'),
            'capturaUser0' => array(self::BELONGS_TO, 'Responsable', 'capturaUser'),);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id_resguardo' => 'Id Resguardo',
            'comentarios' => 'Comentarios',
            'fechaCaptura' => 'Fecha Captura',
            'nombreEquipo' => 'Nombre Equipo',
            'capturaUser' => 'Captura User',
            'numeroColaborador' => 'NUM COLABORADOR',
            'usuarioNombre' => 'NOMBRE',
            'nombregerencia' => 'Gerencia',
            'idColaboradorEmpleado' => 'Id Colaborador Empleado',
            'total' => 'Total de equipos en resguardo',
            'DEPTID' => 'DEPARTAMENTO',
            'gerencia' => 'GERENCIA',
            'direccion' => 'DIRECCIÓN',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.


        $criteria = new CDbCriteria;
        $criteria->select = array(
            'nombreEquipo', 'comentarios', 'capturaUser',
            'idColaboradorEmpleado0.usuarioNombre as usuarioNombre',
            'idColaboradorEmpleado0.idColaboradorEmpleado',
            'hotel.nombreHotel as nombreHotel',
            'departamentoIddepartamento.nombredepartamento as nombredepartamento',
        );
        $criteria->with = array('equipogenerals', 'equipogeneralsCount', 'idColaboradorEmpleado0.hotel', 'idColaboradorEmpleado0.departamentoIddepartamento');
        #$criteria->condition = 'nombreHotel = "CEDIS"';

        $criteria->compare('comentarios', $this->comentarios, true);


        $criteria->compare('nombreEquipo', $this->nombreEquipo, true);
        $criteria->compare('capturaUser', $this->capturaUser);
        $criteria->compare('usuarioNombre', $this->nombre_colaborador, true);
        $criteria->compare('idColaboradorEmpleado', $this->idColaboradorEmpleado, true);

        $criteria->compare('nombreHotel', $this->nombre_hotel, true);
        $criteria->compare('nombredepartamento', $this->departamento, true);
        $criteria->compare('equipogeneralsCount', $this->total, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array('pageSize' => 500),
            'sort' => array(
                'attributes' => array(
                    'nombre_colaborador' => array(
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

    public function searchReporte() {

        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria = new CDbCriteria;
        $criteria->with = array('equipogenerals', 'idColaboradorEmpleado0', 'idColaboradorEmpleado0.departamentoIddepartamento.hotelhasdepartamentos.hoteles', 'idColaboradorEmpleado0.departamentoIddepartamento.fkgerencia.fkdireccion');


        $criteria->compare('id_resguardo', $this->id_resguardo);
        $criteria->compare('comentarios', $this->comentarios, true);
        $criteria->compare('fechaCaptura', $this->fechaCaptura, true);

        $criteria->compare('nombreEquipo', $this->nombreEquipo, true);
        $criteria->compare('capturaUser', $this->capturaUser);
        $criteria->compare('usuarioNombre', $this->nombre_colaborador, true);
        $criteria->compare('idColaboradorEmpleado', $this->idColaboradorEmpleado);
        $criteria->compare('nombredepartamento', $this->departamento, true);

        $criteria->compare('nombreHotel', $this->nombre_hotel, true);
        $criteria->compare('nombregerencia', $this->nombre_gerencia, true);
        $criteria->compare('nombredireccion', $this->nombre_direccion, true);
        $criteria->compare('nombrePC', $this->nombre_PC, true);




        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function getListHotel() {
        return CHtml::listData(Hotel::model()->findAll(), 'nombreHotel', 'nombreHotel');
    }

    public static function getListDepartamento() {
        $depart = CHtml::listData(Departamento:: model()->findAll(), 'nombredepartamento', 'nombredepartamento');

        return $depart;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Resguardo the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
