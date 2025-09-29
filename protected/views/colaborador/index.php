<?php
/* @var $this ColaboradorController */
/* @var $model Colaborador */

$this->breadcrumbs = array(
    'Colaboradors' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear colaborador', 'url' => array('create')),
);
?>

<h1>Control de colaboradores</h1>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'gerencia-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word;'),
    'columns' => array(
        'numeroColaborador',
        'usuarioNombre',
        /*
          'departamento_iddepartamento',
         */
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{update}',
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>
