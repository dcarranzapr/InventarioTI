<?php

/**
 * This is the model class for table "equipogeneral".
 *
 * The followings are the available columns in table 'equipogeneral':
 * @property integer $id
 * @property integer $idModelo
 * @property integer $idProveedores
 * @property integer $idEstatus
 * @property integer $idTipoEquipo
 * @property integer $idHotel
 * @property integer $idMarca
 * @property integer $idTamano
 * @property integer $idSitemaOperativo
 * @property string $numeroSerie
 * @property string $fechaCompra
 * @property string $factura
 * @property string $fechaIngreso
 * @property string $memoria
 * @property string $nombrePC
 * @property integer $capturaColaboradorId
 * @property integer $idHotelCambio
 * * @property integer $resguardo_idresguardo
 *
 * @property integer $idPrestamo
 *
 * The followings are the available model relations:
 * @property Hotel $idHotelCambio0
 * @property Colaborador $capturaColaborador
 * @property Plataforma $plataformaIdPlataforma
 * @property Resguardo $resguardoIdresguardo
 * @property Prestamos $idPrestamo0
 * @property Sistemaoperativo $idSitemaOperativo0
 * @property Estatus $idEstatus0
 * @property Hotel $idHotel0
 * @property Marca $idMarca0
 * @property Modelo $idModelo0
 * @property Proveedores $idProveedores0
 * @property Tipoequipo $idTipoEquipo0
 * @property MantenimientoHasRefacciones[] $mantenimientoHasRefacciones
 * @property ProgramasHasEquipo[] $programasHasEquipos
 */
class Equipogeneral extends CActiveRecord {

