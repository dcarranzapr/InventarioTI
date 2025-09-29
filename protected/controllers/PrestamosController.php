<?php

class PrestamosController extends Controller {

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
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('admin', 'delete', 'index', 'multiexplode', 'loadModelHotelhasdepartamento', 'view', 'create', 'update', 'busquedaColaborador', 'updatePrestamo', 'deleteEquipo', 'imprimir', 'recuperarEquipo'),
                'expression' => '!$user->isGuest && ($user->getState("tipo") == 1||$user->getState("tipo") == 2||$user->getState("tipo") == 3)',
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $dataProviderEquipo = new CActiveDataProvider(Equipogeneral::model(), array(
            'criteria' => array(
                'condition' => 'idPrestamo=' . $id,
            ),
        ));
        $submodel = new Equipogeneral('search');
        $submodel->unsetAttributes(); // clear any default values
        // IMPORTANTE!!!

        if (isset($_GET['Equipogeneral'])) {
            $submodel->attributes = $_GET['Equipogeneral'];
            $this->renderPartial('_gridBusqueda', array(
                'model3' => $submodel));
            return;
        }

        if (Yii::app()->request->isAjaxRequest) {
            // el update del CGridView Productos hecho en Ajax produce un ajaxRequest sobre el mismo
            // action que lo invoco por primera vez y el argumento fue pasado mediante {data: xxx} al // momento de hacer el update al CGridView con id 'productos'
            if (isset($_GET[0])) {
                $idequipo = $_GET[0];
                $tipo = 'agregar';
                $dataProviderEquipo1 = $this->updateEquipo($idequipo, $tipo, $id);
                $resguardo = $this->loadModel($id);
                $tipolog = 'PRESTAMO';
                $this->estadolog($idequipo, $resguardo->idColaboradorEmpleado, $id, $tipolog);
                $dataProviderEquipo->criteria = array('condition' => 'idPrestamo=' . $dataProviderEquipo1->idPrestamo);
                echo CJSON::encode($dataProviderEquipo);
            } else {
                $submodel->unsetAttributes();
            }
        }


