<?php

/**
 * This is the model class for table "modelo".
 *
 * The followings are the available columns in table 'modelo':
 * @property integer $idModelo
 * @property string $nombremodelo
 * @property string $descripcion
 * @property integer $fkidMarca
 * @property integer $fkidTipoEquipo
 *
 * The followings are the available model relations:
 * @property Equipogeneral[] $equipogenerals
 * @property EquipogeneralLog[] $equipogeneralLogs
 * @property Marca $fkidMarca0
 * @property Tipoequipo $fkidTipoEquipo0
 */
class Modelo extends CActiveRecord {

    public $nombre_marca;
    public $nombre_tipo;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'modelo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('fkidMarca, fkidTipoEquipo', 'required'),
            array('fkidMarca, fkidTipoEquipo', 'numerical', 'integerOnly' => true),
            array('nombremodelo', 'length', 'max' => 35),
            array('descripcion', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('idModelo, nombremodelo, descripcion, fkidMarca, fkidTipoEquipo,nombre_marca,nombre_tipo', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'equipogenerals' => array(self::HAS_MANY, 'Equipogeneral', 'idModelo'),
            'equipogeneralLogs' => array(self::HAS_MANY, 'EquipogeneralLog', 'idModelo'),
            'fkidMarca0' => array(self::BELONGS_TO, 'Marca', 'fkidMarca'),
            'fkidTipoEquipo0' => array(self::BELONGS_TO, 'Tipoequipo', 'fkidTipoEquipo'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'idModelo' => 'Id Modelo',
            'nombremodelo' => 'Modelo',
            'descripcion' => 'Especificaciones',
            'fkidMarca' => 'Marca',
            'fkidTipoEquipo' => 'Tipo',
            'nombre_marca' => 'Marca',
            'nombre_tipo' => 'Tipo',
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
        $criteria->with = array('fkidMarca0', 'fkidTipoEquipo0');

        $criteria->compare('idModelo', $this->idModelo);
        $criteria->compare('nombremodelo', $this->nombremodelo, true);
        $criteria->compare('descripcion', $this->descripcion, true);
        $criteria->compare('fkidMarca', $this->fkidMarca);
        $criteria->compare('fkidTipoEquipo', $this->fkidTipoEquipo);
        $criteria->compare('nombremarca', $this->nombre_marca);
        $criteria->compare('nombreTipoEquipo', $this->nombre_tipo);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'attributes' => array(
                    'nombre_marca' => array(
                        'asc' => 'nombremarca',
                        'desc' => 'nombremarca DESC',
                    ),
                    'nombre_tipo' => array(
                        'asc' => 'nombreTipoEquipo',
                        'desc' => 'nombreTipoEquipo DESC',
                    ),
                    '*',
                ),
            ),
        ));
    }

    public static function getListMarca() {
        return CHtml::listData(Marca::model()->findAll(), 'idMarca', 'nombremarca');
    }

    public static function getListTipo() {
        return CHtml::listData(Tipoequipo::model()->findAll(), 'idTipoEquipo', 'nombreTipoEquipo');
    }

    public static function getListMarca1() {
        return CHtml::listData(Marca::model()->findAll(), 'nombremarca', 'nombremarca');
    }

    public static function getListTipo1() {
        return CHtml::listData(Tipoequipo::model()->findAll(), 'nombreTipoEquipo', 'nombreTipoEquipo');
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Modelo the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