    public $nombre_modelo;
    public $nombre_proveedor;
    public $nombre_estado;
    public $nombre_tipo;
    public $nombre_hotel;
    public $nombre_marca;
    public $nombre_sistema_operativo;
    public $nombre_hotel_destino;
    public $nombre_hotel_origen;
    public $nombre_CDs;
    public $nombre_plataforma;
    public $numero_de_serie;
    public $date_first; //Agregar esta variable
    public $date_last;
    public $nombre_PC;
    public $nombreCompleto;
    public $nombre_departamento;
    public $nombre_gerencia;
    public $nombre_direccion;
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
        return 'equipogeneral';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('idModelo, numeroSerie, fechaCompra, factura,idTipoEquipo', 'required'),
            array('idModelo, idProveedores, idEstatus, idTipoEquipo, idHotel, idMarca, idSitemaOperativo, , capturaColaboradorId, idHotelCambio,resguardo_idresguardo, idMemoriaRam, idProcesador, idTipoDiscoDuro', 'numerical', 'integerOnly' => true),
            array('numeroSerie, factura', 'length', 'max' => 50),
            array('numeroSerie', 'unique', 'criteria' => array(
                    'condition' => '`numeroSerie`!=:sinSerie',
                    'params' => array(
                        ':sinSerie' => 'S/N'
                    )
                ), 'message' => 'Este número de serie ya ha sido tomado. Si no tiene el número escribir "S/N"'),
            array('idHotelCambio', 'compare', 'compareAttribute' => 'idHotel', 'operator' => "!=", 'message' => 'El cambio no se puede realizar al mismo hotel favor de escoger otro'),
            array('memoria', 'length', 'max' => 15),
            array('nombrePC', 'length', 'max' => 100),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id,date_first,nombre_plataforma,apepat,apemat,hotelde,nombre_hotel_colaborador,nombre_direccion,nombre_gerencia,nombre_departamento,nombreCompleto,date_last, idModelo, nombre_hotel_destino,nombre_hotel_origen,idProveedores, idEstatus, idTipoEquipo, idHotel, idMarca, idSitemaOperativo, idTipoCD, numeroSerie, fechaCompra, factura, fechaIngreso, memoria, nombrePC, capturaColaboradorId, idHotelCambio,nombre_modelo, nombre_proveedor,nombre_estado, nombre_tipo,nombre_hotel,nombre_marca,nombre_sistema_operativo,', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'idHotelCambio0' => array(self::BELONGS_TO, 'Hotel', 'idHotelCambio'),
            'capturaColaborador' => array(self::BELONGS_TO, 'Colaborador', 'capturaColaboradorId'),
            'idSitemaOperativo0' => array(self::BELONGS_TO, 'Sistemaoperativo', 'idSitemaOperativo'),
            'idTipoCD0' => array(self::BELONGS_TO, 'Tipocd', 'idTipoCD'),
            'idEstatus0' => array(self::BELONGS_TO, 'Estatus', 'idEstatus'),
            'idHotel0' => array(self::BELONGS_TO, 'Hotel', 'idHotel'),
            'idMarca0' => array(self::BELONGS_TO, 'Marca', 'idMarca'),
            'idModelo0' => array(self::BELONGS_TO, 'Modelo', 'idModelo'),
            'idProveedores0' => array(self::BELONGS_TO, 'Proveedores', 'idProveedores'),
            'idTipoEquipo0' => array(self::BELONGS_TO, 'Tipoequipo', 'idTipoEquipo'),
            'mantenimientoHasRefacciones' => array(self::HAS_MANY, 'MantenimientoHasRefacciones', 'equipoGeneral_id'),
            'programasHasEquipos' => array(self::HAS_MANY, 'ProgramasHasEquipo', 'equipoGeneral_id'),
            'resguardoIdresguardo' => array(self::BELONGS_TO, 'Resguardo', 'resguardo_idresguardo'),
            'idPrestamo0' => array(self::BELONGS_TO, 'Prestamos', 'idPrestamo'),
            'idMemoriaRam0' => array(self::BELONGS_TO, 'MemoriaRam', 'idMemoriaRam'),
            'idProcesador0' => array(self::BELONGS_TO, 'Procesadores', 'idProcesador'),
            'idTipoDiscoDuro0' => array(self::BELONGS_TO, 'TipoDiscoDuro', 'idTipoDiscoDuro'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'idModelo' => 'Id Modelo',
            'idProveedores' => 'Id Proveedores',
            'idEstatus' => 'Id Estatus',
            'idTipoEquipo' => 'Id Tipo Equipo',
            'idHotel' => 'Id Hotel',
            'idMarca' => 'Id Marca',
            'idSitemaOperativo' => 'Id Sitema Operativo',
            'idTipoCD' => 'Id Tipo Cd',
            'numeroSerie' => 'NÚMERO DE SERIE',
            'fechaCompra' => 'FECHA DE COMPRA',
            'factura' => 'FACTURA',
            'fechaIngreso' => 'Fecha Ingreso',
            'memoria' => 'MEMORIA RAM',
            'nombrePC' => 'Nombre Pc',
            'capturaColaboradorId' => 'Captura Colaborador',
            'idHotelCambio' => 'Id Hotel Cambio',
            'nombre_tipo' => 'TIPO',
            'nombre_modelo' => 'MODELO',
            'nombre_proveedor' => 'PROVEEDOR',
            'nombre_estado' => 'ESTADO',
            'nombre_hotel' => 'HOTEL',
            'nombre_marca' => 'MARCA',
            'numero_de_serie' => 'NUMERO DE SERIE',
            'nombre_sistema_operativo' => 'SISTEMA OPERATIVO',
            'tamano' => 'TAMAÑO',
            'nombre_CDs' => 'LECTOR DE CDs',
            'nombre_hotel_colaborador' => 'Hotel del colaborador',
            'nombre_gerencia' => 'Gerencia',
            'nombre_direccion' => 'Direccion',
            'nombreCompleto' => 'Nombre',
            'apepat' => 'Apellido paterno',
            'apemat' => 'Apellido materno',
            'nombre_departamento' => 'Departamento',
            'idMemoriaRam' => 'Memoria Ram',
            'idProcesador' => 'Procesador',
            'idTipoDiscoDuro' => 'Tipo Disco Duro',
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
        # $criteria->with = array('idModelo0', 'idHotel0.userPrivilegioHotel', 'idTipoEquipo0', 'idMarca0', 'idProveedores0', 'idEstatus0');
        #$criteria->condition = 'idHotelCambio IS NULL';
        $criteria->with = array('idModelo0', 'idTipoEquipo0', 'idMarca0', 'idProveedores0', 'idEstatus0', 'idHotel0');
        $criteria->condition = 'idEstatus0.idEstatus!=6';


        $criteria->compare('idModelo0.nombremodelo', $this->nombre_modelo, true);
        $criteria->compare('idModelo', $this->idModelo);
        $criteria->compare('nombreHotel', $this->nombre_hotel, true);
        $criteria->compare('nombreTipoEquipo', $this->nombre_tipo, true);
        $criteria->compare('nombreProveedor', $this->nombre_proveedor, true);
        $criteria->compare('nombremarca', $this->nombre_marca, true);
        $criteria->compare('id', $this->id);

        $criteria->compare('idProveedores', $this->idProveedores);
        $criteria->compare('idEstatus0.idEstatus', $this->idEstatus);
        $criteria->compare('idTipoEquipo', $this->idTipoEquipo);
        $criteria->compare('idHotel', $this->idHotel);
        $criteria->compare('idMarca', $this->idMarca);

        $criteria->compare('idSitemaOperativo', $this->idSitemaOperativo);
        $criteria->compare('numeroSerie', $this->numeroSerie, true);
        $criteria->compare('fechaCompra', $this->fechaCompra, true);
        $criteria->compare('factura', $this->factura, true);
        $criteria->compare('fechaIngreso', $this->fechaIngreso, true);
        $criteria->compare('memoria', $this->memoria, true);
        $criteria->compare('nombrePC', $this->nombrePC, true);
        $criteria->compare('capturaColaboradorId', $this->capturaColaboradorId);
        $criteria->compare('idHotelCambio', $this->idHotelCambio);
        $criteria->compare('hotel_id', $this->hotelde);
        $criteria->compare('idEstatus0.descripcion', $this->nombre_estado);



        return new CActiveDataProvider($this, array(
            'keyAttribute' => 'id',
            'criteria' => $criteria,
            'sort' => array(
                'attributes' => array(
                    'nombre_modelo' => array(
                        'asc' => 'nombremodelo',
                        'desc' => 'nombremodelo DESC',
                    ),
                    'nombre_hotel' => array(
                        'asc' => 'nombreHotel',
                        'desc' => 'nombreHotel DESC',
                    ),
                    'nombre_tipo' => array(
                        'asc' => 'nombreTipoEquipo',
                        'desc' => 'nombreTipoEquipo DESC',
                    ),
                    'nombre_marca' => array(
                        'asc' => 'nombremarca',
                        'desc' => 'nombremarca DESC',
                    ),
                    'nombre_estado' => array(
                        'asc' => 'idEstatus0.descripcion',
                        'desc' => 'idEstatus0.descripcion DESC',
                    ),
                    '*',
                ),
            ),
        ));
    }

