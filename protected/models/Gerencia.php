<?php

/**
 * This is the model class for table "gerencia".
 *
 * The followings are the available columns in table 'gerencia':
 * @property integer $id
 * @property string $nombregerencia
 * @property string $descripcion
 * @property integer $direccion_id
 *
 * The followings are the available model relations:
 * @property Departamento[] $departamentos
 * @property Direccion $direccion
 */
class Gerencia extends CActiveRecord {

    public $nombre_direccion;
    public $nombre_gerencia;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'gerencia';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nombregerencia, direccion_id', 'required'),
            array('direccion_id', 'numerical', 'integerOnly' => true),
            array('nombregerencia', 'length', 'max' => 45),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, nombregerencia, direccion_id,nombre_direccion,nombre_gerencia', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'departamentos' => array(self::HAS_MANY, 'Departamento', 'gerencia_id'),
            'fkdireccion' => array(self::BELONGS_TO, 'Direccion', 'direccion_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'nombregerencia' => 'GERENCIA',
            'descripciongerencia' => 'DESCRIPCION',
            'direccion_id' => 'Direccion',
            'nombre_direccion' => 'DIRECCION'
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
        $criteria->with = array('fkdireccion');
        $criteria->compare('id', $this->id);
        $criteria->compare('nombregerencia', $this->nombre_gerencia, true);
        $criteria->compare('descripcion', $this->descripciongerencia, true);
        $criteria->compare('direccion_id', $this->direccion_id);
        $criteria->compare('fkdireccion.nombredireccion', $this->nombre_direccion, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'attributes' => array(
                    'nombre_direccion' => array(
                        'asc' => 'fkdireccion.nombredireccion',
                        'desc' => 'fkdireccion.nombredireccion DESC',
                    ),
                    '*',
                ),
            ),
        ));
    }

    /**
     * Retorna una lista de direcciones
     */
    public static function getListDireccion() {
        return CHtml::listData(Direccion::model()->findAll(), 'id', 'nombredireccion');
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Gerencia the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
