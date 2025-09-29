<?php

class ReportesController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
                //'postOnly + delete',  we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'generals', 'gene', 'reporteSinAsignacion', 'reportePrestamoLog', 'reporteResguardo', 
                'reportePrestamo', 'update', 'general', 'reportesBusqueda', 'busquedas', 'resguardo', 'prestamo', 'prestamoLog', 
                "reporteHotelStatus", "hotelStatus", "prestamoResults", "prestamoLogResults", "reporteSinAsignacionResults", 
                "resguardoResults", "hotelStatusResults"),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionReportesBusqueda() {
        $model = new Equipogeneral('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Equipogeneral']))
            $model->attributes = $_GET['Equipogeneral'];



        $this->render('reportesBusqueda', array(
            'model' => $model,
        ));
    }

    public function actionReportePrestamoLog() {
        $model = new PrestamosLog('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['PrestamosLog']))
            $model->attributes = $_GET['PrestamosLog'];



        $this->render('reportePrestamoLog', array(
            'model' => $model,
        ));
    }

    public function actionReporteHotelStatus() {
        $this->render('reporteHotelStatus');
    }

    public function actionHotelStatusResults()
    {
        $dataInfo = array();
        $dataInfo['error'] = false;
        try {
            $filters = [
                "idHotel" => $_GET["idHotel"],
                "idEstatus" => $_GET["idEstatus"],
            ];

            $sql = "select 
            eg.id, h.nombreHotel as nombreHotel, 
            mar.nombremarca, m.nombremodelo,
            e.descripcion as status, te.nombreTipoEquipo as equipo,
            eg.numeroSerie as serie, ram.nombre as memoria,
            pro.nombreProcesador as procesador, tipo_disc.nombre as discoDuro,
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.usuarioNombre, aux_pres.usuarioNombre) as usuarioNombre, 
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.nombreEquipo, aux_pres.nombreEquipo) as nombreEquipo,
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.nombredepartamento, aux_pres.nombredepartamento) as departamento, 
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.gerencia, aux_pres.gerencia) as gerencia, 
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.direccion, aux_pres.direccion) as direccion
            from equipogeneral eg
            inner join hotel h on h.id = eg.idHotel
            left join estatus e on e.idEstatus = eg.idEstatus
            inner join tipoequipo te on te.idTipoEquipo = eg.idTipoEquipo
            inner join modelo m on m.idModelo = eg.idModelo
            inner join marca mar on mar.idMarca = eg.idMarca
            left join proveedores p ON p.idProveedores = eg.idProveedores
            left join sistemaoperativo so on so.idSitemaOperativo = eg.idSitemaOperativo
            left join procesadores pro on pro.idProcesadores = eg.idProcesador
            left join tipo_disco_duro tipo_disc on tipo_disc.idtipo_disco_duro = eg.idTipoDiscoDuro
            left join memoria_ram ram on ram.idmemoria_ram = eg.idMemoriaRam
            left join (
                select r.id_resguardo, eg.id,c.usuarioNombre, c.numeroColaborador, 
                res.nombre, hc.nombreHotel as HotelColaborador, dep.nombredepartamento,c.gerencia, c.direccion, r.nombreEquipo
                from equipogeneral eg
                inner join resguardo r on r.id_resguardo = eg.resguardo_idresguardo
                inner join colaborador c on c.id_usuario = r.idColaboradorEmpleado
                left join departamento dep on dep.iddepartamento = c.departamento_iddepartamento
                left join responsable res on res.id_responsable = r.capturaUser
                inner join hotel hc on hc.id = c.hotel_id
            ) aux_res on aux_res.id = eg.id
            left join (
                select pst.id as id_prestamo, eg.id, c.usuarioNombre, c.numeroColaborador, 
                res.nombre, hc.nombreHotel as HotelColaborador, dep.nombredepartamento,c.gerencia, c.direccion,
                pst.fecha_prestamo, pst.fecha_devolucion,
                pst.proroga, pfm.nombrePlataforma as Plataforma, pst.nombreEquipo
                from equipogeneral eg
                inner join prestamos pst on pst.id = eg.idPrestamo
                left join plataforma pfm on pfm.idPlataforma = pst.Plataforma_idPlataforma
                left join colaborador c on c.id_usuario = pst.idColaboradorEmpleado
                left join departamento dep on dep.iddepartamento = c.departamento_iddepartamento
                left join responsable res on res.id_responsable = pst.capturaUser
                left join hotel hc on hc.id = c.hotel_id
            ) aux_pres on aux_pres.id = eg.id";
                        
            $dataProvider = new CArrayDataProvider($this->getDataHotelStatus($sql, $filters), array(
                'id' => 'data-provider',
                'sort' => array(
                    'attributes' => array(
                        'nombreHotel' => "Hotel",
                        'nombremarca' => "Marca",
                        'nombremodelo' => "Modelo",
                        "status" => "Estatus",
                        "equipo" => "Equipo",
                        "serie" => "Serie",
                        "memoria" => "Memoria ram",
                        "procesador" => "Procesador",
                        "discoDuro" => "Disco duro",
                        "usuarioNombre" => "Colaborador",
                        "nombreEquipo" => "Nombre equipo",
                        "departamento" => "Departamento",
                        "gerencia" => "Gerencia",
                        "direccion" => "Dirección",
                    ),
                ),
                'pagination' => array('pageSize' => 25),
            ));

            Yii::app()->clientscript->scriptMap['jquery.js'] = false;
            $options = (!isset($_GET['ajax'])) ? true : false;
            
            $dataInfo['render'] = $this->renderPartial("_gridHotelStatusTabla", array('dataProvider' => $dataProvider), $options, $options);
         } catch (Exception $e) {
            $dataInfo['error'] = true;
            $dataInfo['msg'] = $e->getMessage();
        }

        echo CJSON::encode($dataInfo);
    }

    public function actionHotelStatus() {
        $data = array("Hotel", "Marca", "Modelo", "Estatus", "Equipo", "Serie",
        "Memoria", "Procesador", "Disco duro", "Colaborador",
        "Nombre equipo", "Departamento", "Gerencia", "Dirección"
        );

        $filters = [
            "idHotel" => $_GET["idHotel"],
            "idEstatus" => $_GET["idEstatus"],
        ];

        $sql = "select 
            h.nombreHotel as nombreHotel, 
            mar.nombremarca, m.nombremodelo,
            e.descripcion as status, te.nombreTipoEquipo as equipo,
            eg.numeroSerie as serie, ram.nombre as memoria,
            pro.nombreProcesador as procesador, tipo_disc.nombre as discoDuro,
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.usuarioNombre, aux_pres.usuarioNombre) as usuarioNombre, 
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.nombreEquipo, aux_pres.nombreEquipo) as nombreEquipo,
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.nombredepartamento, aux_pres.nombredepartamento) as departamento, 
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.gerencia, aux_pres.gerencia) as gerencia, 
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.direccion, aux_pres.direccion) as direccion
            from equipogeneral eg
            inner join hotel h on h.id = eg.idHotel
            left join estatus e on e.idEstatus = eg.idEstatus
            inner join tipoequipo te on te.idTipoEquipo = eg.idTipoEquipo
            inner join modelo m on m.idModelo = eg.idModelo
            inner join marca mar on mar.idMarca = eg.idMarca
            left join proveedores p ON p.idProveedores = eg.idProveedores
            left join sistemaoperativo so on so.idSitemaOperativo = eg.idSitemaOperativo
            left join procesadores pro on pro.idProcesadores = eg.idProcesador
            left join tipo_disco_duro tipo_disc on tipo_disc.idtipo_disco_duro = eg.idTipoDiscoDuro
            left join memoria_ram ram on ram.idmemoria_ram = eg.idMemoriaRam
            left join (
                select r.id_resguardo, eg.id,c.usuarioNombre, c.numeroColaborador, 
                res.nombre, hc.nombreHotel as HotelColaborador, dep.nombredepartamento,c.gerencia, c.direccion, r.nombreEquipo
                from equipogeneral eg
                inner join resguardo r on r.id_resguardo = eg.resguardo_idresguardo
                inner join colaborador c on c.id_usuario = r.idColaboradorEmpleado
                left join departamento dep on dep.iddepartamento = c.departamento_iddepartamento
                left join responsable res on res.id_responsable = r.capturaUser
                inner join hotel hc on hc.id = c.hotel_id
            ) aux_res on aux_res.id = eg.id
            left join (
                select pst.id as id_prestamo, eg.id, c.usuarioNombre, c.numeroColaborador, 
                res.nombre, hc.nombreHotel as HotelColaborador, dep.nombredepartamento,c.gerencia, c.direccion,
                pst.fecha_prestamo, pst.fecha_devolucion,
                pst.proroga, pfm.nombrePlataforma as Plataforma, pst.nombreEquipo
                from equipogeneral eg
                inner join prestamos pst on pst.id = eg.idPrestamo
                left join plataforma pfm on pfm.idPlataforma = pst.Plataforma_idPlataforma
                left join colaborador c on c.id_usuario = pst.idColaboradorEmpleado
                left join departamento dep on dep.iddepartamento = c.departamento_iddepartamento
                left join responsable res on res.id_responsable = pst.capturaUser
                left join hotel hc on hc.id = c.hotel_id
            ) aux_pres on aux_pres.id = eg.id";
                    
        
        $name = "Reporte de equipo hotel/status-".date("Y-m-d");
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$name.'.csv');
        header('Content-Description: File Transfer');
        header("Content-Transfer-Encoding: UTF-8");
        header('Pragma: no-cache');
        
        $output = fopen('php://output', 'w');
        ob_clean();
        fputcsv($output, $data);
        foreach ($this->getDataHotelStatus($sql, $filters) as $key => $value) {
            fputcsv($output, $value);
        }
        ob_flush();
        fclose($output);
        exit();
    }

    private function getDataHotelStatus($sql, $filters = [], $fetchNum = false)
    {
        if ($sql == "")
            return null;
        
        $criteria = "";

        if ($filters["idHotel"] != "") {
            $criteria .= " WHERE h.nombreHotel = :_idHotel";
        }

        if ($filters["idEstatus"] != 0) {
            $criteria .= ($criteria == "") ? " WHERE " : " AND ";
            $criteria .= "eg.idEstatus = :_idEstatus";	
        }
        
        $sql = $sql.$criteria;
        $comm = Yii::app()->db->createCommand($sql);
        
        if ($fetchNum)
            $comm->setFetchMode(PDO::FETCH_NUM);
                
        if ($filters["idHotel"] != "") {
            $comm->bindParam(":_idHotel", $filters["idHotel"], PDO::PARAM_STR);
        }

        if ($filters["idEstatus"] != 0) {
            $comm->bindParam(":_idEstatus", $filters["idEstatus"], PDO::PARAM_INT);
        }
        
        return $comm->queryAll();
    }


    public function actionIndex() {
        $model = new Equipogeneral('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Equipogeneral']))
            $model->attributes = $_GET['Equipogeneral'];

        $this->render('reporteResguardo', array(
            'model' => $model,
        ));
    }

    public function actionResguardoResults()
    {
        $dataInfo = array();
        $dataInfo['error'] = false;
        try {
            $filters = [
                "idHotel" => $_GET["idHotel"],
                "idMarca" => $_GET["idMarca"],
                "idModelo" => $_GET["idModelo"],
                "idProveedor" => $_GET["idProveedor"],
                "idSistemaOperativo" => $_GET["idSistemaOperativo"],
                "idPlataforma" => $_GET["idPlataforma"],
                "idTipo" => $_GET["idTipo"],
                "nombreColaborador" => $_GET["nombreColaborador"],
                "departamento" => $_GET["departamento"],
                "idHotelColaborador" => $_GET["idHotelColaborador"]
            ];

            $sql = "SELECT 
                t.id as id, ho.nombreHotel, ma.nombremarca, mo.nombremodelo,
                pro.nombreProveedor, tipoe.nombreTipoEquipo, sisope.nombreSistema,
                plataform.nombrePlataforma, colabora.usuarioNombre, dep.nombredepartamento,
                hotelcolaborador.nombreHotel as nombreHotelColaborador
                FROM equipogeneral t
                INNER JOIN hotel ho ON t.idHotel=ho.id
                INNER JOIN modelo mo ON t.idModelo=mo.idModelo
                INNER JOIN marca ma ON t.idMarca=ma.idMarca
                LEFT JOIN proveedores pro ON t.idProveedores=pro.idProveedores
                INNER JOIN estatus esta ON t.idEstatus=esta.idEstatus
                INNER JOIN tipoequipo tipoe ON t.idTipoEquipo=tipoe.idTipoEquipo
                LEFT JOIN sistemaoperativo sisope ON t.idSitemaOperativo=sisope.idSitemaOperativo
                INNER JOIN resguardo res ON t.resguardo_idresguardo=res.id_resguardo
                LEFT JOIN plataforma plataform ON res.Plataforma_idPlataforma=plataform.idPlataforma
                INNER JOIN colaborador colabora ON colabora.id_usuario=res.idColaboradorEmpleado
                INNER JOIN departamento dep ON dep.iddepartamento=colabora.departamento_iddepartamento
                INNER JOIN hotel hotelcolaborador ON colabora.hotel_id=hotelcolaborador.id
                WHERE t.resguardo_idresguardo IS NOT NULL";
            
            $dataProvider = new CArrayDataProvider($this->getDataResguardo($sql, $filters), array(
                'id' => 'data-provider',
                'sort' => array(
                    'attributes' => array(
                        'nombreHotel' => "Hotel",
                        'nombremarca' => "Marca",
                        'nombremodelo' => "Modelo",
                        'nombreProveedor' => "Proveedor",
                        'nombreTipoEquipo' => "Tipo",
                        'nombreSistema' => "Sistema operativo",
                        'nombrePlataforma' => "Plataforma",
                        'usuarioNombre' => "Colaborador",
                        'nombredepartamento' => "Departamento",
                        'nombre_hotel_colaborador' => "Hotel colaborador",
                    ),
                ),
                'pagination' => array('pageSize' => 25),
            ));

            Yii::app()->clientscript->scriptMap['jquery.js'] = false;
            $options = (!isset($_GET['ajax'])) ? true : false;
            
            $dataInfo['render'] = $this->renderPartial("_gridResguardoTabla", array('dataProvider' => $dataProvider), $options, $options);
         } catch (Exception $e) {
            $dataInfo['error'] = true;
            $dataInfo['msg'] = $e->getMessage();
        }

        echo CJSON::encode($dataInfo);
    }

    private function getDataResguardo($sql, $filters = [], $fetchNum = false)
    {
        if ($sql == "")
            return null;
        
        $criteria = "";

        if ($filters["idHotel"] != "") {
            $criteria .= " AND ho.nombreHotel = :_idHotel";
        }

        if ($filters["idMarca"] != 0) {
            $criteria .= " AND ma.idMarca = :_idMarca";	
        }

        if ($filters["idModelo"] != 0) {
            $criteria .= " AND mo.idModelo = :_idModelo";
        }

        if ($filters["idProveedor"] != 0) {
            $criteria .= " AND pro.idProveedores = :_idProveedor";	
        }

        if ($filters["idSistemaOperativo"] != 0) {
            $criteria .= " AND sisope.idSitemaOperativo = :_idSistemaOperativo";
        }

        if ($filters["idPlataforma"] != 0) {
            $criteria .= " AND plataform.idPlataforma = :_idPlataforma";
        }

        if ($filters["idTipo"] != 0) {
            $criteria .= " AND tipoe.idTipoEquipo = :_idTipo";
        }

        if (!empty($filters["nombreColaborador"])) {
            $filters["nombreColaborador"] = "%".$filters["nombreColaborador"]."%";
            $criteria .= " AND colabora.usuarioNombre like :_nombreColaborador";
        }
        
        if (!empty($filters["departamento"])) {
            $filters["departamento"] = "%".$filters["departamento"]."%";
            $criteria .= " AND dep.nombredepartamento like :_departamento";
        }

        if ($filters["idHotelColaborador"] != 0) {
            $criteria .= " AND hotelcolaborador.id = :_idHotelColaborador";	
        }

        $sql = $sql.$criteria;        
        $comm = Yii::app()->db->createCommand($sql);
        
        if ($fetchNum)
            $comm->setFetchMode(PDO::FETCH_NUM);
                
        if ($filters["idHotel"] != "") {
            $comm->bindParam(":_idHotel", $filters["idHotel"], PDO::PARAM_STR);
        }

        if ($filters["idMarca"] != 0) {
            $comm->bindParam(":_idMarca", $filters["idMarca"], PDO::PARAM_INT);
        }

        if ($filters["idModelo"] != 0) {
            $comm->bindParam(":_idModelo", $filters["idModelo"], PDO::PARAM_INT);
        }

        if ($filters["idProveedor"] != 0) {
            $comm->bindParam(":_idProveedor", $filters["idProveedor"], PDO::PARAM_INT);
        }

        if ($filters["idSistemaOperativo"] != 0) {
            $comm->bindParam(":_idSistemaOperativo", $filters["idSistemaOperativo"], PDO::PARAM_INT);
        }

        if ($filters["idPlataforma"] != 0) {
            $comm->bindParam(":_idPlataforma", $filters["idPlataforma"], PDO::PARAM_INT);
        }

        if ($filters["idTipo"] != 0) {
            $comm->bindParam(":_idTipo", $filters["idTipo"], PDO::PARAM_INT);
        }

        if (!empty($filters["nombreColaborador"])) {
            $comm->bindParam(":_nombreColaborador", $filters["nombreColaborador"], PDO::PARAM_STR);
        }

        if (!empty($filters["departamento"])) {
            $comm->bindParam(":_departamento", $filters["departamento"], PDO::PARAM_STR);
        }

        if ($filters["idHotelColaborador"] != 0) {
            $comm->bindParam(":_idHotelColaborador", $filters["idHotelColaborador"], PDO::PARAM_INT);
        }

        return $comm->queryAll();
    }

    public function actionGenerals() {
        $model = new Equipogeneral('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Equipogeneral']))
            $model->attributes = $_GET['Equipogeneral'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function actionReportePrestamo() {
        $model = new Equipogeneral('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Equipogeneral']))
            $model->attributes = $_GET['Equipogeneral'];
        $this->render('reportePrestamo', array(
            'model' => $model,
        ));
    }

    public function actionReporteSinAsignacion() {
        $model = new Equipogeneral('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Equipogeneral']))
            $model->attributes = $_GET['Equipogeneral'];
        $this->render('reporteSinAsignacion', array(
            'model' => $model,
        ));
    }

    public function actionReporteSinAsignacionResults()
    {
        $dataInfo = array();
        $dataInfo['error'] = false;
        try {
            $filters = [
                "idHotel" => $_GET["idHotel"],
                "idMarca" => $_GET["idMarca"],
                "idModelo" => $_GET["idModelo"],
                "idProveedor" => $_GET["idProveedor"],
                "idSistemaOperativo" => $_GET["idSistemaOperativo"],
                "idTipo" => $_GET["idTipo"],                
            ];

            $sql = "SELECT
                t.id as id, ho.nombreHotel, ma.nombremarca, mo.nombremodelo,
                pro.nombreProveedor, tipoe.nombreTipoEquipo, sisope.nombreSistema
                FROM equipogeneral t
                INNER JOIN hotel ho ON t.idHotel=ho.id
                INNER JOIN modelo mo ON t.idModelo=mo.idModelo
                INNER JOIN marca ma ON t.idMarca=ma.idMarca
                LEFT JOIN proveedores pro ON t.idProveedores=pro.idProveedores
                INNER JOIN estatus esta ON t.idEstatus=esta.idEstatus
                INNER JOIN tipoequipo tipoe ON t.idTipoEquipo=tipoe.idTipoEquipo
                LEFT JOIN sistemaoperativo sisope ON t.idSitemaOperativo=sisope.idSitemaOperativo
                INNER JOIN user_privilegio_hotel userprivilegio  ON ho.id=userprivilegio.hotel_id
                WHERE idPrestamo IS NULL and resguardo_idresguardo IS NULL and t.idHotel=userprivilegio.hotel_id and cruge_user_iduser=:_userID";

            $listData = $this->getDataSinAsignacion($sql, $filters);

            $dataProvider = new CArrayDataProvider($listData, array(
                'id' => 'data-provider',
                'sort' => array(
                    'attributes' => array(
                        'nombreHotel' => "Hotel",
                        'nombremarca' => "Marca",
                        'nombremodelo' => "Modelo",
                        'nombreProveedor' => "Proveedor",
                        'nombreTipoEquipo' => "Tipo",
                        'nombreSistema' => "Sistema operativo",                        
                    ),
                ),
                'pagination' => array('pageSize' => 25),
            ));

            Yii::app()->clientscript->scriptMap['jquery.js'] = false;
            $options = (!isset($_GET['ajax'])) ? true : false;
            
            $dataInfo['render'] = $this->renderPartial("_gridSinAsignacionTabla", array('dataProvider' => $dataProvider), $options, $options);
         } catch (Exception $e) {
            $dataInfo['error'] = true;
            $dataInfo['msg'] = $e->getMessage();
        }

        echo CJSON::encode($dataInfo);
    }

    private function getDataSinAsignacion($sql, $filters = [], $fetchNum = false)
    {
        if ($sql == "")
            return null;
        
        $criteria = "";

        if ($filters["idHotel"] != "") {
            $criteria .= " AND ho.nombreHotel = :_idHotel";
        }

        if ($filters["idMarca"] != 0) {
            $criteria .= " AND ma.idMarca = :_idMarca";	
        }

        if ($filters["idModelo"] != 0) {
            $criteria .= " AND mo.idModelo = :_idModelo";
        }

        if ($filters["idProveedor"] != 0) {
            $criteria .= " AND pro.idProveedores = :_idProveedor";	
        }

        if ($filters["idSistemaOperativo"] != 0) {
            $criteria .= " AND sisope.idSitemaOperativo = :_idSistemaOperativo";
        }

        if ($filters["idTipo"] != 0) {
            $criteria .= " AND tipoe.idTipoEquipo = :_idTipo";
        }

        $sql = $sql.$criteria;

        $id = Yii::app()->user->id;
        $comm = Yii::app()->db->createCommand($sql);
        if ($fetchNum)
            $comm->setFetchMode(PDO::FETCH_NUM);
        
        $comm->bindParam(":_userID", $id, PDO::PARAM_INT);

        if ($filters["idHotel"] != "") {
            $comm->bindParam(":_idHotel", $filters["idHotel"], PDO::PARAM_STR);
        }

        if ($filters["idMarca"] != 0) {
            $comm->bindParam(":_idMarca", $filters["idMarca"], PDO::PARAM_INT);
        }

        if ($filters["idModelo"] != 0) {
            $comm->bindParam(":_idModelo", $filters["idModelo"], PDO::PARAM_INT);
        }

        if ($filters["idProveedor"] != 0) {
            $comm->bindParam(":_idProveedor", $filters["idProveedor"], PDO::PARAM_INT);
        }

        if ($filters["idSistemaOperativo"] != 0) {
            $comm->bindParam(":_idSistemaOperativo", $filters["idSistemaOperativo"], PDO::PARAM_INT);
        }

        if ($filters["idTipo"] != 0) {
            $comm->bindParam(":_idTipo", $filters["idTipo"], PDO::PARAM_INT);
        }

        return $comm->queryAll();
    }
    /**
     * Lists all models.
     */

    /**
     * Manages all models.
     */
    public function actionBusquedas() {
        $session = new CHttpSession;
        $session->open();

        if (isset($session['equipo-filtro']))
            $listEquipo = Equipogeneral::model()->findAll($session['equipo-filtro']);
        else
            $listEquipo = Equipogeneral::model()->findAll();

        $data = array(
            array('ID', 'Nombre del modelo', 'Nombre del proveedor', 'Estado del equipo', 'Tipo',
                'Hotel', 'Marca'),
        );

        foreach ($listEquipo as $reporte) {
            if ($reporte->idProveedores === null) {
                $sinproveedor = 'No tiene proveedor';
                $registro = array(
                    $reporte->id,
                    $reporte->idModelo0->nombremodelo,
                    $sinproveedor,
                    $reporte->idEstatus0->descripcion,
                    $reporte->idTipoEquipo0->nombreTipoEquipo,
                    $reporte->idHotel0->nombreHotel,
                );
            } else {
                $registro = array(
                    $reporte->id,
                    $reporte->idModelo0->nombremodelo,
                    $reporte->idProveedores0->nombreProveedor,
                    $reporte->idEstatus0->descripcion,
                    $reporte->idTipoEquipo0->nombreTipoEquipo,
                    $reporte->idHotel0->nombreHotel,
                );
            }
            array_push($data, $registro);
        }


        $excelReport = new ExcelReport('Reportede equipos de cómputo filtrado');
        $excelReport->setHojaPrincipal('Equipos', $data);
        $excelReport->setCabeceraBold('A', 'J', true);

        $excelReport->generar();
    }

    public function actionBusquedasReporte() {
        $session = new CHttpSession;
        $session->open();

        if (isset($session['resguardo-filtro']))
            $listEquipo = Equipogeneral::model()->findAll($session['equipo-filtro']);
        else
            $listEquipo = Equipogeneral::model()->findAll();

        $data = array(
            array('ID', 'Nombre del modelo', 'Nombre del proveedor', 'Estado del equipo', 'Tipo',
                'Hotel', 'Marca'),
        );

        foreach ($listEquipo as $reporte) {
            if ($reporte->idProveedores === null) {
                $sinproveedor = 'No tiene proveedor';
                $registro = array(
                    $reporte->id,
                    $reporte->idModelo0->nombremodelo,
                    $sinproveedor,
                    $reporte->idEstatus0->descripcion,
                    $reporte->idTipoEquipo0->nombreTipoEquipo,
                    $reporte->idHotel0->nombreHotel,
                );
            } else {
                $registro = array(
                    $reporte->id,
                    $reporte->idModelo0->nombremodelo,
                    $reporte->idProveedores0->nombreProveedor,
                    $reporte->idEstatus0->descripcion,
                    $reporte->idTipoEquipo0->nombreTipoEquipo,
                    $reporte->idHotel0->nombreHotel,
                );
            }
            array_push($data, $registro);
        }


        $excelReport = new ExcelReport('Reportede equipos de cómputo filtrado');
        $excelReport->setHojaPrincipal('Equipos', $data);
        $excelReport->setCabeceraBold('A', 'J', true);

        $excelReport->generar();
    }

    public function actionResguardo() {
        $data = array('Hotel al que pertenece el equipo', 'Hotel del colaborador', 'Modelo', 'Marca',
            'Proveedor', 'Estado del equipo', 'Tipo de equipo',
            'Sistema operativo', 'Número de serie', 'Fecha de compra', 'Factura',
            'Fecha de alta en el sistema', 'Memoria RAM', 'Plataforma', 'Nombre completo del usuario',
            'Departamento', 'Gerencia', 'Dirección', 'Nombre del Ingeniero de soporte',
        );

        $filters = [
            "idHotel" => $_GET["idHotel"],
            "idMarca" => $_GET["idMarca"],
            "idModelo" => $_GET["idModelo"],
            "idProveedor" => $_GET["idProveedor"],
            "idSistemaOperativo" => $_GET["idSistemaOperativo"],
            "idPlataforma" => $_GET["idPlataforma"],
            "idTipo" => $_GET["idTipo"],
            "nombreColaborador" => $_GET["nombreColaborador"],
            "departamento" => $_GET["departamento"],
            "idHotelColaborador" => $_GET["idHotelColaborador"]
        ];

        $sql = "SELECT 
            ho.nombreHotel, hotelcolaborador.nombreHotel as nombreHotelColaborador,
            mo.nombremodelo, ma.nombremarca, pro.nombreProveedor, 
            esta.descripcion as estadoEquipo, tipoe.nombreTipoEquipo, 
            sisope.nombreSistema, t.numeroSerie, t.fechaCompra, t.factura,
            t.fechaIngreso, mr.nombre as ram,
            plataform.nombrePlataforma, colabora.usuarioNombre, dep.nombredepartamento,
            colabora.gerencia, colabora.direccion, resp.nombre as resposable
            FROM equipogeneral t
            INNER JOIN hotel ho ON t.idHotel=ho.id
            INNER JOIN modelo mo ON t.idModelo=mo.idModelo
            INNER JOIN marca ma ON t.idMarca=ma.idMarca
            LEFT JOIN proveedores pro ON t.idProveedores=pro.idProveedores
            INNER JOIN estatus esta ON t.idEstatus=esta.idEstatus
            INNER JOIN tipoequipo tipoe ON t.idTipoEquipo=tipoe.idTipoEquipo
            LEFT JOIN sistemaoperativo sisope ON t.idSitemaOperativo=sisope.idSitemaOperativo
            INNER JOIN resguardo res ON t.resguardo_idresguardo=res.id_resguardo
            INNER JOIN plataforma plataform ON res.Plataforma_idPlataforma=plataform.idPlataforma
            LEFT JOIN memoria_ram mr ON mr.idmemoria_ram = t.idMemoriaRam
            INNER JOIN colaborador colabora ON colabora.id_usuario=res.idColaboradorEmpleado
            INNER JOIN departamento dep ON dep.iddepartamento=colabora.departamento_iddepartamento
            INNER JOIN hotel hotelcolaborador ON colabora.hotel_id=hotelcolaborador.id
            LEFT JOIN responsable resp on resp.id_responsable = res.capturaUser
            WHERE t.resguardo_idresguardo IS NOT NULL";

        $name = "Reporte de equipos de cómputo de resguardo filtrado-".date("Y-m-d");
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$name.'.csv');
        header('Content-Description: File Transfer');
        header("Content-Transfer-Encoding: UTF-8");
        header('Pragma: no-cache');
        
        $output = fopen('php://output', 'w');
        ob_clean();
        fputcsv($output, $data);
        foreach ($this->getDataResguardo($sql, $filters) as $key => $value) {
            fputcsv($output, $value);
        }
        ob_flush();
        fclose($output);
        exit();
    }

    public function actionPrestamoResults()
    {
        $dataInfo = array();
        $dataInfo['error'] = false;
        try {
            $filters = [
                "idHotel" => $_GET["idHotel"],
                "idMarca" => $_GET["idMarca"],
                "idModelo" => $_GET["idModelo"],
                "idProveedor" => $_GET["idProveedor"],
                "idSistemaOperativo" => $_GET["idSistemaOperativo"],
                "idPlataforma" => $_GET["idPlataforma"],
                "idTipo" => $_GET["idTipo"],
                "nombreColaborador" => $_GET["nombreColaborador"],
                "departamento" => $_GET["departamento"],
                "idHotelColaborador" => $_GET["idHotelColaborador"],
            ];

            $sql = "SELECT 
                t.id as id, ho.nombreHotel, ma.nombremarca, mo.nombremodelo,
                pro.nombreProveedor, tipoe.nombreTipoEquipo, sisope.nombreSistema,
                plataform.nombrePlataforma, colabora.usuarioNombre, dep.nombredepartamento,
                hotelcolaborador.nombreHotel as nombreHotelColaborador
                FROM equipogeneral t 
                INNER JOIN hotel ho ON t.idHotel=ho.id
                INNER JOIN modelo mo ON t.idModelo=mo.idModelo
                INNER JOIN marca ma ON t.idMarca=ma.idMarca
                LEFT JOIN proveedores pro ON t.idProveedores=pro.idProveedores
                INNER JOIN estatus esta ON t.idEstatus=esta.idEstatus
                INNER JOIN tipoequipo tipoe ON t.idTipoEquipo=tipoe.idTipoEquipo
                LEFT JOIN sistemaoperativo sisope ON t.idSitemaOperativo=sisope.idSitemaOperativo
                LEFT JOIN prestamos pres ON t.idPrestamo=pres.id
                LEFT JOIN plataforma plataform ON pres.Plataforma_idPlataforma=plataform.idPlataforma
                INNER JOIN colaborador colabora ON colabora.id_usuario=pres.idColaboradorEmpleado
                INNER JOIN departamento dep ON dep.iddepartamento=colabora.departamento_iddepartamento
                INNER JOIN hotel hotelcolaborador ON colabora.hotel_id=hotelcolaborador.id
                INNER JOIN gerencia geren ON geren.id=dep.gerencia_id
                INNER JOIN direccion dir  ON dir.id=geren.direccion_id
                INNER JOIN user_privilegio_hotel userprivilegio ON ho.id=userprivilegio.hotel_id 
                    AND t.idHotel = userprivilegio.hotel_id
                WHERE idPrestamo IS NOT NULL AND resguardo_idresguardo IS NULL 
                AND userprivilegio.cruge_user_iduser = :_userID";

            $listData = $this->getDataPrestamo($sql, $filters);

            $dataProvider = new CArrayDataProvider($listData, array(
                'id' => 'data-provider',
                'sort' => array(
                    'attributes' => array(
                        'nombreHotel' => "Hotel",
                        'nombremarca' => "Marca",
                        'nombremodelo' => "Modelo",
                        'nombreProveedor' => "Proveedor",
                        'nombreTipoEquipo' => "Tipo",
                        'nombreSistema' => "Sistema operativo",
                        'nombrePlataforma' => "Plataforma",
                        'usuarioNombre' => "Colaborador",
                        'nombredepartamento' => "Departamento",
                        #'nombre_hotel_colaborador' => "Hotel colaborador",
                    ),
                ),
                'pagination' => array('pageSize' => 25),
            ));

            Yii::app()->clientscript->scriptMap['jquery.js'] = false;
            $options = (!isset($_GET['ajax'])) ? true : false;
            
            $dataInfo['render'] = $this->renderPartial("_gridPrestamoTabla", array('dataProvider' => $dataProvider), $options, $options);
         } catch (Exception $e) {
            $dataInfo['error'] = true;
            $dataInfo['msg'] = $e->getMessage();
        }

        echo CJSON::encode($dataInfo);
    }
    
    public function actionPrestamo() {
        $filters = [
            "idHotel" => $_GET["idHotel"],
            "idMarca" => $_GET["idMarca"],
            "idModelo" => $_GET["idModelo"],
            "idProveedor" => $_GET["idProveedor"],
            "idSistemaOperativo" => $_GET["idSistemaOperativo"],
            "idPlataforma" => $_GET["idPlataforma"],
            "idTipo" => $_GET["idTipo"],
            "nombreColaborador" => $_GET["nombreColaborador"],
            "departamento" => $_GET["departamento"],
            "idHotelColaborador" => $_GET["idHotelColaborador"],
        ];

        $sql = "SELECT 
            ho.nombreHotel, hotelcolaborador.nombreHotel as nombreHotelColaborador,
            mo.nombremodelo, ma.nombremarca, pro.nombreProveedor, e.descripcion,
            tipoe.nombreTipoEquipo, sisope.nombreSistema, t.numeroSerie, t.fechaCompra, t.factura,
            t.fechaIngreso, ram.nombre as ram, plataform.nombrePlataforma, 
            colabora.usuarioNombre, dep.nombredepartamento
            FROM equipogeneral t 
            INNER JOIN estatus e ON e.idEstatus = t.idEstatus
            INNER JOIN hotel ho ON t.idHotel=ho.id
            INNER JOIN modelo mo ON t.idModelo=mo.idModelo
            INNER JOIN marca ma ON t.idMarca=ma.idMarca
            LEFT JOIN proveedores pro ON t.idProveedores=pro.idProveedores
            LEFT JOIN memoria_ram ram ON t.idMemoriaRam=ram.idmemoria_ram
            INNER JOIN estatus esta ON t.idEstatus=esta.idEstatus
            INNER JOIN tipoequipo tipoe ON t.idTipoEquipo=tipoe.idTipoEquipo
            LEFT JOIN sistemaoperativo sisope ON t.idSitemaOperativo=sisope.idSitemaOperativo
            LEFT JOIN prestamos pres ON t.idPrestamo=pres.id
            LEFT JOIN plataforma plataform ON pres.Plataforma_idPlataforma=plataform.idPlataforma
            INNER JOIN colaborador colabora ON colabora.id_usuario=pres.idColaboradorEmpleado
            INNER JOIN departamento dep ON dep.iddepartamento=colabora.departamento_iddepartamento
            INNER JOIN hotel hotelcolaborador ON colabora.hotel_id=hotelcolaborador.id
            INNER JOIN gerencia geren ON geren.id=dep.gerencia_id
            INNER JOIN direccion dir  ON dir.id=geren.direccion_id
            INNER JOIN user_privilegio_hotel userprivilegio ON ho.id=userprivilegio.hotel_id 
                AND t.idHotel = userprivilegio.hotel_id
            WHERE idPrestamo IS NOT NULL AND resguardo_idresguardo IS NULL 
            AND userprivilegio.cruge_user_iduser = :_userID";

        $listData = $this->getDataPrestamo($sql, $filters, true);

        $data = array('Hotel al que pertenece el equipo', 'Hotel del colaborador', 'Modelo', 'Marca',
            'Proveedor', 'Estado del equipo', 'Tipo de equipo', 'Sistema operativo', 'Número de serie', 
            'Fecha de compra', 'Factura', 'Fecha de alta en el sistema', 'Memoria RAM', 'Plataforma', 
            'Nombre completo del usuario', 'Departamento'
        );

        $name = "Reporte de equipos de cómputo filtrado-".date("Y-m-d");
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$name.'.csv');
        header('Content-Description: File Transfer');
        header("Content-Transfer-Encoding: UTF-8");
        header('Pragma: no-cache');
        
        $output = fopen('php://output', 'w');
        ob_clean();
        fputcsv($output, $data);
        foreach ($listData as $key => $value) {
            fputcsv($output, $value);
        }
        ob_flush();
        fclose($output);
        exit();
    }

    private function getDataPrestamo($sql, $filters = [], $fetchNum = false)
    {
        if ($sql == "")
            return null;
        
        $criteria = "";

        if ($filters["idHotel"] != "") {
            $criteria .= " AND ho.nombreHotel = :_idHotel";
        }

        if ($filters["idMarca"] != 0) {
            $criteria .= " AND ma.idMarca = :_idMarca";	
        }

        if ($filters["idModelo"] != 0) {
            $criteria .= " AND mo.idModelo = :_idModelo";
        }

        if ($filters["idProveedor"] != 0) {
            $criteria .= " AND pro.idProveedores = :_idProveedor";	
        }

        if ($filters["idSistemaOperativo"] != 0) {
            $criteria .= " AND sisope.idSitemaOperativo = :_idSistemaOperativo";
        }

        if ($filters["idPlataforma"] != 0) {
            $criteria .= " AND plataform.idPlataforma = :_idPlataforma";
        }

        if ($filters["idTipo"] != 0) {
            $criteria .= " AND tipoe.idTipoEquipo = :_idTipo";
        }

        if (!empty($filters["nombreColaborador"])) {
            $filters["nombreColaborador"] = "%".$filters["nombreColaborador"]."%";
            $criteria .= " AND colabora.usuarioNombre like :_nombreColaborador";
        }
        
        if (!empty($filters["departamento"])) {
            $filters["departamento"] = "%".$filters["departamento"]."%";
            $criteria .= " AND dep.nombredepartamento like :_departamento";
        }

        if ($filters["idHotelColaborador"] != 0) {
            $criteria .= " AND hotelcolaborador.id = :_idHotelColaborador";	
        }

        $sql = $sql.$criteria;

        $id = Yii::app()->user->id;
        $comm = Yii::app()->db->createCommand($sql);
        if ($fetchNum)
            $comm->setFetchMode(PDO::FETCH_NUM);
        
        $comm->bindParam(":_userID", $id, PDO::PARAM_INT);

        if ($filters["idHotel"] != "") {
            $comm->bindParam(":_idHotel", $filters["idHotel"], PDO::PARAM_STR);
        }

        if ($filters["idMarca"] != 0) {
            $comm->bindParam(":_idMarca", $filters["idMarca"], PDO::PARAM_INT);
        }

        if ($filters["idModelo"] != 0) {
            $comm->bindParam(":_idModelo", $filters["idModelo"], PDO::PARAM_INT);
        }

        if ($filters["idProveedor"] != 0) {
            $comm->bindParam(":_idProveedor", $filters["idProveedor"], PDO::PARAM_INT);
        }

        if ($filters["idSistemaOperativo"] != 0) {
            $comm->bindParam(":_idSistemaOperativo", $filters["idSistemaOperativo"], PDO::PARAM_INT);
        }

        if ($filters["idPlataforma"] != 0) {
            $comm->bindParam(":_idPlataforma", $filters["idPlataforma"], PDO::PARAM_INT);
        }

        if ($filters["idTipo"] != 0) {
            $comm->bindParam(":_idTipo", $filters["idTipo"], PDO::PARAM_INT);
        }

        if (!empty($filters["nombreColaborador"])) {
            $comm->bindParam(":_nombreColaborador", $filters["nombreColaborador"], PDO::PARAM_STR);
        }

        if (!empty($filters["departamento"])) {
            $comm->bindParam(":_departamento", $filters["departamento"], PDO::PARAM_STR);
        }

        if ($filters["idHotelColaborador"] != 0) {
            $comm->bindParam(":_idHotelColaborador", $filters["idHotelColaborador"], PDO::PARAM_INT);
        }

        return $comm->queryAll();
    }

    public function actionPrestamoLogResults()
    {
        $dataInfo = array();
        $dataInfo['error'] = false;
        try {            
            $filters = [
                "idHotel" => $_GET["idHotel"],
                "idMarca" => $_GET["idMarca"],
                "idModelo" => $_GET["idModelo"],
                "idProveedor" => $_GET["idProveedor"],
                "idTipo" => $_GET["idTipo"],
                "nombreColaborador" => $_GET["nombreColaborador"],
                "estado" => $_GET["estado"],
                "fechaPrestamo" => $_GET["fechaPrestamo"],
                "fechaDevolucion" => $_GET["fechaDevolucion"],
            ];
            
            $sql = "SELECT
                t.id as id, ho.nombreHotel, ma.nombremarca, mo.nombremodelo,
                pro.nombreProveedor, tipoe.nombreTipoEquipo, colabora.usuarioNombre,
                t.estado, t.fecha_prestamo, t.fecha_devolucion
                FROM prestamos_log t
                INNER JOIN equipogeneral equipo ON t.equipo_id=equipo.id
                INNER JOIN colaborador colabora ON t.id_colaborador=colabora.id_usuario
                INNER JOIN tipoequipo tipoe ON equipo.idTipoEquipo=tipoe.idTipoEquipo
                INNER JOIN marca ma ON equipo.idMarca=ma.idMarca
                INNER JOIN modelo mo ON equipo.idModelo=mo.idModelo
                LEFT JOIN proveedores pro ON equipo.idProveedores=pro.idProveedores
                INNER JOIN hotel hotelcolaborador ON colabora.hotel_id=hotelcolaborador.id
                INNER JOIN hotel ho ON equipo.idHotel=ho.id
                INNER JOIN estatus esta ON equipo.idEstatus=esta.idEstatus
                INNER JOIN user_privilegio_hotel userprivilegio ON ho.id=userprivilegio.hotel_id
                WHERE equipo.idHotel=userprivilegio.hotel_id and cruge_user_iduser=:_userID";

            $listData = $this->getDataPrestamoLog($sql, $filters);

            $dataProvider = new CArrayDataProvider($listData, array(
                'id' => 'data-provider',
                'sort' => array(
                    'attributes' => array(
                        'nombreHotel' => "Hotel",
                        'nombremarca' => "Marca",
                        'nombremodelo' => "Modelo",
                        'nombreProveedor' => "Proveedor",
                        'nombreTipoEquipo' => "Tipo",                        
                        'usuarioNombre' => "Colaborador",
                        'estado' => "Estado",
                        'fecha_prestamo' => "Fecha préstamo",
                        'fecha_devolucion' => "Fecha devolución",                        
                    ),
                ),
                'pagination' => array('pageSize' => 25),
            ));

            Yii::app()->clientscript->scriptMap['jquery.js'] = false;
            $options = (!isset($_GET['ajax'])) ? true : false;
            
            $dataInfo['render'] = $this->renderPartial("_gridPrestamoLogTabla", array('dataProvider' => $dataProvider), $options, $options);
         } catch (Exception $e) {
            $dataInfo['error'] = true;
            $dataInfo['msg'] = $e->getMessage();
        }

        echo CJSON::encode($dataInfo);
    }

    private function getDataPrestamoLog($sql, $filters = [], $fetchNum = false)
    {
        if ($sql == "")
            return null;
        
        $criteria = "";

        if ($filters["idHotel"] != "") {
            $criteria .= " AND ho.nombreHotel = :_idHotel";
        }

        if ($filters["idMarca"] != 0) {
            $criteria .= " AND ma.idMarca = :_idMarca";	
        }

        if ($filters["idModelo"] != 0) {
            $criteria .= " AND mo.idModelo = :_idModelo";
        }

        if ($filters["idProveedor"] != 0) {
            $criteria .= " AND pro.idProveedores = :_idProveedor";	
        }

        if ($filters["idTipo"] != 0) {
            $criteria .= " AND tipoe.idTipoEquipo = :_idTipo";
        }

        if ($filters["estado"] != "") {
            $filters["estado"] = "%".$filters["estado"]."%";
            $criteria .= " AND t.estado like :_estado";
        }

        if (!empty($filters["nombreColaborador"])) {
            $filters["nombreColaborador"] = "%".$filters["nombreColaborador"]."%";
            $criteria .= " AND colabora.usuarioNombre like :_nombreColaborador";
        }

        if ($filters["fechaPrestamo"] != "") {
            $criteria .= " AND t.fecha_prestamo = :_fechaPrestamo";
        }

        if ($filters["fechaDevolucion"] != "") {
            $criteria .= " AND t.fecha_devolucion = :_fechaDevolucion";
        }
        
        $sql = $sql.$criteria;

        $id = Yii::app()->user->id;
        $comm = Yii::app()->db->createCommand($sql);
        if ($fetchNum)
            $comm->setFetchMode(PDO::FETCH_NUM);
        
        $comm->bindParam(":_userID", $id, PDO::PARAM_INT);

        if ($filters["idHotel"] != "") {
            $comm->bindParam(":_idHotel", $filters["idHotel"], PDO::PARAM_STR);
        }

        if ($filters["idMarca"] != 0) {
            $comm->bindParam(":_idMarca", $filters["idMarca"], PDO::PARAM_INT);
        }

        if ($filters["idModelo"] != 0) {
            $comm->bindParam(":_idModelo", $filters["idModelo"], PDO::PARAM_INT);
        }

        if ($filters["idProveedor"] != 0) {
            $comm->bindParam(":_idProveedor", $filters["idProveedor"], PDO::PARAM_INT);
        }

        if ($filters["idTipo"] != 0) {
            $comm->bindParam(":_idTipo", $filters["idTipo"], PDO::PARAM_INT);
        }
        
        if ($filters["estado"] != "") {
            $comm->bindParam(":_estado", $filters["estado"], PDO::PARAM_STR);
        }

        if (!empty($filters["nombreColaborador"])) {
            $comm->bindParam(":_nombreColaborador", $filters["nombreColaborador"], PDO::PARAM_STR);
        }

        if ($filters["fechaPrestamo"] != "") {
            $comm->bindParam(":_fechaPrestamo", $filters["fechaPrestamo"], PDO::PARAM_STR);
        }

        if ($filters["fechaDevolucion"] != "") {
            $comm->bindParam(":_fechaDevolucion", $filters["fechaDevolucion"], PDO::PARAM_STR);
        }

        return $comm->queryAll();
    }

    public function actionPrestamoLog() {
        $filters = [
            "idHotel" => $_GET["idHotel"],
            "idMarca" => $_GET["idMarca"],
            "idModelo" => $_GET["idModelo"],
            "idProveedor" => $_GET["idProveedor"],
            "idTipo" => $_GET["idTipo"],
            "nombreColaborador" => $_GET["nombreColaborador"],
            "estado" => $_GET["estado"],
            "fechaPrestamo" => $_GET["fechaPrestamo"],
            "fechaDevolucion" => $_GET["fechaDevolucion"],
        ];

        $sql = "SELECT
            ho.nombreHotel, hotelcolaborador.nombreHotel as hotelColaborador,
            mo.nombremodelo, ma.nombremarca, pro.nombreProveedor, t.estado,
            tipoe.nombreTipoEquipo as tipo, sisope.nombreSistema,
            equipo.numeroSerie, t.fecha_prestamo, t.fecha_devolucion,
            t.fecha_entrada_equipo, t.fecha_salida_equipo,
            colabora.usuarioNombre, res.nombre as nombreResponsable
            FROM prestamos_log t
            INNER JOIN equipogeneral equipo ON t.equipo_id=equipo.id
            INNER JOIN colaborador colabora ON t.id_colaborador=colabora.id_usuario
            INNER JOIN tipoequipo tipoe ON equipo.idTipoEquipo=tipoe.idTipoEquipo
            INNER JOIN marca ma ON equipo.idMarca=ma.idMarca
            INNER JOIN modelo mo ON equipo.idModelo=mo.idModelo
            LEFT JOIN sistemaoperativo sisope ON equipo.idSitemaOperativo=sisope.idSitemaOperativo
            LEFT JOIN proveedores pro ON equipo.idProveedores=pro.idProveedores
            INNER JOIN hotel hotelcolaborador ON colabora.hotel_id=hotelcolaborador.id
            INNER JOIN hotel ho ON equipo.idHotel=ho.id
            INNER JOIN estatus esta ON equipo.idEstatus=esta.idEstatus
            INNER JOIN user_privilegio_hotel userprivilegio  ON ho.id=userprivilegio.hotel_id
            INNER JOIN responsable res ON t.id_responsable = res.id_responsable
            WHERE equipo.idHotel=userprivilegio.hotel_id and cruge_user_iduser=:_userID";

        $listData = $this->getDataPrestamoLog($sql, $filters);
        $data = array('Hotel al que pertenece el equipo', 'Hotel del colaborador', 'Modelo', 'Marca', 
            'Proveedor', 'Estado del prestamo', 'Tipo de equipo', 'Sistema operativo',
            'Número de serie', 'fecha de prestamo', 'fecha de devolucion',
            'fecha de prestamo al usuario', 'fecha real en la que se devolvió', 'Nombre completo del usuario',
            'Nombre del Ingeniero de soporte'
        );

        $name = "Reporte de equipo en préstamo-".date("Y-m-d");
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$name.'.csv');
        header('Content-Description: File Transfer');
        header("Content-Transfer-Encoding: UTF-8");
        header('Pragma: no-cache');
        
        $output = fopen('php://output', 'w');
        ob_clean();
        fputcsv($output, $data);
        foreach ($listData as $key => $value) {
            fputcsv($output, $value);
        }
        ob_flush();
        fclose($output);
        exit();
    }

    public function actionGeneral() {
        $filters = [
            "idHotel" => $_GET["idHotel"],
            "idMarca" => $_GET["idMarca"],
            "idModelo" => $_GET["idModelo"],
            "idProveedor" => $_GET["idProveedor"],
            "idSistemaOperativo" => $_GET["idSistemaOperativo"],
            "idTipo" => $_GET["idTipo"],                
        ];

        $sql = "SELECT
            ho.nombreHotel, mo.nombremodelo, ma.nombremarca, 
            pro.nombreProveedor, esta.descripcion as estatus, tipoe.nombreTipoEquipo, sisope.nombreSistema,
            t.numeroSerie, t.fechaCompra, t.factura, t.fechaIngreso, mr.nombre as memoria
            FROM equipogeneral t
            INNER JOIN hotel ho ON t.idHotel=ho.id
            INNER JOIN modelo mo ON t.idModelo=mo.idModelo
            INNER JOIN marca ma ON t.idMarca=ma.idMarca
            LEFT JOIN proveedores pro ON t.idProveedores=pro.idProveedores
            INNER JOIN estatus esta ON t.idEstatus=esta.idEstatus
            INNER JOIN tipoequipo tipoe ON t.idTipoEquipo=tipoe.idTipoEquipo
            LEFT JOIN sistemaoperativo sisope ON t.idSitemaOperativo=sisope.idSitemaOperativo
            LEFT JOIN memoria_ram mr on mr.idmemoria_ram = t.idMemoriaRam
            INNER JOIN user_privilegio_hotel userprivilegio  ON ho.id=userprivilegio.hotel_id
            WHERE idPrestamo IS NULL and resguardo_idresguardo IS NULL and t.idHotel=userprivilegio.hotel_id and cruge_user_iduser=:_userID";

        $listData = $this->getDataSinAsignacion($sql, $filters);
        
        $data = array('Hotel al que pertenece el equipo', 'Modelo', 'Marca', 
            'Proveedor', 'Estado del equipo', 'Tipo de equipo',
            'Sistema operativo', 'Número de serie', 'Fecha de compra', 'Factura',
            'Fecha de alta en el sistema', 'Memoria RAM'
        );

        $name = "Reporte de equipos sin asignación-".date("Y-m-d");
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$name.'.csv');
        header('Content-Description: File Transfer');
        header("Content-Transfer-Encoding: UTF-8");
        header('Pragma: no-cache');
        
        $output = fopen('php://output', 'w');
        ob_clean();
        fputcsv($output, $data);
        foreach ($listData as $key => $value) {
            fputcsv($output, $value);
        }
        ob_flush();
        fclose($output);
        exit();
    }

    public function actionGene() {
        $data = array('Hotel al que pertenece el equipo', 'Hotel del colaborador', 'Modelo', 'Marca'
            , 'Proveedor', 'Estado del equipo', 'Tipo de equipo',
            'Sistema operativo', 'Plataforma', 'Número de serie', 'Fecha de compra', 'Factura',
            'Fecha de alta en el sistema', 'Memoria RAM', 'Nombre completo del usuario', '# Colaborador',
            'Nombre Equipo', 'Fecha de préstamo', 'Fecha de devolución', 'Prorroga', 'Nombre del Ingeniero de soporte'
        );

        $sql = 'select h.nombreHotel as HotelPerteneceEquipo, 
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.HotelColaborador, aux_pres.HotelColaborador) as HotelColaborador, 
            m.nombremodelo as Modelo, mar.nombremarca as Marca, p.nombreProveedor as Proveedor, e.descripcion as EstadoEquipo, 
            te.nombreTipoEquipo as TipoEquipo, so.nombreSistema as SistemaOperativo, aux_pres.Plataforma, eg.numeroSerie as NumeroSerie,
            eg.fechaCompra as FechaCompra, eg.factura as Factura, eg.fechaIngreso as FechaAltaAlSistema, eg.memoria as RAM,
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.usuarioNombre, aux_pres.usuarioNombre) as NombreUsuario, 
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.numeroColaborador, aux_pres.numeroColaborador) as numeroColaborador, 
            IF(aux_res.id_resguardo IS NOT NULL, aux_res.nombreEquipo, aux_pres.nombreEquipo) as nombreEquipo,
            IFNULL(aux_pres.fecha_prestamo,"No Aplica") as FechaPrestamo, IFNULL(aux_pres.fecha_devolucion, "No Aplica") as FechaDevolucion, 
            IFNULL(aux_pres.proroga, "No Aplica") as Prorroga, IF(aux_res.id_resguardo IS NOT NULL, aux_res.nombre, aux_pres.nombre) as nombre
            from equipogeneral eg
            inner join hotel h on h.id = eg.idHotel
            left join estatus e on e.idEstatus = eg.idEstatus
            inner join tipoequipo te on te.idTipoEquipo = eg.idTipoEquipo
            inner join modelo m on m.idModelo = eg.idModelo
            inner join marca mar on mar.idMarca = eg.idMarca
            left join proveedores p ON p.idProveedores = eg.idProveedores
            left join sistemaoperativo so on so.idSitemaOperativo = eg.idSitemaOperativo
            left join (
                select r.id_resguardo, eg.id,c.usuarioNombre, c.numeroColaborador, 
                res.nombre, hc.nombreHotel as HotelColaborador, r.nombreEquipo
                from equipogeneral eg
                inner join resguardo r on r.id_resguardo = eg.resguardo_idresguardo
                inner join colaborador c on c.id_usuario = r.idColaboradorEmpleado
                left join responsable res on res.id_responsable = r.capturaUser
                inner join hotel hc on hc.id = c.hotel_id
            ) aux_res on aux_res.id = eg.id
            left join (
                select pst.id as id_prestamo, eg.id, c.usuarioNombre, c.numeroColaborador, 
                res.nombre, hc.nombreHotel as HotelColaborador,
                pst.fecha_prestamo, pst.fecha_devolucion,
                pst.proroga, pfm.nombrePlataforma as Plataforma, pst.nombreEquipo
                from equipogeneral eg
                inner join prestamos pst on pst.id = eg.idPrestamo
                left join plataforma pfm on pfm.idPlataforma = pst.Plataforma_idPlataforma
                left join colaborador c on c.id_usuario = pst.idColaboradorEmpleado
                left join responsable res on res.id_responsable = pst.capturaUser
                left join hotel hc on hc.id = c.hotel_id
            ) aux_pres on aux_pres.id = eg.id
            ORDER by eg.id asc;';

        $db = Yii::app()->db;
        $comm = $db->createCommand($sql);
        $comm->setFetchMode(PDO::FETCH_NUM);
        
        $name = "Reporte equipo general-".date("Y-m-d");
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$name.'.csv');
        header('Content-Description: File Transfer');
        header("Content-Transfer-Encoding: UTF-8");
        header('Pragma: no-cache');
        
        $output = fopen('php://output', 'w');
        ob_clean();
        fputcsv($output, $data);
        foreach ($comm->queryAll() as $key => $value) {
            fputcsv($output, $value);
        }
        ob_flush();
        fclose($output);
        exit();
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Colaborador the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Colaborador::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Colaborador $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'colaborador-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}