    /**
     * Reporte de equipo en resguardo
     */
    public function searchReporte() {


        // @todo Please modify the following code to remove attributes that should not be searched.
        $session = new CHttpSession;
        $criteria = new CDbCriteria;


        $criteria->join = ' JOIN hotel ho ON t.idHotel=ho.id '
                . 'JOIN modelo mo ON t.idModelo=mo.idModelo '
                . 'JOIN marca ma ON t.idMarca=ma.idMarca '
                . 'left JOIN proveedores pro ON t.idProveedores=pro.idProveedores '
                . 'JOIN estatus esta ON t.idEstatus=esta.idEstatus '
                . 'JOIN tipoequipo tipoe ON t.idTipoEquipo=tipoe.idTipoEquipo '
                . 'left JOIN sistemaoperativo sisope ON t.idSitemaOperativo=sisope.idSitemaOperativo '
                . 'left JOIN resguardo res ON t.resguardo_idresguardo=res.id_resguardo '
                . 'left JOIN plataforma plataform ON res.Plataforma_idPlataforma=plataform.idPlataforma '
                . 'inner JOIN colaborador colabora ON colabora.id_usuario=res.idColaboradorEmpleado '
                . 'inner JOIN departamento dep ON dep.iddepartamento=colabora.departamento_iddepartamento '
                . 'inner JOIN hotel hotelcolaborador ON colabora.hotel_id=hotelcolaborador.id '
                . 'inner JOIN gerencia geren ON geren.id=dep.gerencia_id '
                . 'inner JOIN direccion dir  ON dir.id=geren.direccion_id '
                . 'inner JOIN user_privilegio_hotel userprivilegio  ON ho.id=userprivilegio.hotel_id'



        ;
        $criteria->condition = 'resguardo_idresguardo IS NOT NULL and idPrestamo IS NULL and t.idHotel=userprivilegio.hotel_id';
        $criteria->compare('ho.nombreHotel', $this->nombre_hotel, true);
        $criteria->compare('mo.nombremodelo', $this->nombre_modelo, true);
        $criteria->compare('ma.nombremarca', $this->nombre_marca, true);
        $criteria->compare('pro.nombreProveedor', $this->nombre_proveedor, true);
        $criteria->compare('esta.descripcion', $this->nombre_estado, true);
        $criteria->compare('tipoe.nombreTipoEquipo', $this->nombre_tipo, true);

        $criteria->compare('sisope.nombreSistema', $this->nombre_sistema_operativo, true);
        $criteria->compare('res.nombreEquipo', $this->nombre_PC, true);
        $criteria->compare('colabora.usuarioNombre', $this->nombreCompleto, true);
        $criteria->compare('colabora.usuarioApellidoPat', $this->apepat, true);
        $criteria->compare('colabora.usuarioApellidoMat', $this->apemat, true);
        $criteria->compare('dep.nombredepartamento', $this->nombre_departamento, true);
        $criteria->compare('hotelcolaborador.nombreHotel', $this->nombre_hotel_colaborador, true);
        $criteria->compare('geren.nombregerencia', $this->nombre_gerencia, true);
        $criteria->compare('dir.nombredireccion', $this->nombre_direccion, true);
        $criteria->compare('plataform.nombrePlataforma', $this->nombre_plataforma, true);




        $criteria->compare('id', $this->id);
        $session->open();
        $session['resguardo-filtro'] = $criteria;

        return new CActiveDataProvider($this, array(
            'keyAttribute' => 'id',
            'criteria' => $criteria,
        ));
    }

