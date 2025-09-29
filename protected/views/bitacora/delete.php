<?php
if ($tipo == 1) {
    $this->breadcrumbs = array(
        'Servidores fisicos' => array('/servidoresfisicos'),
        'Detalle Servidor' => array('/servidoresfisicos/view', 'id' => $id),
        'Eliminar incidencia',
    );
    $this->menu = array(
        array('label' => 'Lista de Servidores fisicos', 'url' => array('/servidoresfisicos')),
    );
} else if ($tipo == 2) {
    $this->breadcrumbs = array(
        'Servidores virtuales' => array('/servidoresvirtuales'),
        'Detalle Servidor' => array('/servidoresvirtuales/view', 'id' => $id),
        'Eliminar incidencia',
    );
    $this->menu = array(
        array('label' => 'Lista de Servidores virtuales', 'url' => array('/servidoresvirtuales')),
    );
}
?>

<h2>Eliminar bitacora de incidencia del servidor <?php echo $nombre; ?></h2>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'ip-form',
    'enableAjaxValidation' => true,
        ));
?>
<?php echo $form->hiddenField($model, 'id_diario', array('type' => "hidden", 'size' => 150, 'maxlength' => 255));
?>	
<div class="alert alert-error">
    <strong>Importante!!!</strong>.
</div>
<p>Desea <strong>Eliminar</strong> el registro?</p>


<div class="form-actions">
    <?php
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type' => 'primary',
        'label' => 'Confirmar',
    ));
    ?>
</div>

<?php $this->endWidget(); ?>
