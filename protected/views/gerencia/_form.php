<?php
/* @var $this GerenciaController */
/* @var $model Gerencia */
/* @var $form CActiveForm */
?>

<div class="form">
    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'gerencia-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">LOS CAMPOS CON<span class="required">*</span> SON REQUERIDOS.</p>
<?php echo $form->errorSummary($model); ?>

    <div class="row-fluid">


        <div class="span4">
            <?php echo $form->labelEx($model, 'nombregerencia'); ?>

            <?php
            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'model' => $model,
                'attribute' => 'nombregerencia',
                'id' => 'gerencia',
                'name' => 'Gerencia[nombregerencia]',
                'source' => $this->createUrl('listarnombregerencia'),
                'options' => array(
                    'showAnim' => 'fold',
                    'select' => "js:function(event, ui) {
    $('#" . CHtml::activeId($model, 'nombregerencia') . "').val(ui.item.id);
    $('#nombregerencia').val(ui.item.label);
    return false;
  }",
                ),
                'htmlOptions' => array(
                    'style' => 'text-transform:uppercase',
                ),
            ));
            ?>
            <?php echo $form->error($model, 'nombregerencia'); ?>
        </div>

        <div class="span4">
<?php echo $form->labelEx($model, 'nombre_direccion'); ?>
<?php echo $form->dropdownList($model, 'direccion_id', Gerencia::getListDireccion(), array('empty' => 'Seleccione direccion')); ?>
<?php echo $form->error($model, 'nombre_direccion'); ?>
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

</div><!-- form -->