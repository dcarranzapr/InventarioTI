<?php

class SistemaoperativoController extends Controller {

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
                'actions' => array('index', 'view', 'create', 'update', 'delete', 'Listarnombresistema'),
                'expression' => '!$user->isGuest && ($user->getState("tipo") == 1 || $user->getState("tipo") == 2)',
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
        $model = new Sistemaoperativo;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Sistemaoperativo'])) {
            $model->attributes = $this->convertLetters($_POST['Sistemaoperativo']);
            if ($model->save())
                $this->redirect(array('index'));
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

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Sistemaoperativo'])) {
            $model->attributes = $this->convertLetters($_POST['Sistemaoperativo']);
            if ($model->save())
                $this->redirect(array('index'));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

    /**
     * Manages all models.
     */
    public function actionIndex() {
        $model = new Sistemaoperativo('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Sistemaoperativo']))
            $model->attributes = $_GET['Sistemaoperativo'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Sistemaoperativo the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Sistemaoperativo::model()->findByPk($id);
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

    public function actionListarnombresistema() {
        if (isset($_GET['term'])) {

            $criteria = new CDbCriteria;

            $criteria->condition = "nombreSistema like '%" . $_GET['term'] . "%'";


            $sistemas = Sistemaoperativo::model()->findAll($criteria);
            $return_array = array();
            foreach ($sistemas as $sistema) {
                $return_array[] = array(
                    'label' => $sistema->nombreSistema,
                    'value' => $sistema->nombreSistema,
                    'id' => $sistema->idSitemaOperativo,
                );
            }
            echo CJSON::encode($return_array);
        }
    }

    /**
     * Performs the AJAX validation.
     * @param Sistemaoperativo $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'sistemaoperativo-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
