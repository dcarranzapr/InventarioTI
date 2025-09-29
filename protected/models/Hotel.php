<?php

/**
 * This is the model class for table "hotel".
 *
 * The followings are the available columns in table 'hotel':
 * @property integer $id
 * @property string $nombreHotel
 * @property string $descripcion
 * @property integer $zona_id
 *
 * The followings are the available model relations:
 * @property Colaborador[] $colaboradors
 * @property Equipogeneral[] $equipogenerals
 * @property EquipogeneralLog[] $equipogeneralLogs
 * @property hotel_has_departamento $hotel_id
 * @property Departamento[] $departamentos
 * @property UserPrivilegioHotel $userPrivilegioHotel
 */
class Hotel extends CActiveRecord {

    public $id_departamento;
    public $departamento_iddepartamento;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'hotel';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nombreHotel', 'required'),
            array('nombreHotel', 'length', 'max' => 35),
            array('descripcion', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, nombreHotel,departamento_iddepartamento, descripcion,id_departamento', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'colaboradors' => array(self::HAS_MANY, 'Colaborador', 'hotel_id'),
            'equipogenerals' => array(self::HAS_MANY, 'Equipogeneral', 'idHotel'),
            'equipogeneralLogs' => array(self::HAS_MANY, 'EquipogeneralLog', 'idHotel'),
            'hotelhasdepartamento' => array(self::HAS_MANY, 'Hotelhasdepartamento', 'hotel_id'),
            'userPrivilegioHotel' => array(self::HAS_ONE, 'UserPrivilegioHotel', 'hotel_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'nombreHotel' => 'Nombre',
            'descripcion' => 'Descripcion',
            'departamento_iddepartamento' => 'DEPARTAMENTO',
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

        $criteria->compare('id', $this->id);

        $criteria->compare('nombreHotel', $this->nombreHotel, true);
        $criteria->compare('descripcion', $this->descripcion, true);


        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function getListDepartamentos() {
        $depart = CHtml::listData(Departamento:: model()->findAll(), 'iddepartamento', 'nombredepartamento');
        $s = Hotel::model()->with('hotelhasdepartamento', 'hotelhasdepartamento.departamentos')->findAll(array('condition' => 'id=12'));
        $h = CHtml::listData(Hotel::model()->with('hotelhasdepartamento')->findAll(array('condition' => 'id=12')), '', 'nombreHotel');

        return $depart;
    }

    public static function getListHoteles() {
        $hotel = CHtml::listData(Hotel:: model()->findAll(), 'id', 'nombreHotel');

        return $hotel;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Hotel the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
