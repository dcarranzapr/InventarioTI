<?php
/* @var $this ResguardoController */
/* @var $model Resguardo */

$this->breadcrumbs = array(
    'Resguardos' => array('index'),
    'Control',
);

$this->menu = array(
    array('label' => 'Crear resguardo', 'url' => array('create')),
);
?>

<h1>Control de resguardos</h1>



<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'resguardo-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'rowCssClassExpression' => '$data->equipogeneralsCount==0 ? "error" : ""',
    'columns' => array(
        array(
            'name' => 'nombre_hotel',
            'filter' => Resguardo::getListHotel(),
            #'value' => '$data->idColaboradorEmpleado0->departamentoIddepartamento->hotelhasdepartamentos->hoteles->nombreHotel',
            'value' => '$data->idColaboradorEmpleado0->hotel->nombreHotel',
        ),
        array(
            'name' => 'departamento',
            'filter' => Resguardo::getListDepartamento(),
            'value' => '$data->idColaboradorEmpleado0->departamentoIddepartamento->nombredepartamento',
        ),
        array(
            'name' => 'nombre_colaborador',
            'value' => '$data->idColaboradorEmpleado0->usuarioNombre',
        ),
        //'fechaCaptura',
        'nombreEquipo',
        array(
            'name' => 'total',
            'value' => '$data->equipogeneralsCount',
            'filter' => false,
        ),
        //'capturaUser',
        /*
          'iddepartamento',
          'idColaboradorEmpleado',
          'ip',
         */
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{delete}{update}{view}{print}',
            'buttons' => array(
                'print' => array(
                    'label' => 'Imprimir resguardo',
                    'icon' => 'print',
                    'click' => "function(){
                                    $.fn.yiiGridView.update('resguardo-grid', {
                                        type:'POST',
                                        url:$(this).attr('href'),
                                
                                    })
                                 return true;
                              }
                              ",
                    'url' => 'Yii::app()->controller->createUrl("imprimir",array("id"=>$data->id_resguardo))',
                ),
            ),
        ),
    ), 'pagerCssClass' => 'pagination pagination-centered',
));
?>
