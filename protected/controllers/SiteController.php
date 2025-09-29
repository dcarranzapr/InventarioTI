<?php

class SiteController extends Controller {

    public $layout = '//layouts/blank';
    public $defaultAction = 'login';

    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
                #'postOnly + delete', // we only allow deletion via POST request
        );
    }

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('login'),
                'users' => array('?'),
            ),
            array('deny',
                'actions' => array('login'),
                'users' => array('@'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
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
     * Displays the login page
     */
    public function actionLogin() {
        $model = new LoginForm;

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            if ($model->validate() && $model->login())
                $this->redirect(array('/home'));
            #$this->redirect(Yii::app()->user->returnUrl);
        }

        $this->render('login', array('model' => $model));
    }

}
