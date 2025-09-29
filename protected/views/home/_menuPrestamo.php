
<?php

$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'prestamos-grid',
    'dataProvider' => $model->searchPrestamos(),
    'type' => 'condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'template' => "{items} {pager}",
    'columns' => array(
        array(
            'name' => 'nombre_usuario',
            'value' => '$data->nombre_usuario',
        ),
        array(
            'name' => 'fecha_prestamo',
            'type' => 'date',
            'value' => 'strtotime($data->fecha_prestamo)',
        ),
        array(
            'name' => 'fecha_devolucion',
            'type' => 'date',
            'value' => 'strtotime($data->fecha_devolucion)',
        ),
        array(
            'class' => 'editable.EditableColumn',
            'name' => 'proroga',
            'headerHtmlOptions' => array('style' => 'width: 100px'),
            'editable' => array(
                'emptytext' => 'Asignar prorroga',
                'type' => 'text',
                'attribute' => 'proroga',
                'url' => $this->createUrl('Prestamos/updatePrestamo'),
                'placement' => 'right',
                'title' => 'Días de proroga',
                'success' => 'function(data){
	                	$.fn.yiiGridView.update("prestamos-grid");

	                               
	                                }',
            ), 'visible' => Yii::app()->user->getState('tipo') == 2 || Yii::app()->user->getState('tipo') == 1
        ),
        array(
            'name' => 'nombreResponsable',
            'value' => '$data->nombreResponsable',
        ),
        array(
            'name' => 'estatus',
            'type' => 'raw',
            'value' => 'CHtml::link($data->estatus == 1 && $data->dias<=0? "Recuperar equipo": ($data->dias == 1 ? "$data->dias día" : "$data->dias días"),
				array("/prestamos/view", "id"=>$data->id),
				array("class"=>$data->estatus == 1 && $data->dias<=0 ? "label label-important" : "label label-success", 
					"title"=>$data->estatus == 1 && $data->dias<=0?"Se ha restrasado por"."$data->dias día":"Ver detalles"))',
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{recuperar}',
            'htmlOptions' => array('onclick' => 'return confirm("¿Esta seguro que todos los equipos de este préstamo fueron devueltos?")'),
            'buttons' => array(
                'recuperar' => array(
                    'label' => 'Equipo recuperado',
                    'icon' => 'ok-sign',
                    'click' => "function(){}",
                    'url' => 'Yii::app()->controller->createUrl("recuperarEquipo",array("id"=>$data->id))',
                ),
            ),
        ),
    ),
    'emptyText' => 'No hay prestamos para recoger',
    'pagerCssClass' => 'pagination pagination-centered',
));
?>