        $this->render('view', array(
            'model' => $this->loadModel($id),
            'model3' => $submodel,
            'dataProviderEquipo' => $dataProviderEquipo,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {


        $model = new Prestamos;


        // Uncomment the following line if AJAX validation is needed
        //$this->performAjaxValidation($model);


        if (isset($_POST['Prestamos'])) {
            $model->attributes = $this->convertLetters($_POST['Prestamos']);
            $id_colaborador = $this->BusquedaColaborador((int) $model->numeroColaborador);

            $model->idColaboradorEmpleado = $id_colaborador;
            $id = Yii::app()->user->id;
            $model->capturaUser = $id;

            $unixtime = CDateTimeParser::parse($model->fecha_prestamo, "dd-MM-yyyy");
            $finaldate = Yii::app()->dateformatter->format("yyyy-MM-dd", $unixtime);
            $model->fecha_prestamo = $finaldate;
            $unixtime = CDateTimeParser::parse($model->fecha_devolucion, "dd-MM-yyyy");
            $finaldate = Yii::app()->dateformatter->format("yyyy-MM-dd", $unixtime);
            $model->fecha_devolucion = $finaldate;
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $model->numeroColaborador = $model->colaboradorIdUsuario->numeroColaborador;
        $model->usuarioNombre = $model->colaboradorIdUsuario->usuarioNombre;
        if (!isset($_POST['Prestamos'])) {
            $unixtime = CDateTimeParser::parse($model->fecha_prestamo, "yyyy-MM-dd");
            $finaldate = Yii::app()->dateformatter->format("dd-MM-yyyy", $unixtime);
            $model->fecha_prestamo = $finaldate;
            $unixtime = CDateTimeParser::parse($model->fecha_devolucion, "yyyy-MM-dd");
            $finaldate = Yii::app()->dateformatter->format("dd-MM-yyyy", $unixtime);
            $model->fecha_devolucion = $finaldate;
        }
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Prestamos'])) {
            $model->attributes = $this->convertLetters($_POST['Prestamos']);
            $id_colaborador = $this->BusquedaColaborador($_POST['Prestamos']['numeroColaborador']);
            $model->idColaboradorEmpleado = $id_colaborador;
            $unixtime = CDateTimeParser::parse($_POST['Prestamos']['fecha_prestamo'], "dd-MM-yyyy");
            $finaldate = Yii::app()->dateformatter->format("yyyy-MM-dd", $unixtime);
            $model->fecha_prestamo = $finaldate;
            $unixtime = CDateTimeParser::parse($_POST['Prestamos']['fecha_devolucion'], "dd-MM-yyyy");
            $finaldate = Yii::app()->dateformatter->format("yyyy-MM-dd", $unixtime);
            $model->fecha_devolucion = $finaldate;
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function updateEquipo($id, $tipo, $idColaborador) {
        if ($tipo === 'agregar') {
            $model = $this->loadModelEquipoGeneral($id);

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);


            $model->idPrestamo = $idColaborador;
            $model->idEstatus = '3';
            $model->save();
        } else {
            $model = $this->loadModelEquipoGeneral($id);
            $idPrestamo = $model->idPrestamo;
            $log = $this->BuscarLog($idPrestamo);
            $log->fecha_salida_equipo = new CDbExpression('NOW()');
            $log->save();
            $tipolog = 'RECUPERADO';
            $this->estadolog($id, $model->idPrestamo0->colaboradorIdUsuario->id_usuario, $model->idPrestamo, $tipolog);
            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);	
            $model->idPrestamo = Null;
            $model->idEstatus = '2';
            $model->save();
            $model->idPrestamo = $idPrestamo;
        }

        return $model;
    }

    public function actionUpdatePrestamo() {
        $es = new EditableSaver('prestamos');

        $es->update();
    }

    public function loadModelHotelhasdepartamento($iddepartamento, $idhotel) {
        $model = Hotelhasdepartamento::model()->find('hotel_id=' . $idhotel . ' and departamento_iddepartamento=' . $iddepartamento);

        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function buscar($id) {
        $dataProviderEquipo = Equipogeneral::model()->findAll('idPrestamo=' . $id);

        if (empty($dataProviderEquipo)) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        if ($this->buscar($id)) {
            throw new CHttpException(404, 'Antes de borrar el préstamo, desasignar los equipos');
        } else {
            $this->loadModel($id)->delete();
        }

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function actionDeleteEquipo($id) {



        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax'])) {

            $tipo = 'delete';
            $idPrestamo = "";
            $dataProviderEquipo1 = $this->updateEquipo($id, $tipo, $idPrestamo);

            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('view', 'id' => $dataProviderEquipo1->idPrestamo));
        }
    }

    /**
     * Lists all models.
     */

    /**
     * Manages all models.
     */
    public function actionIndex() {
        $model = new Prestamos('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Prestamos']))
            $model->attributes = $_GET['Prestamos'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function BuscarLog($id) {
        $model = PrestamosLog::model()->findByAttributes(array('id_prestamo' => $id));
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function BusquedaColaborador($numcolaborador = '') {

        if ($numcolaborador === '') {
            
        } else {

            $lista = Colaborador::model()->findByAttributes(
                    array('numeroColaborador' => $numcolaborador));
            if ($lista === null)
                $id_colaborador = '';
            else
                $id_colaborador = $lista->id_usuario;

            return $id_colaborador;
        }
    }

    public function actionRecuperarEquipo($id) {

        if (!isset($_GET['ajax'])) {

            $this->busquedaEquipo($id);
            $model = $this->loadModel($id)->delete();

            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }
    }

    public function busquedaEquipo($idPrestamo) {
        $equipos = Equipogeneral::model()->findall(
                array('condition' => 'idPrestamo=' . $idPrestamo));
        foreach ($equipos as $equipo) {
            $tipolog = 'RECUPERADO';
            $this->estadolog($equipo->id, $equipo->idPrestamo0->colaboradorIdUsuario->id_usuario, $idPrestamo, $tipolog);
        }
        foreach ($equipos as $equipo) {


            $equipo->idPrestamo = Null;
            $equipo->idEstatus = '2';
            $equipo->save();
        }
    }

    public function estadolog($id_equipo, $id_colaborador, $id_prestamo, $tipo) {
        $log = new PrestamosLog;
        $prestamo = $this->loadModel($id_prestamo);
        $log->equipo_id = $id_equipo;
        $log->id_colaborador = $id_colaborador;
        $log->estado = $tipo;
        $log->id_prestamo = $id_prestamo;
        $log->fecha_prestamo = $prestamo->fecha_prestamo;
        $log->fecha_devolucion = $prestamo->fecha_devolucion;
        if ($tipo == 'PRESTAMO') {
            $log->fecha_entrada_equipo = new CDbExpression('NOW()');
            $log->fecha_salida_equipo = null;
        } else {
            $log->fecha_entrada_equipo = null;
            $log->fecha_salida_equipo = new CDbExpression('NOW()');
        }
        $log->id_responsable = Yii::app()->user->id;
        $log->save();
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Prestamos::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function loadModelEquipoGeneral($id) {
        $model = Equipogeneral::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function multiexplode($delimiters, $string) {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }

    public function actionBusquedaColaborador() {
        if (isset($_POST['Prestamos']['numeroColaborador'])) {
            $id_colaborador = $_POST['Prestamos']['numeroColaborador'];

            $data = Yii::app()->ccolaborador->buscar($id_colaborador);

            echo CJSON::encode($data);
            
        } else {
            throw new CHttpException(404, 'No ha mandado datos');
        }
    }

    public function actionImprimir($id) {
        $this->layout = '//layouts/blank3.php';
        $dataProviderEquipo = new CActiveDataProvider(Equipogeneral::model(), array(
            'criteria' => array(
                'condition' => 'idPrestamo=' . $id,
                'with' => 'idPrestamo0',
            ),
        ));

        $this->render('imprimir', array(
            'model' => $this->loadModel($id),
            'dataProviderEquipo' => $dataProviderEquipo,
        ));
    }

    public function convertLetters($letters) {

        foreach ($letters as &$dato) {
            $dato = strtoupper($dato);
        }
        return $letters;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'prestamos-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
