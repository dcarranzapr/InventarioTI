<?php

/**
 * This is the model class for table "tipo_disco_duro".
 *
 * The followings are the available columns in table 'tipo_disco_duro':
 * @property integer $idtipo_disco_duro
 * @property string $nombre
 *
 * The followings are the available model relations:
 * @property Equipogeneral[] $equipogenerals
 */
class TipoDiscoDuro extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'tipo_disco_duro';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('idtipo_disco_duro', 'required'),
            array('idtipo_disco_duro', 'numerical', 'integerOnly' => true),
            array('nombre', 'length', 'max' => 100),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('idtipo_disco_duro, nombre', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'equipogenerals' => array(self::HAS_MANY, 'Equipogeneral', 'idtipo_disco_duro'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'idtipo_disco_duro' => 'Id Memoria Ram',
            'nombre' => 'nombre',
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

        $criteria->compare('idtipo_disco_duro', $this->idtipo_disco_duro);
        $criteria->compare('nombre', $this->nombre, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return tipo_disco_duro the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
