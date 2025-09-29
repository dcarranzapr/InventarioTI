<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">

        <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />

        <title><?php echo CHtml::encode($this->pageTitle); ?></title>

        <?php Yii::app()->bootstrap->register(); ?>
    </head>

    <body>

        <?php
        $this->widget('bootstrap.widgets.TbNavbar', array(
            'type' => 'inverse',
            'brandUrl' => array('/home/index'),
            'collapse' => true,
            'items' => array(
                array(
                    'class' => 'bootstrap.widgets.TbMenu',
                    'encodeLabel' => false,
                    'items' => array(
                           array('label' => 'Inventario', 'url' => array('#'), 'items' => array(
                                array('label' => 'Captura de inventario', 'url' => array('/equipogeneral'), 'visible' =>!Yii::app()->user->isGuest),
                                array('label' => 'Tipo', 'url' => array('/tipoequipo'), 'visible' => Yii::app()->user->getState('tipo') == 1 || Yii::app()->user->getState('tipo') == 2),
                                array('label' => 'Plataforma', 'url' => array('/plataforma'),'visible' => Yii::app()->user->getState('tipo') == 1|| Yii::app()->user->getState('tipo') == 2 ),
                                array('label' => 'Sitema Operativo', 'url' => array('/sistemaoperativo'), 'visible' => Yii::app()->user->getState('tipo') == 1 || Yii::app()->user->getState('tipo') == 2),
                                array('label' => 'Modelo', 'url' => array('/modelo'), 'visible' => Yii::app()->user->getState('tipo') == 1|| Yii::app()->user->getState('tipo') == 2),
                                array('label' => 'Marca', 'url' => array('/marca'), 'visible' => Yii::app()->user->getState('tipo') == 1|| Yii::app()->user->getState('tipo') == 2),
                                array('label' => 'Procesadores', 'url' => array('/procesadores'),'visible' => Yii::app()->user->getState('tipo') == 1 || Yii::app()->user->getState('tipo') == 2),
                                array('label' => 'Proveedores', 'url' => array('/proveedores'),'visible' => Yii::app()->user->getState('tipo') == 1 || Yii::app()->user->getState('tipo') == 2),
                            ),),
                              array('label' => 'Equipo', 'url' => array('#'), 'items' => array(
                                array('label' => 'Resguardo', 'url' => array('/resguardo')),
                                array('label' => 'Prestamo', 'url' => array('/prestamos')),
                                 array('label' => 'Reportes', 'url' => array('/reportes')),
                            ),  'visible' => Yii::app()->user->getState('tipo') == 1 || Yii::app()->user->getState('tipo') == 3|| Yii::app()->user->getState('tipo') == 2),
                         

                   array('label' => 'Administración', 'url' => array('#'), 'items' => array(
                     array('label' => 'Ingenieros', 'url' => array('/responsable'),'visible' => Yii::app()->user->getState('tipo') == 1 ),
                       array('label' => 'Colaboradores', 'url' => array('/colaborador'),'visible' => Yii::app()->user->getState('tipo') == 1 ),
                         
                                array('label' => 'Hotel', 'url' => array('/hotel')),
                                array('label' => 'Departamento', 'url' => array('/departamento')),
                                array('label' => 'Gerencia', 'url' => array('/gerencia')),
                                array('label' => 'Direccion', 'url' => array('/direccion')),
                                //array('label' => 'Borrado de datos', 'url' => array('/eliminarTablas'),'visible' => Yii::app()->user->getState('tipo') == 1 ),
                            ),'visible' => Yii::app()->user->getState('tipo') == 1 ||Yii::app()->user->getState('tipo') == 2
                        ),
                         
                               
                    ),
                ),
                array(
                    'class' => 'bootstrap.widgets.TbMenu',
                    'encodeLabel' => false,
                    'htmlOptions' => array('class' => 'pull-right'),
                    'items' => array(
                        array('label' => '<i class="icon-user icon-white"></i> ' . Yii::app()->user->name, 'url' => '#', 'active' => true, 'items' => array(
                                array('label' => '<i class="icon-cog"></i> Cambiar password', 'url' => array('/home/cambiarPassword'), 'visible' => !Yii::app()->user->isGuest),
                                '---',
                                array('label' => '<i class="icon-off"></i> Logout', 'url' => array('/home/logout'), 'visible' => !Yii::app()->user->isGuest),
                            )),
                    ),
                ),
            ),
        ));
        ?>

        <div class="container" id="page">

            <?php if (isset($this->breadcrumbs)): ?>
                <?php
                $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
                    'links' => $this->breadcrumbs,
                ));
                ?><!-- breadcrumbs -->
<?php endif ?>

<?php echo $content; ?>

            <div class="clear"></div>

            <hr>
            <div id="footer" class="span4 offset4">
                &copy; <?php echo date('Y'); ?> Palace Resorts.<br/>
                <br/>
            </div><!-- footer -->

        </div><!-- page -->
    </body>
</html>
