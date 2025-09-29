<?php

/**
 * This is the model class for table "tipoequipo".
 *
 * The followings are the available columns in table 'tipoequipo':
 * @property integer $idTipoEquipo
 * @property string $nombreTipoEquipo
 * @property string $descripcion
 *
 * The followings are the available model relations:
 * @property Equipogeneral[] $equipogenerals
 * @property EquipogeneralLog[] $equipogeneralLogs
 */
class Tipoequipo extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'tipoequipo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(' nombreTipoEquipo', 'required'),
            array('idTipoEquipo', 'numerical', 'integerOnly' => true),
            array('nombreTipoEquipo', 'length', 'max' => 35),
            array('descripcion', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('idTipoEquipo, nombreTipoEquipo, descripcion', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'equipogenerals' => array(self::HAS_MANY, 'Equipogeneral', 'idTipoEquipo'),
            'equipogeneralLogs' => array(self::HAS_MANY, 'EquipogeneralLog', 'idTipoEquipo'),
            'fkmodelos' => array(self::HAS_MANY, 'Modelo', 'fkidTipoEquipo'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'idTipoEquipo' => 'Id Tipo Equipo',
            'nombreTipoEquipo' => 'Tipo de equipo',
            'descripcion' => 'Descripcion',
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

        $criteria->compare('idTipoEquipo', $this->idTipoEquipo);
        $criteria->compare('nombreTipoEquipo', $this->nombreTipoEquipo, true);
        $criteria->compare('descripcion', $this->descripcion, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Tipoequipo the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
