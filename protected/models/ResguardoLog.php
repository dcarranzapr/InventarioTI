<?php

/**
 * This is the model class for table "resguardo_log".
 *
 * The followings are the available columns in table 'resguardo_log':
 * @property integer $idresguardo_log
 * @property integer $iddepartamento
 * @property integer $idColaborador
 * @property integer $idResguardo
 * @property integer $idEquipoGeneral
 * @property string $fecha
 * @property integer $capturaUsuario
 * @property string $estatus
 *
 * The followings are the available model relations:
 * @property Resguardo $idResguardo0
 * @property Resguardo $idEquipoGeneral0
 */
class ResguardoLog extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'resguardo_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('iddepartamento, idColaborador, idResguardo, idEquipoGeneral, fecha, capturaUsuario, estatus', 'required'),
            array('idresguardo_log, iddepartamento, idColaborador, idResguardo, idEquipoGeneral, capturaUsuario', 'numerical', 'integerOnly' => true),
            array('estatus', 'length', 'max' => 45),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('idresguardo_log, iddepartamento, idColaborador, idResguardo, idEquipoGeneral, fecha, capturaUsuario, estatus', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'idResguardo0' => array(self::BELONGS_TO, 'Resguardo', 'idResguardo'),
            'idEquipoGeneral0' => array(self::BELONGS_TO, 'Resguardo', 'idEquipoGeneral'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'idresguardo_log' => 'Idresguardo Log',
            'iddepartamento' => 'Iddepartamento',
            'idColaborador' => 'Id Colaborador',
            'idResguardo' => 'Id Resguardo',
            'idEquipoGeneral' => 'Id Equipo General',
            'fecha' => 'Fecha',
            'capturaUsuario' => 'Captura Usuario',
            'estatus' => 'Estatus',
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

        $criteria->compare('idresguardo_log', $this->idresguardo_log);
        $criteria->compare('iddepartamento', $this->iddepartamento);
        $criteria->compare('idColaborador', $this->idColaborador);
        $criteria->compare('idResguardo', $this->idResguardo);
        $criteria->compare('idEquipoGeneral', $this->idEquipoGeneral);
        $criteria->compare('fecha', $this->fecha, true);
        $criteria->compare('capturaUsuario', $this->capturaUsuario);
        $criteria->compare('estatus', $this->estatus, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ResguardoLog the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
