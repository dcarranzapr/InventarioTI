<?php
$this->breadcrumbs = array(
    'Ingenieros',
);

$this->menu = array(
    array('label' => 'Crear ingeniero de soporte', 'url' => array('create')),
);
?>

<h2>Ingenieros de soporte</h2>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'responsable-grid',
    'type' => 'striped bordered condensed hover',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        'usuario',
        'nombre',
        array(
            'name' => 'tipo',
            'filter' => Responsable::getListTipo(),
            'value' => '$data->fkTipo->nombreperfil',
        ),
        array(
            'name' => 'acceso',
            'type' => 'raw',
            'filter' => Responsable::getListEstatus(),
            'value' => 'CHtml::link($data->acceso == 0 ? "Denegado" : "Permitido", "",array("class" => $data->acceso == 0 ? "label label-important" : "label label-success"))',
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{update} {delete}',
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>
