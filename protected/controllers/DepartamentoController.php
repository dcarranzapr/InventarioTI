<?php

class DepartamentoController extends Controller {

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
                'actions' => array('index', 'view', 'create', 'listarnombredepartamento', 'update', 'admin'),
                'expression' => '!$user->isGuest && ($user->getState("tipo") == 1||$user->getState("tipo") == 2)',
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('delete'),
                'expression' => '!$user->isGuest && $user->getState("tipo") == 1',
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
        $model = new Departamento;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Departamento'])) {
            $model->attributes = $this->convertLetters($_POST['Departamento']);
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->iddepartamento));
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

        if (isset($_POST['Departamento'])) {
            $model->attributes = $_POST['Departamento'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->iddepartamento));
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
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Manages all models.
     */
    public function actionIndex() {
        $model = new Departamento('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Departamento']))
            $model->attributes = $_GET['Departamento'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function actionListarnombredepartamento() {
        if (isset($_GET['term'])) {

            $criteria = new CDbCriteria;

            $criteria->condition = "nombredepartamento like '%" . $_GET['term'] . "%'";


            $departamentos = Departamento::model()->findAll($criteria);
            $return_array = array();
            foreach ($departamentos as $departamento) {
                $return_array[] = array(
                    'label' => $departamento->nombredepartamento,
                    'value' => $departamento->nombredepartamento,
                    'id' => $departamento->iddepartamento,
                );
            }
            echo CJSON::encode($return_array);
        }
    }

    public function convertLetters($letters) {

        foreach ($letters as &$dato) {
            $dato = strtoupper($dato);
        }
        return $letters;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Departamento the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Departamento::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Departamento $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'departamento-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
