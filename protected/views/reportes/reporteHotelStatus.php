<?php
/* @var $this EquipogeneralController */
/* @var $model Equipogeneral */

$this->breadcrumbs = array(
    'Equipogenerals' => array('index'),
    'Resporte de préstamo',
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

<h1>Reporte de equipo hotel/status</h1>
</br>
<div class="row-fluid">
<?php echo CHtml::beginForm(Yii::app()->createUrl('/reportes/hotelStatusResults'), 'POST', array(
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
            <?php echo CHtml::label("Status", 'idEstatus', array(
                'class' => 'control-label'
            )); ?>
            <div class="controls">
                <?php echo CHtml::dropDownList("idEstatus", 'select', 
                    Equipogeneral::getListEstatus(),
                    array('empty' => "Seleccionar status")
                );?>
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
<?php echo CHtml::link('<i class="icon-download-alt icon-white"></i> Generar Excel', array('/reportes/hotelStatus'), array('target' => '_blank', 'class' => 'btn btn-primary','id'=>"downloadExcel")); ?>

<div class="row-fluid" id="renderResults"></div>
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
