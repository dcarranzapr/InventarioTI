<?php
$this->breadcrumbs = array(
    'Reportes',
);
?>

<h2>Eliminar datos de resguardo,inventario y préstamos</h2>

<table class="table table-hover" style="width:75%">
    <thead>
    <th>Acción</th>
    <th></th>
</thead>
<tbody>
    <tr>
        <td>Vaciar base de datos</td>
        <td>
            <?php
            echo CHtml::ajaxSubmitButton('Eliminar datos', CHtml::normalizeUrl(array('/EliminarTablas/drop')), array('type' => 'POST',
                'success' => 'function(){alert("Los datos de las tablas se han eliminado")}', 'error' => 'function(data){alert("Ha surgido un error")}'), array('target' => '_blank', 'class' => 'btn btn-danger ', 'confirm' => Yii::t('admin', "Está seguro que desea borrar los datos de las tablas, \nInventario, Resguardo y Préstamos?"),
                    //test
            ));
            ?>
        </td>
    </tr>

</tbody>
</table>