    /**
     * Reporte de equipo en préstamo
     */
    public function searchReporteP() {


        // @todo Please modify the following code to remove attributes that should not be searched.
        $session = new CHttpSession;
        $criteria = new CDbCriteria;


        $criteria->join = ' JOIN hotel ho ON t.idHotel=ho.id '
                . 'JOIN modelo mo ON t.idModelo=mo.idModelo '
                . 'JOIN marca ma ON t.idMarca=ma.idMarca '
                . 'left JOIN proveedores pro ON t.idProveedores=pro.idProveedores '
                . 'JOIN estatus esta ON t.idEstatus=esta.idEstatus '
                . 'JOIN tipoequipo tipoe ON t.idTipoEquipo=tipoe.idTipoEquipo '
                . 'left JOIN sistemaoperativo sisope ON t.idSitemaOperativo=sisope.idSitemaOperativo '
                . 'left JOIN prestamos pres ON t.idPrestamo=pres.id '
                . 'left JOIN plataforma plataform ON pres.Plataforma_idPlataforma=plataform.idPlataforma '
                . 'inner JOIN colaborador colabora ON colabora.id_usuario=pres.idColaboradorEmpleado '
                . 'inner JOIN departamento dep ON dep.iddepartamento=colabora.departamento_iddepartamento '
                . 'inner JOIN hotel hotelcolaborador ON colabora.hotel_id=hotelcolaborador.id '
                . 'inner JOIN gerencia geren ON geren.id=dep.gerencia_id '
                . 'inner JOIN direccion dir  ON dir.id=geren.direccion_id '
                . 'inner JOIN user_privilegio_hotel userprivilegio  ON ho.id=userprivilegio.hotel_id'



        ;
        $criteria->condition = 'idPrestamo IS NOT NULL and resguardo_idresguardo IS NULL and t.idHotel=userprivilegio.hotel_id and cruge_user_iduser=' . Yii::app()->user->id;
        $criteria->compare('ho.nombreHotel', $this->nombre_hotel, true);
        $criteria->compare('mo.nombremodelo', $this->nombre_modelo, true);
        $criteria->compare('ma.nombremarca', $this->nombre_marca, true);
        $criteria->compare('pro.nombreProveedor', $this->nombre_proveedor, true);
        $criteria->compare('esta.descripcion', $this->nombre_estado, true);
        $criteria->compare('tipoe.nombreTipoEquipo', $this->nombre_tipo, true);

        $criteria->compare('sisope.nombreSistema', $this->nombre_sistema_operativo, true);
        $criteria->compare('pres.nombreEquipo', $this->nombre_PC, true);
        $criteria->compare('colabora.usuarioNombre', $this->nombreCompleto, true);
        $criteria->compare('colabora.usuarioApellidoPat', $this->apepat, true);
        $criteria->compare('colabora.usuarioApellidoMat', $this->apemat, true);
        $criteria->compare('dep.nombredepartamento', $this->nombre_departamento, true);
        $criteria->compare('hotelcolaborador.nombreHotel', $this->nombre_hotel_colaborador, true);
        $criteria->compare('geren.nombregerencia', $this->nombre_gerencia, true);
        $criteria->compare('dir.nombredireccion', $this->nombre_direccion, true);
        $criteria->compare('plataform.nombrePlataforma', $this->nombre_plataforma, true);




        $criteria->compare('id', $this->id);
        $session->open();
        $session['prestamo-filtro'] = $criteria;

        return new CActiveDataProvider($this, array(
            'keyAttribute' => 'id',
            'criteria' => $criteria,
        ));
    }

