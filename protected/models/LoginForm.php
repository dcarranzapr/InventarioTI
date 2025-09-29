<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel {

    public $usuario;
    public $password;
    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules() {
        return array(
            // username and password are required
            array('usuario, password', 'required', 'message' => '{attribute} esta vacío'),
            // password needs to be authenticated
            array('password', 'authenticate'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute, $params) {
        if (!$this->hasErrors()) {
            $this->_identity = new UserIdentity($this->usuario, $this->password);
            if (!$this->_identity->authenticate())
                $this->addError('password', 'Sus credenciales son inválidas.');
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login() {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = 7200;
            Yii::app()->user->login($this->_identity, $duration);
            $this->bitacoraUsuario();
            return true;
        }
        else
            return false;
    }

    private function bitacoraUsuario() {
        date_default_timezone_set("America/Cancun");
        $model = new Bitacorausuarios;
        $model->cuenta = Yii::app()->user->name;
        $model->fechahora = date("d-m-Y H:i:s");
        $model->accion = 'ENTRADA';
        $model->save();
    }

}
