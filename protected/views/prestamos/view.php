<?php
/* @var $this ResguardoController */
/* @var $model Resguardo */

$this->breadcrumbs = array(
    'Prestamo' => array('index'),
    $model->id,
);

$this->menu = array(
    array('label' => 'Crear prestamo', 'url' => array('create')),
    array('label' => 'Modificar prestamo', 'url' => array('update', 'id' => $model->id)),
    array('label' => 'Prestamo recuperado', 'url' => '#', 'linkOptions' => array('submit' => array('recuperarEquipo', 'id' => $model->id), 'confirm' => 'Está seguro que todos los equipos fueron recuperados?')),
    array('label' => 'Control de prestamos', 'url' => array('index')),
);
?>

<h1>Prestamos de <?php echo $model->colaboradorIdUsuario->usuarioNombre; ?></h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'fecha_prestamo',
        'fecha_devolucion',
        'proroga',
        array('name' => 'usuarioNombre',
            'value' => $model->colaboradorIdUsuario->usuarioNombre),
        'descripcion',
    ),
));
?>



<?php
/** Start Widget * */
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'dialog-animation',
    'options' => array(
        'width' => '500px',
        'height' => 'auto',
        'title' => 'CAPTURA DE INVENTARIO',
        'autoOpen' => false,
        'show' => array(
            'effect' => 'blind',
            'duration' => 1000,
        ),
        'hide' => array(
            'effect' => 'explode',
            'duration' => 500,
        ),
    ),
));



$this->renderPartial('_gridBusqueda', array(
    'model3' => $model3,
));



$this->endWidget('zii.widgets.jui.CJuiDialog');

/** End Widget * */
echo "CAPTURA DE EQUIPO";
echo CHtml::imageButton(Yii::app()->baseUrl . '/images/resguardo.jpg', array('style' => 'margin-right:0px;margin-top:5px;float:right; width:30px;',
    'title' => 'Agignar equipo',
    'onclick' => '$("#dialog-animation").dialog("open"); return false;',
));
$imghtml = CHtml::image(Yii::app()->baseUrl . '/images/iconos/printer.png');
echo CHtml::link($imghtml, array('imprimir', 'id' => $model->id), array("style" => "margin-right:5px;margin-top:10px;float:right; width:30px;"));
?>




<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'equipo',
    'dataProvider' => $dataProviderEquipo,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word;'),
    'columns' => array(
        array(
            'name' => 'nombre_tipo',
            'value' => '$data->idTipoEquipo0->nombreTipoEquipo',
            'htmlOptions' => array('style' => 'width:80px'),
        ),
        array(
            'name' => 'nombre_marca',
            'value' => '$data->idMarca0->nombremarca',
            'htmlOptions' => array('style' => 'width:70px'),
        ),
        array(
            'name' => 'nombre_modelo',
            'value' => '$data->idModelo0->nombremodelo',
            'htmlOptions' => array('style' => 'width:70px'),
        ),
        'name' => 'numeroSerie',
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{baja} ',
            'buttons' => array(
                'baja' => array(
                    'label' => 'Baja',
                    'icon' => 'trash',
                    'click' => "function(){
                                    $.fn.yiiGridView.update('equipo', {
                                        type:'POST',
                                        url:$(this).attr('href'),
                                        success:function(data) {
                                            
                                              
                                               $.fn.yiiGridView.update('equipogeneral-grid');
                                               
                                        }
                                    })
                                 
                              }
                              ",
                    'url' => 'Yii::app()->controller->createUrl("deleteEquipo",array("id"=>$data->id))',
                ),
            ),
        ),
    ),
))
?>


<script>
    /*
     MUY IMPORTANTE:
     Tu CActiveDataProvider debe proveer esta configuracion:
     'keyAttribute'=>'idcategoria',
     para que  var idcategoria = $.fn.yiiGridView.getSelection('categorias');
     devuelva un valor de seleccion.
     */
    function mostrarDetalles() {
        // no olvides configurar tu CActiveDataProvider con: 'keyAttribute'=>'idcategoria',
        var id = $.fn.yiiGridView.getSelection('equipogeneral-grid');
        $.fn.yiiGridView.update('equipo', {data: id});
        $.fn.yiiGridView.update('equipogeneral-grid');


    }



</script>
