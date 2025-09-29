<?php
/* @var $this EquipogeneralController */
/* @var $model Equipogeneral */

$this->breadcrumbs = array(
    'Equipogenerals' => array('index'),
    'Reporte de historiscos de préstamos',
);

$this->menu = array(
    array('label' => 'Reporte general', 'url' => array('generals'), 'visible' => Yii::app()->user->getState('tipo') == 1 || Yii::app()->user->getState('tipo') == 2),
    array('label' => 'Reportes por resguardo', 'url' => array('index')),
    array('label' => 'Reportes por prestamos', 'url' => array('reportePrestamo')),
    array('label' => 'Reporte de equipo sin asignar', 'url' => array('reporteSinAsignacion')),
    array('label' => 'Reporte de historico de préstamos', 'url' => array('reportePrestamoLog'), 'visible' => Yii::app()->user->getState('tipo') == 1 || Yii::app()->user->getState('tipo') == 2),
    array('label' => 'Reporte de equipo hotel/status', 'url' => array('reporteHotelStatus'), 'visible' => Yii::app()->user->getState('tipo') == 1 || Yii::app()->user->getState('tipo') == 2),
);
?>

<h1>Reporte de equipo en préstamo</h1>
</br>
<div class="row-fluid">
<?php echo CHtml::beginForm(Yii::app()->createUrl('/reportes/prestamoLogResults'), 'POST', array(
    'id'=>'form-search',
    'onSubmit'=>'return false;'
)); ?>
    <div class="row-fluid">
        <div class="control-group span3">
            <?php echo CHtml::label("Hotel", 'idHotel', array(
                'class' => 'control-label'
            )); ?>
            <div class="controls">
                <?php echo CHtml::dropDownList("idHotel", 'select', 
                    Equipogeneral::getListHotel(),
                    array('empty' => "Seleccionar hotel")
                );?>
            </div>
        </div>
        <div class="control-group span3">
            <?php echo CHtml::label("Marca", 'idMarca', array(
                'class' => 'control-label'
            )); ?>
            <div class="controls">
                <?php echo CHtml::dropDownList("idMarca", 'select', 
                    Equipogeneral::getListMarca(),
                    array('empty' => "Seleccionar marca")
                );?>
            </div>
        </div>
        <div class="control-group span3">
            <?php echo CHtml::label("Model", 'idModelo', array(
                'class' => 'control-label'
            )); ?>
            <div class="controls">
                <?php echo CHtml::dropDownList("idModelo", 'select', 
                    Equipogeneral::getListMarca(),
                    array('empty' => "Seleccionar modelo")
                );?>
            </div>
        </div>
        <div class="control-group span3">
            <?php echo CHtml::label("Proveedor", 'idProveedor', array(
                'class' => 'control-label'
            )); ?>
            <div class="controls">
                <?php echo CHtml::dropDownList("idProveedor", 'select', 
                    Equipogeneral::getListProveedor1(),
                    array('empty' => "Seleccionar proveedor")
                );?>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="control-group span3">
            <?php echo CHtml::label("Tipo equipo", 'idTipo', array(
                'class' => 'control-label'
            )); ?>
            <div class="controls">
                <?php echo CHtml::dropDownList("idTipo", 'select', 
                    Equipogeneral::getListTipo(),
                    array('empty' => "Seleccionar tipo")
                );?>
            </div>
        </div>
        <div class="control-group span3">
            <?php echo CHtml::label("Nombre colaborador", 'nombreColaborador', array(
                'class' => 'control-label'
            )); ?>
            <div class="controls">
                <?php echo CHtml::textField('nombreColaborador', '', array(
                    "class" => "span12 form-block",
                )); ?>
            </div>
        </div>
        <div class="control-group span3">
            <?php echo CHtml::label("Estado", 'estado', array(
                'class' => 'control-label'
            )); ?>
            <div class="controls">
                <?php echo CHtml::textField('estado', '', array(
                    "class" => "span12 form-block",
                )); ?>
            </div>            
        </div>
        <div class="control-group span3">
            <?php echo CHtml::label("Fecha préstamo", 'fecha_prestamo', array(
                'class' => 'control-label'
            )); ?>
            <div class="controls">
                <?php echo $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name'=>'fechaPrestamo',
                    'language' => 'es',
                    'htmlOptions' => array(
                        'id' => 'fecha_prestamo',
                        'size' => '10',
                    ),
                    'options'=>array(
                        'dateFormat'=>'yy-mm-dd',
                    ),
                    'defaultOptions' => array(
                        'showOn' => 'focus',
                        #'dateFormat' => 'yy-mm-dd',
                        'showOtherMonths' => true,
                        'selectOtherMonths' => true,
                        'changeMonth' => true,
                        'changeYear' => true,
                        'showButtonPanel' => true,
                    )
                ), true)?>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="control-group span3">
            <?php echo CHtml::label("Fecha devolución", 'fecha_devolucion', array(
                'class' => 'control-label'
            )); ?>
            <div class="controls">
                <?php echo $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name'=>'fechaDevolucion',
                    'language' => 'es',
                    'htmlOptions' => array(
                        'id' => 'fecha_devolucion',
                        'size' => '10',
                    ),
                    'options'=>array(
                        'dateFormat'=>'yy-mm-dd',
                    ),
                    'defaultOptions' => array(
                        'showOn' => 'focus',
                        #'dateFormat' => 'dd-mm-yy',
                        'showOtherMonths' => true,
                        'selectOtherMonths' => true,
                        'changeMonth' => true,
                        'changeYear' => true,
                        'showButtonPanel' => true,
                    )
                ), true)?>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="control-group">
            <div class="controls text-center">
                <?php echo CHtml::button("Buscar", array(
                    'class' => 'btn btn-success btn-large',
                    'id' => 'searchButton'
                )); ?>
            </div>
        </div>
    </div>
<?php echo CHtml::endForm(); ?>
</div>

<br />
<?php echo CHtml::link('<i class="icon-download-alt icon-white"></i> Generar Excel', array('/reportes/prestamolog'), array('target' => '_blank', 'class' => 'btn btn-primary','id'=>"downloadExcel")); ?>

<div class="row-fluid" id="renderResults"></div>

<?php
Yii::app()->clientScript->registerScript('re-install-date-picker', "
function reinstallDatePicker(id, data) {
    $('#datepicker_for_prestamos').datepicker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional['es'], {'showAnim':'fold','dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true','constrainInput':'false'}));
      $('#datepicker_for_prestamos1').datepicker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional['es'], {'showAnim':'fold','dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true','constrainInput':'false'}));
    
}
");
?>
<script>
    $( "#searchButton" ).click(function() {
        var form = $('#form-search');
        var url = form.attr('action');
        var data = form.serialize();

        $("#renderResults").html('');

        $.ajax({
            'data' : data,
            'dataType' : 'json',
            'type' : 'get',
            'url' : url,
            'cache' : false,
            'success' : function(data) {
                if (data['error']) {
                    alert(data['msg']);
                } else {
                    $("#renderResults").html(data['render']);
                }
            }
        });
    });

    $( "#downloadExcel" ).click(function(e) {
        var url = $(this).attr('href');
        var data = $('#form-search').serialize();        
        window.open(url+"?"+data, '_blank');
        
        e.preventDefault();
        e.stopPropagation();
    });
</script>
