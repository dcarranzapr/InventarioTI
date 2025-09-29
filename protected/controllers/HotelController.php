<?php

class HotelController extends Controller {

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
                'actions' => array('index', 'view', 'create', 'update', 'admin', 'ListarnombreHotel'),
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
        $model = new Hotel;
        $model1 = new Hotelhasdepartamento;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $this->performAjaxValidation($model);

        if (isset($_POST['Hotel'], $_POST['Hotelhasdepartamento'])) {
            $model->attributes = $this->convertLetters($_POST['Hotel']);
            $model1->attributes = $_POST['Hotelhasdepartamento'];
            $valid = $model->validate();

            if ($valid) {
                $model->save(false);
                if (isset($_POST['Hotelhasdepartamento'])) {
                    foreach ($_POST['Hotelhasdepartamento'] as $item) {
                        foreach ($item as $departamento) {
                            $model2 = new UserPrivilegioHotel;
                            $model2->hotel_id = $departamento;
                            $model2->cruge_user_iduser = $model->id;
                            $model2->save(false);
                        }
                    }
                }
                $this->redirect(array('index'));
            }
        } else {
            if (isset($_POST['Hotel'])) {
                $model->attributes = $_POST['Hotel'];
                if ($model->save())
                    $this->redirect(array('index'));
            }
        }

        $this->render('create', array(
            'model' => $model,
            'model1' => $model1,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $model3 = $this->loadModelRelation($id);
        $model1 = new Hotelhasdepartamento;
        if (isset($_POST['Hotel'])) {
            if (empty($model3) && isset($_POST['Hotelhasdepartamento'])) {
                foreach ($_POST['Hotelhasdepartamento'] as $name) {
                    foreach ($name as $departamento) {
                        if (!($this->isExistDepartamento($departamento, $id))) {
                            $model2 = new Hotelhasdepartamento;
                            $model2->hotel_id = $model->id;
                            $model2->departamento_iddepartamento = $departamento;
                            $model2->save(false);
                        }
                    }
                }
            } else {
                if (!isset($_POST['Hotelhasdepartamento'])) {

                    foreach ($model3 as $departamento) {
                        $model2 = $this->loadModelHotelhasdepartamento($departamento, $id)->delete();
                    }
                } else {
                    foreach ($_POST['Hotelhasdepartamento'] as $name) {
                        $delete = array_diff($model3, $name);
                        if (empty($delete)) {
                            foreach ($name as $departamento) {
                                if (!($this->isExistDepartamento($departamento, $id))) {
                                    $model2 = new Hotelhasdepartamento;
                                    $model2->hotel_id = $model->id;
                                    $model2->departamento_iddepartamento = $departamento;
                                    $model2->save(false);
                                }
                            }
                        } else {
                            foreach ($delete as $departamento) {
                                $model2 = $this->loadModelHotelhasdepartamento($departamento, $id)->delete();
                            }
                        }
                    }
                }
            }
            $model->attributes = $this->convertLetters($_POST['Hotel']);
            $model->save();
            $this->redirect(array('index'));
        }
        $this->render('update', array(
            'model' => $model,
            'model1' => $model1,
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
        $model = new Hotel('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Hotel']))
            $model->attributes = $_GET['Hotel'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function isExistDepartamento($iddepartamento, $idhotel) {


        $departamento = Hotelhasdepartamento::model()->find('hotel_id=' . $idhotel . ' and departamento_iddepartamento=' . $iddepartamento);

        return !($departamento === null);
    }

    public function loadModelHotelhasdepartamento($iddepartamento, $idhotel) {
        $model = Hotelhasdepartamento::model()->find('hotel_id=' . $idhotel . ' and departamento_iddepartamento=' . $iddepartamento);

        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function actionListarnombreHotel() {
        if (isset($_GET['term'])) {

            $criteria = new CDbCriteria;

            $criteria->condition = "nombregerencia like '%" . $_GET['term'] . "%'";


            $gerencias = Gerencia::model()->findAll($criteria);
            $return_array = array();
            foreach ($gerencias as $gerencia) {
                $return_array[] = array(
                    'label' => $gerencia->nombregerencia,
                    'value' => $gerencia->nombregerencia,
                    'id' => $gerencia->id,
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
     * @return Hotel the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Hotel::model()->with('hotelhasdepartamento')->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function loadModelRelation($id_hotel) {
        $model = Hotelhasdepartamento::model()->findall(array('select' => 'departamento_iddepartamento', 'condition' => 'hotel_id=' . $id_hotel));

        if (empty($model)) {
            $array = array();
            return $array;
        }
        foreach ($model as $departamento) {
            $array[] = $departamento->departamento_iddepartamento;
        }


        return $array;
    }

    /**
     * Performs the AJAX validation.
     * @param Hotel $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'hotel-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
