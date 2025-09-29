<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle = Yii::app()->name . ' - Inicio de sesión';
$this->breadcrumbs = array(
    'Inicio de sesión',
);
?>

<div class="form">



    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'login-form',
        'focus' => array($model, 'usuario'),
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    ));
    ?>

    <h2>Inicio de sesión</h2>
    <div class="row-fluid">
        <div class="offset2">
            <div>
                <?php echo $form->labelEx($model, 'usuario'); ?>
                <?php echo $form->textField($model, 'usuario'); ?>
<?php echo $form->error($model, 'usuario'); ?>
            </div>

            <div>
                <?php echo $form->labelEx($model, 'password'); ?>
                <?php echo $form->passwordField($model, 'password'); ?>
<?php echo $form->error($model, 'password'); ?>
            </div>
        </div>

        <div class="row buttons"><center>

                <?php
                $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType' => 'submit',
                    'type' => 'primary',
                    'htmlOptions' => array('name' => 'login'),
                    'label' => 'Entrar',
                ));
                ?>
        </div>


<?php $this->endWidget(); ?>
    </div><!-- form -->