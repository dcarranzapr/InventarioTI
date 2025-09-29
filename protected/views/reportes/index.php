<?php
$this->breadcrumbs = array(
    'Reportes',
);
$this->menu = array(
    array('label' => 'Reporte general', 'url' => array('generals'), 'visible' => Yii::app()->user->getState('tipo') == 1 || Yii::app()->user->getState('tipo') == 2),
    array('label' => 'Reportes por resguardo', 'url' => array('index')),
    array('label' => 'Reportes por prestamos', 'url' => array('reportePrestamo')),
    array('label' => 'Reporte de equipo sin asignar', 'url' => array('reporteSinAsignacion')),
    array('label' => 'Reporte de historico de préstamos', 'url' => array('reportePrestamoLog'), 'visible' => Yii::app()->user->getState('tipo') == 1 || Yii::app()->user->getState('tipo') == 2),
);
?>

<h2>Reportes</h2>

<table class="table table-hover" style="width:75%">
    <thead>
    <th>Reporte</th>
    <th>Descargar</th>
</thead>
<tbody>
    <tr>
        <td>Reporte general</td>
        <td>
            <?php echo CHtml::link('<i class="icon-download-alt icon-white"></i> Generar Excel', array('/reportes/gene'), array('target' => '_blank', 'class' => 'btn btn-primary')); ?>
        </td>
    </tr>

</tbody>
</table>