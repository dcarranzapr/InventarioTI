<?php

class ResguardoController extends Controller {

    public $numcolaborador;

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
                'actions' => array('create', 'delete', 'update', 'BusquedaColaborador', 'index', 'view', 'deleteEquipo', 'buscarResguardo', 'imprimir'),
                'expression' => '!$user->isGuest',
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
        $dataProviderEquipo = new CActiveDataProvider(Equipogeneral::model(), array(
            'criteria' => array(
                'condition' => 'resguardo_idresguardo=' . $id,
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
                $dataProviderEquipo->criteria = array('condition' => 'resguardo_idresguardo=' . $dataProviderEquipo1->resguardo_idresguardo);
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

    public function actionImprimir($id) {
        $this->layout = '//layouts/blank3.php';
        $dataProviderEquipo = new CActiveDataProvider(Equipogeneral::model(), array(
            'criteria' => array(
                'condition' => 'resguardo_idresguardo=' . $id,
            ),
            'pagination' => false
        ));

        $this->render('imprimir', array(
            'model' => $this->loadModel($id),
            'dataProviderEquipo' => $dataProviderEquipo,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {


        $model = new Resguardo;


        // Uncomment the following line if AJAX validation is needed
        //$this->performAjaxValidation($model);


        if (isset($_POST['Resguardo'])) {
            $model->attributes = $this->convertLetters($_POST['Resguardo']);
            $valid = $model->validate();
            if ($valid) {
                $id_colaborador = $this->BusquedaColaborador((int) $model->numeroColaborador);
                if (empty($id_colaborador)) {
                    $this->redirect('create', array(
                        'model' => $model,
                    ));
                } else {
                    $model->idColaboradorEmpleado = $id_colaborador;
                    $id = Yii::app()->user->id;
                    $model->capturaUser = $id;
                    $model->usuarioNombre = $_POST['Resguardo']['usuarioNombre'];
                    $model->fechaCaptura = new CDbExpression('NOW()');
                    
                    $colaborador = Colaborador::model()->findByPk($id_colaborador);
                    $colaborador->gerencia = $_POST['Resguardo']['gerencia'];
                    $colaborador->direccion = $_POST['Resguardo']['direccion'];

                    if ($model->save() && $colaborador->save())
                        $this->redirect(array('view', 'id' => $model->id_resguardo));
                }
            }
            $model->usuarioNombre = $_POST['Resguardo']['usuarioNombre'];
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionDeleteEquipo($id) {



        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax'])) {

            $tipo = 'delete';
            $idResguardo = "";
            $dataProviderEquipo1 = $this->updateEquipo($id, $tipo, $idResguardo);
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('view', 'id' => $dataProviderEquipo1->resguardo_idresguardo));
        }
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $model->numeroColaborador = $model->idColaboradorEmpleado0->numeroColaborador;
        $model->usuarioNombre = $model->idColaboradorEmpleado0->usuarioNombre;
        $model->gerencia = $model->idColaboradorEmpleado0->gerencia;
        $model->direccion = $model->idColaboradorEmpleado0->direccion;
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Resguardo'])) {
            $model->attributes = $this->convertLetters($_POST['Resguardo']);
            $id_colaborador = $this->BusquedaColaborador($_POST['Resguardo']['numeroColaborador']);
            $model->idColaboradorEmpleado = $id_colaborador;
            $colaborador = Colaborador::model()->findByPk($id_colaborador);
            $colaborador->gerencia = $_POST['Resguardo']['gerencia'];
            $colaborador->direccion = $_POST['Resguardo']['direccion'];

            if ($model->save() && $colaborador->save())
                $this->redirect(array('view', 'id' => $model->id_resguardo));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function buscar($id) {
        $dataProviderEquipo = Equipogeneral::model()->findAll('resguardo_idresguardo=' . $id);

        if (empty($dataProviderEquipo)) {
            return 0;
        } else {
            return 1;
        }
    }

    public function updateEquipo($id, $tipo, $idColaborador) {
        if ($tipo === 'agregar') {
            $model = $this->loadModelEquipoGeneral($id);

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);


            $model->resguardo_idresguardo = $idColaborador;
            $model->idEstatus = '5';
            $model->save();
        } else {
            $model = $this->loadModelEquipoGeneral($id);
            $resguardo_idresguardo = $model->resguardo_idresguardo;
            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);	
            $model->resguardo_idresguardo = Null;
            $model->idEstatus = '2';
            $model->save();
            $model->resguardo_idresguardo = $resguardo_idresguardo;
        }

        return $model;
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {


        if ($this->buscar($id)) {
            throw new CHttpException(404, 'Antes de borrar el resguardo, desasignar los equipos');
        } else {
            $this->loadModel($id)->delete();
        }
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Manages all models.
     */
    public function actionIndex() {
        $model = new Resguardo('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Resguardo']))
            $model->attributes = $_GET['Resguardo'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function multiexplode($delimiters, $string) {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }

    public function actionBusquedaColaborador() {
        if (isset($_POST['Resguardo']['numeroColaborador'])) {
            $id_colaborador = $_POST['Resguardo']['numeroColaborador'];

            $data = Yii::app()->ccolaborador->buscar($id_colaborador);

            echo CJSON::encode($data);
            
        } else {
            throw new CHttpException(404, 'No ha mandado datos');
        }
    }

    public function BusquedaColaborador($numcolaborador) {

        if ($numcolaborador === '') {
            
        } else {

            $lista = Colaborador::model()->findByAttributes(
                    array('numeroColaborador' => $numcolaborador));

            if (empty($lista->id_usuario)) {
                return '';
            } else {
                $id_colaborador = $lista->id_usuario;

                return $id_colaborador;
            }
        }
    }

    public function loadModelHotelhasdepartamento($iddepartamento, $idhotel) {
        $model = Hotelhasdepartamento::model()->find('hotel_id=' . $idhotel . ' and departamento_iddepartamento=' . $iddepartamento);


        return $model;
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
     * @return Resguardo the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Resguardo::model()->with('idColaboradorEmpleado0')->findByPk($id);
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

    public function isExistDepartamento($iddepartamento, $idhotel) {


        $departamento = Hotelhasdepartamento::model()->find('hotel_id=' . $idhotel . ' and departamento_iddepartamento=' . $iddepartamento);

        return !($departamento === null);
    }

    /**
     * Performs the AJAX validation.
     * @param Resguardo $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'resguardo-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
