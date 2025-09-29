<?php
/* @var $this PrestamosController */
/* @var $model Prestamos */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'prestamos-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">LOS CAMPOS CON<span class="required">*</span> SON REQUERIDOS.</p>
    <?php echo $form->errorSummary($model); ?>


    <?php
    $this->renderPartial('colaborador', array(
        'model' => $model,
    ));
    ?>



    <div class="span4">
        <?php echo $form->labelEx($model, 'fecha_prestamo'); ?>
        <?php
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'model' => $model,
            'attribute' => 'fecha_prestamo',
            'language' => 'es',
            'options' => array(
                'dateFormat' => 'dd-mm-yy',
                'constrainInput' => 'false',
                'duration' => 'fast',
                'showAnim' => 'slideDown',
                'changeMonth' => true,
                'changeYear' => true,
            )
                )
        );
        ?>
        <?php echo $form->error($model, 'fecha_prestamo'); ?>
    </div>
    <div class="span4">
        <?php echo $form->labelEx($model, 'fecha_devolucion'); ?>
        <?php
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'model' => $model,
            'attribute' => 'fecha_devolucion',
            'language' => 'es',
            'options' => array(
                'dateFormat' => 'dd-mm-yy',
                'constrainInput' => 'false',
                'duration' => 'fast',
                'showAnim' => 'slideDown',
                'changeMonth' => true,
                'changeYear' => true,
            )
                )
        );
        ?>
<?php echo $form->error($model, 'fecha_devolucion'); ?>
    </div>

    <div class="row-fluid">


        <div class="span4">
            <?php echo $form->labelEx($model, 'nombreEquipo'); ?>
            <?php echo $form->textField($model, 'nombreEquipo', array('size' => 35, 'maxlength' => 35, 'style' => 'text-transform:uppercase')); ?>
<?php echo $form->error($model, 'nombreEquipo'); ?>
        </div>
        <div id="plataforma"class="span4" >
            <?php echo $form->labelEx($model, 'nombre_plataforma'); ?>
            <?php echo $form->dropdownList($model, 'Plataforma_idPlataforma', Equipogeneral::getListPlataforma(), array('empty' => 'SELECCIONE PLATAFORMA')); ?>
<?php echo $form->error($model, 'nombre_plataforma'); ?>
        </div>


    </div>
    <div class="row-fluid">
        <div class="span">
            <?php echo $form->labelEx($model, 'descripcion'); ?>
            <?php echo $form->textArea($model, 'descripcion', array('rows' => 3, 'cols' => 400, 'style' => 'text-transform:uppercase;width:92%;')); ?>
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
</div>


