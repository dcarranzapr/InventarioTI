<?php
/* @var $this DepartamentoController */
/* @var $model Departamento */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'departamento-form',
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
            <?php echo $form->labelEx($model, 'nombredepartamento'); ?>

            <?php
            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'model' => $model,
                'attribute' => 'nombredepartamento',
                'id' => 'gerencia',
                'name' => 'Departamento[nombredepartamento]',
                'source' => $this->createUrl('listarnombredepartamento'),
                'options' => array(
                    'showAnim' => 'fold',
                    'select' => "js:function(event, ui) {
    $('#" . CHtml::activeId($model, 'nombredepartamento') . "').val(ui.item.id);
    $('#nombredepartamento').val(ui.item.label);
    return false;
  }",
                ),
                'htmlOptions' => array(
                    'style' => 'text-transform:uppercase',
                ),
            ));
            ?>
            <?php echo $form->error($model, 'nombredepartamento'); ?>
        </div>

        <div class="span4">
<?php echo $form->labelEx($model, 'descripcion'); ?>
<?php echo $form->textArea($model, 'descripciondepartamento', array('rows' => 1, 'cols' => 50)); ?>
            <?php echo $form->error($model, 'descripciondepartamento'); ?>
        </div>

        <div class="span4">
<?php echo $form->labelEx($model, 'gerencia_id'); ?>
<?php echo $form->dropDownList($model, 'gerencia_id', Departamento::getListGerencia(), array('empty' => 'Seleccione gerencia')); ?>
        <?php echo $form->error($model, 'gerencia_id'); ?>
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