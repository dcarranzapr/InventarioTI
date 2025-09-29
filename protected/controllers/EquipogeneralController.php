<?php

class EquipogeneralController extends Controller {

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
                'actions' => array('delete', 'admin', 'index', 'view', 'create', 'createMultiple', 'update', 'selectmodelo', 'cambio', 'autorizarCambio', 'getListHotel', 'updateEquipo', 'cancelarCambio', 'autorizaciones'),
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
    public function actionView($id) {

        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new Equipogeneral;

        $model->validausuario = 'valido';
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Equipogeneral'])) {
            $model->attributes = $this->convertLetters($_POST['Equipogeneral']);
            $model->idEstatus = 2;
            $model->capturaColaboradorId = Yii::app()->user->id;
            $model->numeroSerie = $this->limpiaCadena($model->numeroSerie);
            $unixtime = CDateTimeParser::parse($model->fechaCompra, "dd-MM-yyyy");
            $finaldate = Yii::app()->dateformatter->format("yyyy-MM-dd", $unixtime);
            $model->fechaCompra = $finaldate;
            if ($model->save())
                $this->redirect(array('index'));
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionCreateMultiple() {
        $model = new Equipogeneral;
        $model->validausuario = 'valido';

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Equipogeneral'])) {
            $model->attributes = $this->convertLetters($_POST['Equipogeneral']);
            $model->idEstatus = 2;
            if ($model->idTipoEquipo == 2 || $model->idTipoEquipo == 5) {
                
            } else {
                $model->idSitemaOperativo = '';
            }
            $model->numeroSerie = $this->limpiaCadena($model->numeroSerie);
            if ($model->save())
                $model->numeroSerie = '';
        }

        $this->render('createMultiple', array(
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
        $model->vista = 'update';
        $validar = $this->validarusuario($model->idHotel);
        if (!empty($validar)) {
            $model->validausuario = 'valido';
        }
        if (!isset($_POST['Equipogeneral'])) {
            $unixtime = CDateTimeParser::parse($model->fechaCompra, "yyyy-MM-dd");
            $finaldate = Yii::app()->dateformatter->format("dd-MM-yyyy", $unixtime);
            $model->fechaCompra = $finaldate;
        }
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Equipogeneral'])) {
            $validar = $this->validarusuario($model->idHotel);
            if (!empty($validar)) {

                $model->attributes = $this->convertLetters($_POST['Equipogeneral']);
                $unixtime = CDateTimeParser::parse($_POST['Equipogeneral']['fechaCompra'], "dd-MM-yyyy");
                $finaldate = Yii::app()->dateformatter->format("yyyy-MM-dd", $unixtime);
                $model->fechaCompra = $finaldate;

                $model->numeroSerie = $this->limpiaCadena($model->numeroSerie);
                if ($model->save())
                    $this->redirect(array('index'));
            }
            else {
                throw new CHttpError(1, 'No cuenta con permisos en este hotel');
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function cambioHotel($id) {
        $model = $this->loadModel($id);
        $model->idEstatus = '6';
        $model->save();
    }

    public function actionCancelarCambio($id) {
        $model = $this->loadModel($id);
        $model->idEstatus = '2';
        $model->idHotelCambio = Null;
        if ($model->save())
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('cambio'));
    }

    public function actionAutorizarCambio($id) {
        $cambio = '14';
        $model = $this->loadModel($id);
        if ($model->idHotelCambio === null) {
            
        } else {
            $cambio = $model->idHotelCambio;
            $model->idHotel = $cambio;
            $model->idEstatus = '2';
            $model->idHotelCambio = Null;
            $model->save();
        }


        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('autorizaciones'));
    }

    public function actionUpdateEquipo() {
        $es = new EditableSaver('Equipogeneral');

        $es->update();
        $this->cambioHotel($es->primaryKey);
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $equipo = $this->loadModel($id);

        if (!$equipo->idEstatus == 1 || $equipo->idEstatus == 2) {
            $validar = $this->validarusuario($equipo->idHotel);
            if (!empty($validar)) {
                $equipo->idEstatus = 1;
                $equipo->save(false);
            } else {
                throw new CHttpException(404, 'No cuenta con permisos en este hotel');
            }
        }


        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function validarusuario($idhotel) {
        $privilegio = UserPrivilegioHotel::model()->findAll('cruge_user_iduser=' . Yii::app()->user->id . ' and hotel_id=' . $idhotel);
        if ($privilegio === null) {
            throw new CHttpException(404, 'No se encuentra autorizado para esta acción');
        }
        return $privilegio;
    }

    /**
     * Manages all models.
     */
    public function actionIndex() {
        $model = new Equipogeneral('search');
        $model->unsetAttributes();  // clear any default values

        if (isset($_GET['Equipogeneral']))
            $model->attributes = $_GET['Equipogeneral'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionCambio() {
        $model = new Equipogeneral('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Equipogeneral']))
            $model->attributes = $_GET['Equipogeneral'];


        $this->render('cambio', array(
            'model' => $model,
        ));
    }

    public function actionAutorizaciones() {
        $model = new Equipogeneral('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Equipogeneral']))
            $model->attributes = $_GET['Equipogeneral'];


        $this->render('autorizaciones', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Equipogeneral the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Equipogeneral::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function loadModelUsuario($id) {
        $model = Responsable::model()->with('userPrivilegioHotels')->findByPk($id);

        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function convertLetters($letters) {

        foreach ($letters as &$dato) {
            $dato = strtoupper($dato);
        }
        return $letters;
    }

    public function actionSelectmodelo() {

        $id_marca = $_POST['Equipogeneral']['idMarca'];
        $id_tipo = $_POST['Equipogeneral']['idTipoEquipo'];

        $lista = Modelo::model()->findAll('fkidMarca = :id_marca AND fkidTipoEquipo= :id_tipo', array(':id_marca' => $id_marca, ':id_tipo' => $id_tipo));
        $lista = CHtml::listData($lista, 'idModelo', 'nombremodelo');

        echo CHtml::tag('option', array('value' => ''), 'SELECCIONE MODELO', false);

        foreach ($lista as $valor => $nombreModelo) {
            echo CHtml::tag('option', array('value' => $valor), CHtml::encode($nombreModelo), true);
        }
    }

    public function actionGetListHotel() {
        echo CJSON::encode(CHtml::listData(Hotel::model()->findAll(), 'id', 'nombreHotel'));
    }

    public function limpiaCadena($cadena) {
        return (preg_replace('[^ A-Za-z0-9_-ñÑ]', '', $cadena));
    }

    /**
     * Performs the AJAX validation.
     * @param Equipogeneral $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'equipogeneral-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
