<?php

class HomeController extends Controller {

    public function filters() {
        return array(
            'accessControl',
        );
    }

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('index', 'logout', 'error', 'grafica', 'cambiarPassword', 'RecuperarEquipo', 'busquedaEquipo', 'busquedaLog'),
                'users' => array('@'),
            ),
            /* array('allow',
              'actions'=>array('grafica'),
              'expression'=>'!$user->isGuest && $user->getState("tipo") != 2',
              ), */
            array('deny',
                'users' => array('*'),
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
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

    public function busquedaLog($idequipo, $prestamo) {
        $fecha = Null;
        $prestamoLog = PrestamosLog::model()->findByAttributes(array('equipo_id' => $idequipo, 'id_prestamo' => $prestamo, 'fecha_devolucion' => $fecha));
        $prestamoLog->fecha_devolucion = new CDbExpression('NOW()');
        $prestamoLog->id_prestamo = Null;

        return $prestamoLog;
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

    /**
     * Logs out the current user and redirect to homepage.
     */
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

    private function bitacoraUsuario() {
        date_default_timezone_set("America/Cancun");
        $model = new Bitacorausuarios;
        $model->cuenta = Yii::app()->user->name;
        $model->fechahora = date("d-m-Y H:i:s");
        $model->accion = 'SALIDA';
        $model->save();
    }

    public function loadModel($id) {
        $model = Prestamos::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'cambiarPassword-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
