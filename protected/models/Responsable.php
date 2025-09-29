<?php

/**
 * This is the model class for table "responsable".
 *
 * The followings are the available columns in table 'responsable':
 * @property integer $id_responsable
 * @property string $usuario
 * @property string $nombre
 * @property string $password
 * @property integer $tipo
 * @property integer $acceso
 *
 * The followings are the available model relations:
 * @property Perfil $tipo0
 * @property UserPrivilegioHotel[] $userPrivilegioHotels
 */
class Responsable extends CActiveRecord {

    public $password_actual;
    public $nuevo_password;
    public $confirmar_nuevo_password;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'responsable';
    }

    public static function getListTipo() {
        return CHtml::listData(Perfil::model()->findAll(array('order' => 'nombreperfil')), 'id_auto', 'nombreperfil');
    }

    public static function getListeStatus() {
        return array(0 => 'Denegado', 1 => 'Permitido');
    }

    public function validatePassword($pass) {
        if ($pass === $this->password)
            return true;
        return false;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('usuario, nombre, password', 'required'),
            array('password_actual, nuevo_password, confirmar_nuevo_password', 'required', 'on' => 'cambiarPassword'),
            array('tipo, acceso', 'numerical', 'integerOnly' => true),
            array('usuario, nombre', 'length', 'max' => 50),
            array('password', 'length', 'max' => 100),
            array('acceso', 'safe'),
            array('usuario', 'unique', 'attributeName' => 'usuario', 'className' => 'responsable', 'allowEmpty' => false, 'message' => '{value} ya existe, ingrese otro usuario.'),
            array('password_actual', 'validatorPassword', 'on' => 'cambiarPassword'),
            array('confirmar_nuevo_password', 'compare', 'compareAttribute' => 'nuevo_password', 'on' => 'cambiarPassword', 'message' => 'Su nuevo password no coindice con la confirmación.'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id_responsable, usuario, nombre, password, tipo, acceso', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'fkTipo' => array(self::BELONGS_TO, 'Perfil', 'tipo'),
            'userPrivilegioHotels' => array(self::HAS_MANY, 'UserPrivilegioHotel', 'cruge_user_iduser'),
            'prestamoses' => array(self::HAS_MANY, 'Prestamos', 'capturaUser'),
            'prestamosLogs' => array(self::HAS_MANY, 'PrestamosLog', 'id_responsable'),
            'resguardos' => array(self::HAS_MANY, 'Resguardo', 'capturaUser'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id_responsable' => 'Id Responsable',
            'usuario' => 'Usuario',
            'nombre' => 'Nombre',
            'password' => 'Password',
            'tipo' => 'Tipo',
            'acceso' => 'Acceso',
        );
    }

    public function validatorPassword($attribute, $params) {
        if ($this->$attribute !== Yii::app()->user->getState('pass'))
            $this->addError($attribute, 'La contraseña ingresada no coincide con la actual.');
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
        $criteria->condition = 'nombre!="(VACIO)"';
        $criteria->with = array('fkTipo');

        #$criteria->compare('id_responsable',$this->id_responsable);
        $criteria->compare('usuario', $this->usuario, true);
        $criteria->compare('nombre', $this->nombre, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('tipo', $this->tipo);

        $criteria->compare('acceso', $this->acceso);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Responsable the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