    public function searchCambio() {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria = new CDbCriteria;
        $criteria->with = array('idModelo0', 'idHotel0', 'idTipoEquipo0', 'idMarca0', 'idProveedores0', 'idHotel0.userPrivilegioHotel');
        $criteria->condition = 'resguardo_idresguardo IS NULL and idPrestamo IS NULL and idHotel=hotel_id and cruge_user_iduser=' . Yii::app()->user->id;

        $criteria->compare('nombremodelo', $this->nombre_modelo, true);
        $criteria->compare('nombreHotel', $this->nombre_hotel, true);
        $criteria->compare('nombreTipoEquipo', $this->nombre_tipo, true);
        $criteria->compare('nombreProveedor', $this->nombre_proveedor, true);
        $criteria->compare('nombremarca', $this->nombre_marca, true);
        $criteria->compare('id', $this->id);
        $criteria->compare('idModelo', $this->idModelo);
        $criteria->compare('idProveedores', $this->idProveedores);
        $criteria->compare('idEstatus', $this->idEstatus);
        $criteria->compare('idTipoEquipo', $this->idTipoEquipo);
        $criteria->compare('idHotel', $this->idHotel);
        $criteria->compare('idMarca', $this->idMarca);

        $criteria->compare('numeroSerie', $this->numeroSerie, true);
        $criteria->compare('factura', $this->factura, true);
        $criteria->compare('nombrePC', $this->nombrePC, true);


        return new CActiveDataProvider($this, array(
            'keyAttribute' => 'id',
            'criteria' => $criteria,
            'sort' => array(
                'attributes' => array(
                    'nombre_modelo' => array(
                        'asc' => 'nombremodelo',
                        'desc' => 'nombremodelo DESC',
                    ),
                    'nombre_hotel' => array(
                        'asc' => 'nombreHotel',
                        'desc' => 'nombreHotel DESC',
                    ),
                    'nombre_tipo' => array(
                        'asc' => 'nombreTipoEquipo',
                        'desc' => 'nombreTipoEquipo DESC',
                    ),
                    'nombre_marca' => array(
                        'asc' => 'nombremarca',
                        'desc' => 'nombremarca DESC',
                    ),
                    '*',
                ),
            ),
        ));
    }

    public function searchAutorizaciones() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;


        $criteria->select = array(
            'idHotelCambio0.nombreHotel as nombreHotelCambio',
            'idModelo0.nombremodelo as nombremodelo', 'idHotel0.nombreHotel as nombreHotel',
            'idTipoEquipo0.nombreTipoEquipo as nombreTipoEquipo', 'idMarca0.nombremarca as nombremarca',
            'numeroSerie', 'nombrePC');
        $criteria->with = array('idModelo0', 'idHotel0', 'idTipoEquipo0', 'idMarca0', 'idProveedores0', 'idHotelCambio0.userPrivilegioHotel');


        $criteria->condition = '(resguardo_idresguardo IS NULL and idPrestamo IS NULL and idHotelCambio IS NOT NULL) and (idHotelCambio0.id=hotel_id and cruge_user_iduser=' . Yii::app()->user->id . ')';


        $criteria->compare('nombremodelo', $this->nombre_modelo, true);
        $criteria->compare('idHotel0.nombreHotel', $this->nombre_hotel, true);
        $criteria->compare('nombreTipoEquipo', $this->nombre_tipo, true);
        $criteria->compare('nombremarca', $this->nombre_marca, true);
        $criteria->compare('id', $this->id);
        $criteria->compare('idModelo', $this->idModelo);
        $criteria->compare('idProveedores', $this->idProveedores);
        $criteria->compare('idEstatus', $this->idEstatus);
        $criteria->compare('idTipoEquipo', $this->idTipoEquipo);
        $criteria->compare('idHotel', $this->idHotel);
        $criteria->compare('idMarca', $this->idMarca);

