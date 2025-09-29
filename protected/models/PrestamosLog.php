<?php

/**
 * This is the model class for table "prestamos_log".
 *
 * The followings are the available columns in table 'prestamos_log':
 * @property integer $id
 * @property integer $equipo_id
 * @property integer $id_colaborador
 * @property string $fecha_prestamo
 * @property string $fecha_devolucion
 * @property integer $id_prestamo
 * @property integer $id_responsable
 * @property string $fecha_entrada_equipo
 * @property string $fecha_salida_equipo
 * @property string $estado
 *
 * The followings are the available model relations:
 * @property Colaborador $idColaborador
 * @property Equipogeneral $equipo
 * @property Responsable $idResponsable
 */
class PrestamosLog extends CActiveRecord {

    public $nombre_modelo;
    public $nombre_proveedor;
    public $nombre_tipo;
    public $nombre_hotel;
    public $nombre_marca;
    public $nombre_sistema_operativo;
    public $numero_de_serie;
    public $nombreCompleto;
    public $nombre_hotel_colaborador;
    public $hotelde;
    public $apepat;
    public $apemat;
    public $vista;
    public $validausuario;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'prestamos_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('equipo_id, id_colaborador, fecha_prestamo, id_responsable', 'required'),
            array('equipo_id, id_colaborador, id_prestamo, id_responsable', 'numerical', 'integerOnly' => true),
            array('estado', 'length', 'max' => 35),
            array('fecha_devolucion, fecha_entrada_equipo, fecha_salida_equipo', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, equipo_id, id_colaborador, fecha_prestamo, fecha_devolucion, id_prestamo, id_responsable, fecha_entrada_equipo, fecha_salida_equipo, estado,nombre_modelo,nombre_proveedor,nombre_tipo,nombre_hotel,nombre_marca,nombreCompleto,nombre_hotel_colaborador,hotelde,apepat,apemat', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'idColaborador' => array(self::BELONGS_TO, 'Colaborador', 'id_colaborador'),
            'equipo' => array(self::BELONGS_TO, 'Equipogeneral', 'equipo_id'),
            'idResponsable' => array(self::BELONGS_TO, 'Responsable', 'id_responsable'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'Llave primaria',
            'equipo_id' => 'llave foranea del equipo',
            'id_colaborador' => 'Llave forÃ¡nea del colaborador que prestÃ³ el equipo',
            'fecha_prestamo' => 'Fecha préstamo',
            'fecha_devolucion' => 'Fecha prevista para devolución',
            'id_prestamo' => 'Llave forÃ¡nea de la tabla prestamo. Se vuelve null cuando se elimina el prÃ©stamo',
            'id_responsable' => 'Id Responsable',
            'fecha_entrada_equipo' => 'Fecha de captura de equipo',
            'fecha_salida_equipo' => 'Fecha de devolucion de equipo',
            'estado' => 'Estado',
            'apemat' => 'Apellido materno',
            'apepat' => 'Apellido paterno',
            'nombreCompleto' => 'Nombre',
            'nombre_tipo' => 'Tipo',
            'nombre_marca' => 'Marca',
            'nombre_modelo' => 'Modelo',
            'nombre_proveedor' => 'Proveedor',
            'nombre_hotel' => 'Hotel del equipo'
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
        $criteria->compare('equipo_id', $this->equipo_id);
        $criteria->compare('id_colaborador', $this->id_colaborador);
        $criteria->compare('fecha_prestamo', $this->fecha_prestamo, true);
        $criteria->compare('fecha_devolucion', $this->fecha_devolucion, true);
        $criteria->compare('id_prestamo', $this->id_prestamo);
        $criteria->compare('id_responsable', $this->id_responsable);
        $criteria->compare('fecha_entrada_equipo', $this->fecha_entrada_equipo, true);
        $criteria->compare('fecha_salida_equipo', $this->fecha_salida_equipo, true);
        $criteria->compare('estado', $this->estado, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function searchReporte() {


        // @todo Please modify the following code to remove attributes that should not be searched.
        $session = new CHttpSession;
        $criteria = new CDbCriteria;


        $criteria->join = ' JOIN equipogeneral equipo ON t.equipo_id=equipo.id '
                . 'JOIN colaborador colabora ON t.id_colaborador=colabora.id_usuario '
                . 'JOIN tipoequipo tipoe ON equipo.idTipoEquipo=tipoe.idTipoEquipo '
                . 'JOIN marca ma ON equipo.idMarca=ma.idMarca '
                . 'JOIN modelo mo ON equipo.idModelo=mo.idModelo '
                . 'left JOIN proveedores pro ON equipo.idProveedores=pro.idProveedores '
                . 'inner JOIN hotel hotelcolaborador ON colabora.hotel_id=hotelcolaborador.id '
                . ' JOIN hotel ho ON equipo.idHotel=ho.id '
                . 'inner JOIN user_privilegio_hotel userprivilegio  ON ho.id=userprivilegio.hotel_id'


        ;
        $criteria->condition = 'equipo.idHotel=userprivilegio.hotel_id and cruge_user_iduser=' . Yii::app()->user->id;
        $criteria->compare('fecha_prestamo', $this->fecha_prestamo, true);
        $criteria->compare('fecha_devolucion', $this->fecha_devolucion, true);
        $criteria->compare('fecha_entrada_equipo', $this->fecha_entrada_equipo, true);
        $criteria->compare('fecha_salida_equipo', $this->fecha_salida_equipo, true);

        $criteria->compare('ho.nombreHotel', $this->nombre_hotel, true);
        $criteria->compare('mo.nombremodelo', $this->nombre_modelo, true);
        $criteria->compare('ma.nombremarca', $this->nombre_marca, true);
        $criteria->compare('pro.nombreProveedor', $this->nombre_proveedor, true);
        $criteria->compare('tipoe.nombreTipoEquipo', $this->nombre_tipo, true);

        $criteria->compare('colabora.usuarioNombre', $this->nombreCompleto, true);
        $criteria->compare('colabora.usuarioApellidoPat', $this->apepat, true);
        $criteria->compare('colabora.usuarioApellidoMat', $this->apemat, true);
        $criteria->compare('hotelcolaborador.nombreHotel', $this->nombre_hotel_colaborador, true);
        $criteria->compare('estado', $this->estado, true);


        $session->open();
        $session['prestamolog-filtro'] = $criteria;

        return new CActiveDataProvider($this, array(
            'keyAttribute' => 'id',
            'criteria' => $criteria,
        ));
    }

    public static function getListTipo() {
        return CHtml::listData(Tipoequipo::model()->findAll(), 'nombreTipoEquipo', 'nombreTipoEquipo');
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PrestamosLog the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
