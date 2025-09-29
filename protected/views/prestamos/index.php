<?php
/* @var $this PrestamosController */
/* @var $model Prestamos */

$this->breadcrumbs = array(
    'Prestamo' => array('index'),
    'Control',
);
$this->menu = array(
    array('label' => 'Crear prestamo', 'url' => array('create')),
);
?>
<h1>Control de préstamos</h1>
<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'prestamos-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'rowCssClassExpression' => '$data->equipogeneralsCount==0 ? "error" : ""',
    'afterAjaxUpdate' => 'reinstallDatePicker',
    'type' => 'striped bordered condensed hover',
    'columns' => array(
        array(
            'name' => 'nombre_hotel',
            'filter' => Resguardo::getListHotel(),
            'value' => '$data->colaboradorIdUsuario->hotel->nombreHotel',
        ),
        array(
            'name' => 'departamento',
            'filter' => Resguardo::getListDepartamento(),
            'value' => '$data->colaboradorIdUsuario->departamentoIddepartamento->nombredepartamento',
        ),
        array(
            'name' => 'usuarioNombre',
            'value' => '$data->colaboradorIdUsuario->usuarioNombre',
        ),
        'nombreEquipo',
        array(
            'class' => 'editable.EditableColumn',
            'name' => 'fecha_prestamo',
            'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model' => $model,
                'attribute' => 'fecha_prestamo',
                'language' => 'es',
                // 'i18nScriptFile' => 'jquery.ui.datepicker-es.js', 
                'htmlOptions' => array(
                    'id' => 'datepicker_for_prestamos1',
                    'size' => '10',
                ),
                'defaultOptions' => array(// (#3)
                    'showOn' => 'focus',
                    'dateFormat' => 'yy-mm-dd',
                    'showOtherMonths' => true,
                    'selectOtherMonths' => true,
                    'changeMonth' => true,
                    'changeYear' => true,
                    'showButtonPanel' => true,
                )
                    ), true),
            'headerHtmlOptions' => array('style' => 'width: 100px'),
            'editable' => array(
                'type' => 'date',
                'attribute' => 'fecha_prestamo',
                'url' => $this->createUrl('updatePrestamo'),
                'placement' => 'right',
                'viewformat' => 'dd-mm-yyyy',
                'title' => 'Seleccione una fecha',
                'validate' => 'function(value) {
	              if(!value) return "Seleccione una fecha."
	            }',
            ),
        ),
        array(
            'class' => 'editable.EditableColumn',
            'name' => 'fecha_devolucion',
            'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model' => $model,
                'attribute' => 'fecha_devolucion',
                'language' => 'es',
                //'i18nScriptFile' => 'jquery.ui.datepicker-es.js', 
                'htmlOptions' => array(
                    'id' => 'datepicker_for_prestamos',
                    'size' => '10',
                ),
                'defaultOptions' => array(// (#3)
                    'showOn' => 'focus',
                    'dateFormat' => 'dd-mm-yy',
                    'showOtherMonths' => true,
                    'selectOtherMonths' => true,
                    'changeMonth' => true,
                    'changeYear' => true,
                    'showButtonPanel' => true,
                )
                    ), true),
            'headerHtmlOptions' => array('style' => 'width: 100px'),
            'editable' => array(
                'type' => 'date',
                'attribute' => 'fecha_devolucion',
                'url' => $this->createUrl('updatePrestamo'),
                'placement' => 'right',
                'viewformat' => 'dd-mm-yyyy',
                'title' => 'Seleccione una fecha',
                'validate' => 'function(value) {
	              if(!value) return "Seleccione una fecha."
	            }',
            ),
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
            'header' => 'Total de equipos en préstamo',
            'value' => '$data->equipogeneralsCount',
            'filter' => false,
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{delete}{update}{view}{print}',
            'buttons' => array(
                'print' => array(
                    'label' => 'Imprimir prestamo',
                    'icon' => 'print',
                    'click' => "function(){
                                    $.fn.yiiGridView.update('prestamos-grid', {
                                        type:'POST',
                                        url:$(this).attr('href'),
                                
                                    })
                                 return true;
                              }
                              ",
                    'url' => 'Yii::app()->controller->createUrl("imprimir",array("id"=>$data->id))',
                ),
            ),
        ),
    ),
));
?>

<?php
Yii::app()->clientScript->registerScript('re-install-date-picker', "
function reinstallDatePicker(id, data) {
    $('#datepicker_for_prestamos').datepicker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional['es'], {'showAnim':'fold','dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true','constrainInput':'false'}));
      $('#datepicker_for_prestamos1').datepicker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional['es'], {'showAnim':'fold','dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true','constrainInput':'false'}));
    
}
");
?>