        $criteria->compare('numeroSerie', $this->numeroSerie, true);
        $criteria->compare('nombrePC', $this->nombrePC, true);
        $criteria->compare('idHotelCambio', $this->idHotelCambio);
        $criteria->compare('idHotelCambio0.nombreHotel', $this->nombre_hotel_destino, true);

        return new CActiveDataProvider($this, array(
            'keyAttribute' => 'id',
            'criteria' => $criteria,
            'sort' => array(
                'attributes' => array(
                    'nombre_modelo' => array(
                        'asc' => 'nombremodelo',
                        'desc' => 'nombremodelo DESC',
                    ),
                    'nombre_hotel' => array(
                        'asc' => 'nombreHotel',
                        'desc' => 'nombreHotel DESC',
                    ),
                    'nombre_hotel_origen' => array(
                        'asc' => 'nombreHotel',
                        'desc' => 'nombreHotel DESC',
                    ),
                    'nombre_hotel_destino' => array(
                        'asc' => 'nombreHotelCambio',
                        'desc' => 'nombreHotelCambio DESC',
                    ),
                    'nombre_tipo' => array(
                        'asc' => 'nombreTipoEquipo',
                        'desc' => 'nombreTipoEquipo DESC',
                    ),
                    'nombre_marca' => array(
                        'asc' => 'nombremarca',
                        'desc' => 'nombremarca DESC',
                    ),
                    '*',
                ),
            ),
        ));
    }

    /**
     * Reporte de equipo sin asignación
     */
    public function searchReporteS() {


        // @todo Please modify the following code to remove attributes that should not be searched.
        $session = new CHttpSession;
        $criteria = new CDbCriteria;


        $criteria->join = ' JOIN hotel ho ON t.idHotel=ho.id '
                . 'JOIN modelo mo ON t.idModelo=mo.idModelo '
                . 'JOIN marca ma ON t.idMarca=ma.idMarca '
                . 'left JOIN proveedores pro ON t.idProveedores=pro.idProveedores '
                . 'JOIN estatus esta ON t.idEstatus=esta.idEstatus '
                . 'JOIN tipoequipo tipoe ON t.idTipoEquipo=tipoe.idTipoEquipo '
                . 'left JOIN sistemaoperativo sisope ON t.idSitemaOperativo=sisope.idSitemaOperativo '
                . 'inner JOIN user_privilegio_hotel userprivilegio  ON ho.id=userprivilegio.hotel_id'

        ;
        $criteria->condition = 'idPrestamo IS NULL and resguardo_idresguardo IS NULL and t.idHotel=userprivilegio.hotel_id and cruge_user_iduser=' . Yii::app()->user->id;
        $criteria->compare('ho.nombreHotel', $this->nombre_hotel, true);
        $criteria->compare('mo.nombremodelo', $this->nombre_modelo, true);
        $criteria->compare('ma.nombremarca', $this->nombre_marca, true);
        $criteria->compare('pro.nombreProveedor', $this->nombre_proveedor, true);
        $criteria->compare('esta.descripcion', $this->nombre_estado, true);
        $criteria->compare('tipoe.nombreTipoEquipo', $this->nombre_tipo, true);

        $criteria->compare('sisope.nombreSistema', $this->nombre_sistema_operativo, true);





        $criteria->compare('id', $this->id);
        $session->open();
        $session['sinasignacion-filtro'] = $criteria;

        return new CActiveDataProvider($this, array(
            'keyAttribute' => 'id',
            'criteria' => $criteria,
        ));
    }

    public function searchEquipoDesasignado() {
        $va = '2';
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria = new CDbCriteria;
        $criteria->with = array('idModelo0', 'idHotel0', 'idTipoEquipo0', 'idMarca0', 'idProveedores0', 'idHotel0.userPrivilegioHotel');
        $criteria->condition = 'idEstatus=2 and idHotel=hotel_id and cruge_user_iduser=' . Yii::app()->user->id;
        $criteria->compare('nombremodelo', $this->nombre_modelo, true);
        $criteria->compare('nombreTipoEquipo', $this->nombre_tipo, true);
        $criteria->compare('nombremarca', $this->nombre_marca, true);

        $criteria->compare('numeroSerie', $this->numeroSerie, true);



        return new CActiveDataProvider($this, array(
            'keyAttribute' => 'id',
            'criteria' => $criteria,
            'sort' => array(
                'attributes' => array(
                    'nombre_modelo' => array(
                        'asc' => 'nombremodelo',
                        'desc' => 'nombremodelo DESC',
                    ),
                    'nombre_tipo' => array(
                        'asc' => 'nombreTipoEquipo',
                        'desc' => 'nombreTipoEquipo DESC',
                    ),
                    'nombre_marca' => array(
                        'asc' => 'nombremarca',
                        'desc' => 'nombremarca DESC',
                    ),
                    '*',
                ),
            ),
        ));
    }

    public static function getListModelo() {
        return CHtml::listData(Modelo::model()->findAll(), 'idModelo', 'nombremodelo');
    }

    public static function getListProveedor() {
        return CHtml::listData(Proveedores::model()->findAll(), 'idProveedores', 'nombreProveedor');
    }

    public static function getListProveedor1() {
        return CHtml::listData(Proveedores::model()->findAll(), 'nombreProveedor', 'nombreProveedor');
    }

    public static function getListEstatus() {
        return CHtml::listData(Estatus::model()->findAll(), 'idEstatus', 'descripcion');
    }

    public static function getListTipo() {
        return CHtml::listData(Tipoequipo::model()->findAll(), 'idTipoEquipo', 'nombreTipoEquipo');
    }

    public static function getListTipo1() {
        return CHtml::listData(Tipoequipo::model()->findAll(), 'nombreTipoEquipo', 'nombreTipoEquipo');
    }

    public static function getListHotel() {
        return CHtml::listData(Hotel::model()->findAll(), 'nombreHotel', 'nombreHotel');
    }

    public static function getListHotel1() {
        return CHtml::listData(Hotel::model()->with('userPrivilegioHotel')->findAllByAttributes(array(), "cruge_user_iduser = :user", array(':user' => Yii::app()->user->id)), 'id', 'nombreHotel');
    }

    public static function getListMarca() {
        return CHtml::listData(Marca::model()->findAll(), 'idMarca', 'nombremarca');
    }

    public static function getListMarca1() {
        return CHtml::listData(Marca::model()->findAll(), 'nombremarca', 'nombremarca');
    }

    public static function getListSistema() {
        return CHtml::listData(Sistemaoperativo::model()->findAll(), 'idSitemaOperativo', 'nombreSistema');
    }

    public static function getListPlataforma() {
        return CHtml::listData(Plataforma::model()->findAll(), 'idPlataforma', 'nombrePlataforma');
    }

    public static function getListPrueba($variable = null) {
        if (isset($variable)) {
            return CHtml::listData(Modelo::model()->with('fkidTipoEquipo0', 'fkidMarca0')->findAllByAttributes(
                                    array(), "nombreTipoEquipo = :tipo", array(':tipo' => $variable)), 'fkidMarca0.nombremarca', 'fkidMarca0.nombremarca');
        } else {

            return CHtml::listData(Modelo::model()->with('fkidMarca0')->findAllByAttributes(
                                    array(), array('select' => 'marca.nombremodelo')), 'fkidMarca0.nombremarca', 'fkidMarca0.nombremarca');
        }
    }

    public static function getListEstado() {
        return CHtml::listData(Estatus::model()->findAll("idEstatus=1 or idEstatus=2"), 'idEstatus', 'descripcion');
    }

    public static function getListEstadoAllowUpdate() {
        return CHtml::listData(Estatus::model()->findAll("idEstatus!=4 and idEstatus!=6"), 'idEstatus', 'descripcion');
    }
    
    public static function getListEstado1() {
        return CHtml::listData(Estatus::model()->findAll("idEstatus!=4 and idEstatus!=6"), 'descripcion', 'descripcion');
    }

    public static function getListMemoriaRam() {
        return CHtml::listData(MemoriaRam::model()->findAll(), 'idmemoria_ram', 'nombre');
    }

    public static function getListTipoDiscoDuro() {
        return CHtml::listData(TipoDiscoDuro::model()->findAll(), 'idtipo_disco_duro', 'nombre');
    }

    public static function getListProcesador() {
        return CHtml::listData(Procesadores::model()->findAll(), 'idProcesadores', 'nombreProcesador');
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Equipogeneral the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
