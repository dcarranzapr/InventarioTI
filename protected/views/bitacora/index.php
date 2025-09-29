<?php
$this->breadcrumbs = array(
    'Incidencias',
);
?>

<h2>Lista de Incidencias</h2>

<?php echo CHtml::link('<i class="icon-download-alt icon-white"></i> Generar Excel', array('/reportes/incidenciasFiltro'), array('target' => '_blank', 'class' => 'btn btn-primary')); ?>
<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'servidor-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'columns' => array(
        array(
            'name' => 'nombre_servidor',
            'value' => '$data->fkServidor->nombreservidor',
        ),
        array(
            'name' => 'tipo_servidor',
            'value' => '$data->fkServidor->fkTipo->nombretipo',
        ),
        'fechadiaria',
        'causabitacora',
        'solucionbitacora',
        'estadobitacora',
        array(
            'name' => 'nombre_responsable',
            'value' => '$data->fkResponsable->nombre',
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{view}',
            'buttons' => array(
                'view' => array(
                    'url' => '$data->fkServidor->tipo == 1 ? Yii::app()->controller->createUrl("/servidoresfisicos/view", array("id"=>$data->fkServidor->id_servidor)) : Yii::app()->controller->createUrl("/servidoresvirtuales/view", array("id"=>$data->fkServidor->id_servidor))',
                ),
            ),
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>