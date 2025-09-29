<?php
/* @var $this TipoequipoController */
/* @var $model Tipoequipo */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'tipoequipo-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">LOS CAMPOS CON<span class="required">*</span> SON REQUERIDOS.</p>
<?php echo $form->errorSummary($model); ?>

    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->labelEx($model, 'nombreTipoEquipo'); ?>

            <?php
            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'model' => $model,
                'attribute' => 'nombreTipoEquipo',
                'id' => 'plataforma',
                'name' => 'Sistemaoperativo[nombreTipoEquipo]',
                'source' => $this->createUrl('listarnombretipo'),
                'options' => array(
                    'showAnim' => 'fold',
                    'select' => "js:function(event, ui) {
    $('#" . CHtml::activeId($model, 'nombreTipoEquipo') . "').val(ui.item.id);
    $('#nombreTipoEquipo').val(ui.item.label);
    return false;
  }",
                ),
                'htmlOptions' => array(
                    'style' => 'text-transform:uppercase',
                ),
            ));
            ?>
<?php echo $form->error($model, 'nombreTipoEquipo'); ?>
        </div>

        <div class="span4" >
            <?php echo $form->labelEx($model, 'descripcion'); ?>
            <?php echo $form->textArea($model, 'descripcion', array('rows' => 1, 'cols' => 50)); ?>
<?php echo $form->error($model, 'descripcion'); ?>
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