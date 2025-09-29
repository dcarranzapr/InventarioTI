<?php

class EliminarTablasController extends Controller {

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
                'actions' => array('index', 'view', 'drop'),
                'expression' => '!$user->isGuest && $user->getState("tipo") == 1',
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex() {


        $this->render('index');
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

    public function actionDrop() {
        $command = Yii::app()->db->createCommand();
        $command->update('equipogeneral', array(
            'idPrestamo' => new CDbExpression('NULL')
        ));
        $command->update('equipogeneral', array(
            'resguardo_idresguardo' => new CDbExpression('NULL')
        ));
        $command->update('equipogeneral', array(
            'idHotelCambio' => new CDbExpression('NULL')
        ));

        $command->delete('prestamos_log');
        $command->delete('resguardo_log');
        $command->delete('prestamos');
        $command->delete('resguardo');
        $command->delete('bitacorausuarios');
        $command->delete('equipogeneral');
        $this->redirect(array('index'));
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
