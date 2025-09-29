<?php

/**
 * This is the model class for table "plataforma".
 *
 * The followings are the available columns in table 'plataforma':
 * @property integer $idPlataforma
 * @property string $nombrePlataforma
 *
 * The followings are the available model relations:
 * @property Equipogeneral[] $equipogenerals
 * @property EquipogeneralLog[] $equipogeneralLogs
 */
class Plataforma extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'plataforma';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nombrePlataforma', 'required'),
            array('idPlataforma', 'numerical', 'integerOnly' => true),
            array('nombrePlataforma', 'length', 'max' => 40),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('idPlataforma, nombrePlataforma', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'equipogenerals' => array(self::HAS_MANY, 'Equipogeneral', 'Plataforma_idPlataforma'),
            'equipogeneralLogs' => array(self::HAS_MANY, 'EquipogeneralLog', 'Plataforma_idPlataforma'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'idPlataforma' => 'Id Plataforma',
            'nombrePlataforma' => 'Nombre Plataforma',
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

        $criteria->compare('idPlataforma', $this->idPlataforma);
        $criteria->compare('nombrePlataforma', $this->nombrePlataforma, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Plataforma the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
