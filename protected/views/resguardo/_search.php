<?php
/* @var $this ResguardoController */
/* @var $model Resguardo */
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
<?php echo $form->label($model, 'id_resguardo'); ?>
<?php echo $form->textField($model, 'id_resguardo'); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'comentarios'); ?>
<?php echo $form->textArea($model, 'comentarios', array('rows' => 6, 'cols' => 50)); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'fechaCaptura'); ?>
<?php echo $form->textField($model, 'fechaCaptura'); ?>
    </div>



    <div class="row">
<?php echo $form->label($model, 'nombreEquipo'); ?>
<?php echo $form->textField($model, 'nombreEquipo', array('size' => 35, 'maxlength' => 35)); ?>
    </div>

    <div class="row">
<?php echo $form->label($model, 'capturaUser'); ?>
<?php echo $form->textField($model, 'capturaUser'); ?>
    </div>


    <div class="row">
<?php echo $form->label($model, 'idColaboradorEmpleado'); ?>
<?php echo $form->textField($model, 'idColaboradorEmpleado'); ?>
    </div>

    <div class="row buttons">
    <?php echo CHtml::submitButton('Search'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->