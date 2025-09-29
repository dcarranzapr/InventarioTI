<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
Yii::setPathOfAlias('bootstrap', dirname(__FILE__) . '/../extensions/bootstrap');
Yii::setPathOfAlias('editable', dirname(__FILE__) . '/../extensions/x-editable');

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'INVENTARIO TI',
    'language' => 'es',
    'sourceLanguage' => 'en',
    'charset' => 'utf-8',
    'theme' => 'bootstrap',
    'homeUrl' => array('home/index'),
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.extensions.*',
        'editable.*',
    ),
    'modules' => array(
        // uncomment the following to enable the Gii tool

        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '0',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            #'ipFilters'=>array('127.0.0.1','::1'),
            'generatorPaths' => array(
                'bootstrap.gii',
            ),
        ),
    ),
    // application components
    'components' => array(
        'ccolaborador'=>array('class'=>'ColaboradorComponent'),
        'user' => array(
            'allowAutoLogin' => true,
            'loginUrl' => array('site/login'),
        ),
        'session' => array(
            'autoStart' => true,
        ),
        'bootstrap' => array(
            'class' => 'bootstrap.components.Bootstrap',
        ),
        // uncomment the following to enable URLs in path-format
        'urlManager' => array(
            'showScriptName' => false,
            'urlFormat' => 'path',
            'urlSuffix' => '.html',
            'rules' => array(
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            #'<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
            ),
        ),
        'format' => array(
            'dateFormat' => 'd-m-Y'
        ),
        'editable' => array(
            'class' => 'editable.EditableConfig',
            'form' => 'bootstrap', //form style: 'bootstrap', 'jqueryui', 'plain' 
            'mode' => 'popup', //mode: 'popup' or 'inline'  
            'defaults' => array(//default settings for all editable elements
                'emptytext' => 'Click to edit'
            )
        ),
        'excel' => array(
            'class' => 'application.extensions.PHPExcel',
        ),
        'db' => array(
            'connectionString' => 'mysql:host=clever-v2-pro.cluster-cdrfidjuoewu.us-east-1.rds.amazonaws.com;dbname=inventario',
            'emulatePrepare' => true,
            'username' => 'InventarioAPP',
            'password' => 'TsFRX)ERhFHE}^8',
            'charset' => 'utf8',
        ),
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'home/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            // uncomment the following to show log messages on web pages
            /*
              array(
              'class'=>'CWebLogRoute',
              ),
             */
            ),
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
/*
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
        // 'webServiceEmpleados' => 'http://sisturws.palace-resorts.local/sisturws/index.php?r=externos/Empleados/ServiceInterface', //Pruebas
        'webServiceEmpleados' => 'http://serviciosws.palace-resorts.local/sisturws/index.php?r=externos/Empleados/ServiceInterface', //Pruebas
    ),
*/
'params' => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
        'webServiceEmpleados' => 'http://sisturws.palace-resorts.local/sisturws/index.php?r=externos/Empleados/ServiceInterface', //Pruebas
        "defaultCountry" => "MX",
        'webServicePeopleSoft' => array(
            'MX' => array(
                'url' => 'http://10.8.16.17:8005/PSIGW/PeopleSoftServiceListeningConnector/PSFT_HR',
                'user' => 'usuarioaltas',
                'password' => '*usuarioaltasws!',
            ),
            'JM' => array(
                'url' => 'http://ps-procpro-mpj:8003/PSIGW/PeopleSoftServiceListeningConnector/PSFT_HR',
                'user' => 'usuarioaltas',
                'password' => '*usuarioaltasws!',
            ),
            'PA' => array(               
                'url' => 'http://ps-apppro-hsc.heron.local:8006/PSIGW/PeopleSoftServiceListeningConnector/PSFT_HR',
                'user' => 'usuarioaltas',
                'password' => '*usuarioaltasws!',               
            ),
        )
    ),
);
