<?php

/**
 * This is the model class for table "departamento".
 *
 * The followings are the available columns in table 'departamento':
 * @property integer $iddepartamento
 * @property string $nombredepartamento
 * @property string $descripciondepartamento
 * @property integer $gerencia_id
 *
 * The followings are the available model relations:
 * @property Colaborador[] $colaboradors
 * @property Gerencia $gerencia
 * @property Hotel[] $hotels
 * @property ResguardoHasDepartamento[] $resguardoHasDepartamentos
 */
class Departamento extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public $nombre_gerencia;
    public $nombre_direccion;

    public function tableName() {
        return 'departamento';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nombredepartamento, gerencia_id', 'required'),
            array('gerencia_id', 'numerical', 'integerOnly' => true),
            array('nombredepartamento', 'length', 'max' => 35),
            array('descripciondepartamento', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('iddepartamento, nombredepartamento, descripciondepartamento, gerencia_id,nombre_gerencia,nombre_direccion', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'colaboradors' => array(self::HAS_MANY, 'Colaborador', 'departamento_iddepartamento'),
            'fkgerencia' => array(self::BELONGS_TO, 'Gerencia', 'gerencia_id'),
            //'hotels' => array(self::MANY_MANY, 'Hotel', 'hotel_has_departamento(departamento_iddepartamento, hotel_id)'),
            'resguardoHasDepartamentos' => array(self::HAS_MANY, 'ResguardoHasDepartamento', 'departamento_iddepartamento'),
            'hotelhasdepartamentos' => array(self::HAS_ONE, 'Hotelhasdepartamento', 'departamento_iddepartamento'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'iddepartamento' => 'ID DEPARTAMENTO',
            'nombredepartamento' => 'NOMBRE',
            'descripciondepartamento' => 'DESCRIPCIÓN',
            'gerencia_id' => 'GERENCIA',
            'nombregerencia' => 'GERENCIA',
            'departamento_iddepartamento' => 'DEPARTAMENTO'
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
        $criteria->with = array('fkgerencia', 'fkgerencia.fkdireccion');

        $criteria->compare('iddepartamento', $this->iddepartamento);
        $criteria->compare('nombredepartamento', $this->nombredepartamento, true);
        $criteria->compare('descripciondepartamento', $this->descripciondepartamento, true);
        $criteria->compare('fkgerencia.nombregerencia', $this->nombre_gerencia, true);
        $criteria->compare('nombredireccion', $this->nombre_direccion, true);


        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'attributes' => array(
                    'nombre_gerencia' => array(
                        'asc' => 'fkgerencia.nombregerencia',
                        'desc' => 'fkgerencia.nombregerencia DESC',
                    ),
                    'nombre_direccion' => array(
                        'asc' => 'fkgerencia.fkdireccion.nombredireccion',
                        'desc' => 'fkgerencia.fkdireccion.nombredireccion DESC',
                    ),
                    '*',
                ),
            ),
        ));
    }

    public static function getListGerencia() {
        return CHtml::listData(Gerencia::model()->findAll(), 'id', 'nombregerencia');
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Departamento the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
