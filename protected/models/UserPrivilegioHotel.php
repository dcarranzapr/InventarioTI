<?php

/**
 * This is the model class for table "user_privilegio_hotel".
 *
 * The followings are the available columns in table 'user_privilegio_hotel':
 * @property integer $cruge_user_iduser
 * @property integer $hotel_id
 *
 * The followings are the available model relations:
 * @property Responsable $crugeUserIduser
 * @property Hotel $hotel
 */
class UserPrivilegioHotel extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'user_privilegio_hotel';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('cruge_user_iduser, hotel_id', 'required'),
            array('cruge_user_iduser, hotel_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('cruge_user_iduser, hotel_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'crugeUserIduser' => array(self::BELONGS_TO, 'Responsable', 'cruge_user_iduser'),
            'hotel' => array(self::HAS_MANY, 'Hotel', 'hotel_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'cruge_user_iduser' => 'Usuario',
            'hotel_id' => 'Hotel',
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

        $criteria->compare('cruge_user_iduser', $this->cruge_user_iduser);
        $criteria->compare('hotel_id', $this->hotel_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return UserPrivilegioHotel the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
