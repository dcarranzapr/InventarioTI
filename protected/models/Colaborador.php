<?php

/**
 * This is the model class for table "colaborador".
 *
 * The followings are the available columns in table 'colaborador':
 * @property integer $id_usuario
 * @property integer $hotel_id
 * @property string $numeroColaborador
 * @property string $usuarioNombre
 * @property string $gerencia
 * @property string $direccion
 * @property integer $departamento_iddepartamento
 *
 * The followings are the available model relations:
 * @property Departamento $departamentoIddepartamento
 * @property Hotel $hotel
 * @property EquipogeneralLog[] $equipogeneralLogs
 * @property Prestamos[] $prestamoses
 * @property PrestamosLog[] $prestamosLogs
 * @property Resguardo[] $resguardos
 */

class Colaborador extends CActiveRecord {
    public static $internalEmployees = array(
        0 => 999999
    );

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'colaborador';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('hotel_id, numeroColaborador, departamento_iddepartamento,usuarioNombre', 'required'),
            array('numeroColaborador', 'unique','attributeName'=>'numeroColaborador','className'=>'colaborador','allowEmpty'=>false, 'message'=>'Ya se ha registrado un Colaborador con el No. {value}'),
            array('hotel_id, departamento_iddepartamento, numeroColaborador', 'numerical', 'integerOnly' => true),
            array('numeroColaborador', 'length', 'max' => 45),
            array('usuarioNombre', 'length', 'max' => 80),
            array('gerencia, direccion', 'length', 'max' => 200),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id_usuario, hotel_id, numeroColaborador, usuarioNombre, departamento_iddepartamento', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'departamentoIddepartamento' => array(self::BELONGS_TO, 'Departamento', 'departamento_iddepartamento'),
            'hotel' => array(self::BELONGS_TO, 'Hotel', 'hotel_id'),
            'equipogeneralLogs' => array(self::HAS_MANY, 'EquipogeneralLog', 'colaborador_id_usuario'),
            'prestamoses' => array(self::HAS_MANY, 'Prestamos', 'idColaboradorEmpleado'),
            'prestamosLogs' => array(self::HAS_MANY, 'PrestamosLog', 'id_colaborador'),
            'resguardos' => array(self::HAS_MANY, 'Resguardo', 'idColaboradorEmpleado'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id_usuario' => 'Llave primario de la tabla colaborador ',
            'hotel_id' => 'Hotel',
            'numeroColaborador' => 'Número del colaborador',
            'usuarioNombre' => 'Nombre del usuario',
            'departamento_iddepartamento' => 'Departamento',
            'gerencia' => 'Gerencia',
            'dirección' => 'Dirección',
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

        $criteria->compare('id_usuario', $this->id_usuario);
        $criteria->compare('hotel_id', $this->hotel_id);
        $criteria->compare('numeroColaborador', $this->numeroColaborador, true);
        $criteria->compare('usuarioNombre', $this->usuarioNombre, true);
        $criteria->compare('departamento_iddepartamento', $this->departamento_iddepartamento);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function getListHotel() {
        return CHtml::listData(Hotel::model()->findAll(), 'id', 'nombreHotel');
    }

    public static function getListDepartamentos() {
        return CHtml::listData(Departamento:: model()->findAll(), 'iddepartamento', 'nombredepartamento');
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Colaborador the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function isInternal()
    {
        return (in_array($this->id_usuario, self::$internalEmployees));
    }
}
