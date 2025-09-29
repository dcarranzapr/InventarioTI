<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'bitacora-form',
    'enableAjaxValidation' => false,
        ));
?>

<p class="help-block">Los campos con <span class="required">*</span> son requeridos.</p>

<?php echo $form->errorSummary($model); ?>

<div class="row-fluid">
    <div class="span4">
        <?php echo $form->labelEx($model, 'fechadiaria'); ?>
        <?php
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'model' => $model,
            'attribute' => 'fechadiaria',
            'language' => 'es',
            'options' => array(
                'dateFormat' => 'yy-mm-dd',
                'constrainInput' => 'false',
                'duration' => 'fast',
                'showAnim' => 'slideDown',
                'changeMonth' => true,
                'changeYear' => true,
            )
                )
        );
        ?>
<?php echo $form->error($model, 'fechadiaria'); ?>
    </div>
    <div class="span4">
<?php echo $form->textFieldRow($model, 'causabitacora'); ?>
    </div>
    <div class="span4">
<?php echo $form->textFieldRow($model, 'solucionbitacora'); ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span4">
<?php echo $form->textFieldRow($model, 'estadobitacora'); ?>
    </div>
</div>

<div class="form-actions">
    <?php
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type' => 'primary',
        'label' => $model->isNewRecord ? 'Crear' : 'Guardar',
    ));
    ?>
</div>

<?php $this->endWidget(); ?>
