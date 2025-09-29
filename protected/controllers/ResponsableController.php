<?php

class ResponsableController extends CatalogosController {

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new Responsable;
        $model1 = new UserPrivilegioHotel;

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if (isset($_POST['Responsable'], $_POST['UserPrivilegioHotel'])) {
            if (!empty($_POST['UserPrivilegioHotel']['hotel_id'])) {
                $model->attributes = $_POST['Responsable'];
                $model1->attributes = $_POST['UserPrivilegioHotel'];
                $valid = $model->validate();
                if ($valid) {
                    $model->save(false);
                    if (isset($_POST['UserPrivilegioHotel'])) {
                        foreach ($_POST['UserPrivilegioHotel'] as $item) {
                            foreach ($item as $departamento) {
                                $model2 = new UserPrivilegioHotel;
                                $model2->hotel_id = $departamento;
                                $model2->cruge_user_iduser = $model->id_responsable;
                                $model2->save(false);
                            }
                        }
                    }
                }
                $this->redirect(array('index'));
            } else {
                if (isset($_POST['Responsable'])) {
                    $model->attributes = $_POST['Responsable'];
                    if ($model->save())
                        $this->redirect(array('index'));
                }
            }
        }else {
            if (isset($_POST['Responsable'])) {
                $model->attributes = $_POST['Responsable'];
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
        $model1 = new UserPrivilegioHotel;

        // Uncomment the following line if AJAX validation is needed
        //$this->performAjaxValidation($model);

        if (isset($_POST['Responsable'])) {


            if (empty($model3) && isset($_POST['UserPrivilegioHotel'])) {
                foreach ($_POST['UserPrivilegioHotel'] as $name) {
                    foreach ($name as $hotel) {
                        if (!($this->isExistHotel($id, $hotel))) {
                            $model2 = new UserPrivilegioHotel;
                            $model2->hotel_id = $hotel;
                            $model2->cruge_user_iduser = $model->id_responsable;
                            $model2->save(false);
                        }
                    }
                }
            } else {
                if (!isset($_POST['UserPrivilegioHotel'])) {
                    foreach ($model3 as $hotel) {
                        $model2 = $this->loadModelUserPrivilegioHotel($hotel, $id)->delete();
                    }
                } else {
                    foreach ($_POST['UserPrivilegioHotel'] as $name) {
                        $delete = array_diff($model3, $name);
                        if (empty($delete)) {
                            foreach ($name as $hotel) {
                                if (!($this->isExistHotel($id, $hotel))) {
                                    $model2 = new UserPrivilegioHotel;
                                    $model2->hotel_id = $hotel;
                                    $model2->cruge_user_iduser = $model->id_responsable;
                                    $model2->save(false);
                                }
                            }
                        } else {
                            foreach ($delete as $hotel) {
                                $model2 = $this->loadModelUserPrivilegioHotel($hotel, $id)->delete();
                            }
                        }
                    }
                }
            }
            $model->attributes = $_POST['Responsable'];
            $model->save();
            $this->redirect(array('index'));
        }

        $this->render('update', array(
            'model' => $model,
            'model1' => $model1,
        ));
    }

    public function loadModelUserPrivilegioHotel($idhotel, $user) {
        $model = UserPrivilegioHotel::model()->find('hotel_id=' . $idhotel . ' and cruge_user_iduser=' . $user);

        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function isExistHotel($user, $idhotel) {


        $hotel = UserPrivilegioHotel::model()->find('hotel_id=' . $idhotel . ' and cruge_user_iduser=' . $user);

        return !($hotel === null);
    }

    public function loadModelRelation($id_user) {
        $model = UserPrivilegioHotel::model()->findall(array('select' => 'hotel_id', 'condition' => 'cruge_user_iduser=' . $id_user));

        if (empty($model)) {
            $array = array();
            return $array;
        }
        foreach ($model as $hotel) {
            $array[] = $hotel->hotel_id;
        }
        return $array;
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Manages all models.
     */
    public function actionIndex() {
        $model = new Responsable('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Responsable']))
            $model->attributes = $_GET['Responsable'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {

        $model = Responsable::model()->with('userPrivilegioHotels')->findByPk($id);

        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'responsable-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
