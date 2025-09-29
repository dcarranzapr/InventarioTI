<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of principal
 *
 * @author christian
 */
class PrincipalController extends Controller {

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
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'RecuperarEquipo'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex() {
        $model = new Prestamos('search');
        $model->unsetAttributes();
        if (isset($_GET['Prestamos']))
            $model->attributes = $_GET['Prestamos'];


        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function actionRecuperarEquipo($id) {

        if (!isset($_GET['ajax'])) {

            $this->busquedaEquipo($id);
            $model = $this->loadModel($id)->delete();

            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }
    }

    private function bitacoraUsuario() {
        date_default_timezone_set("America/Cancun");
        $model = new Bitacorausuarios;
        $model->cuenta = Yii::app()->user->name;
        $model->fechahora = date("d-m-Y H:i:s");
        $model->accion = 'SALIDA';
        $model->save();
    }

    public function actionLogout() {
        $this->bitacoraUsuario();
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionCambiarPassword() {
        $id = Yii::app()->user->id;
        $model = Responsable::model()->findByPk($id);
        $model->scenario = 'cambiarPassword';

        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');

        $this->performAjaxValidation($model);

        if (isset($_POST['Responsable'])) {
            $model->attributes = $_POST['Responsable'];
            $model->password = $model->nuevo_password;
            if ($model->save())
                $this->redirect(array('index'));
        }

        $this->render('cambiarPassword', array(
            'model' => $model,
        ));
    }

    public function busquedaEquipo($idPrestamo) {

        $equipos = Equipogeneral::model()->findall(
                array('condition' => 'idPrestamo=' . $idPrestamo));


        foreach ($equipos as $equipo) {

            $this->busquedaLog($equipo->id, $equipo->idPrestamo)->save();
        }

        foreach ($equipos as $equipo) {



            $equipo->idPrestamo = Null;
            $equipo->idEstatus = '2';
            $equipo->save();
        }
    }

    public function busquedaLog($idequipo, $prestamo) {
        $fecha = Null;
        $prestamoLog = PrestamosLog::model()->findByAttributes(array('equipo_id' => $idequipo, 'id_prestamo' => $prestamo, 'fecha_devolucion' => $fecha));
        $prestamoLog->fecha_devolucion = new CDbExpression('NOW()');
        $prestamoLog->id_prestamo = Null;

        return $prestamoLog;
    }

    public function loadModel($id) {
        $model = Prestamos::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

}
