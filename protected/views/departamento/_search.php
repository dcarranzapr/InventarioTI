<?php
/* @var $this DepartamentoController */
/* @var $model Departamento */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

    <div class="row">
<?php echo $form->label($model, 'iddepartamento'); ?>
<?php echo $form->textField($model, 'iddepartamento'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'nombredepartamento'); ?>
<?php echo $form->textField($model, 'nombredepartamento', array('size' => 35, 'maxlength' => 35)); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'descripciondepartamento'); ?>
<?php echo $form->textArea($model, 'descripciondepartamento', array('rows' => 6, 'cols' => 50)); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'gerencia_id'); ?>
<?php echo $form->textField($model, 'gerencia_id'); ?>
    </div>

    <div class="row buttons">
    <?php echo CHtml::submitButton('Search'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->