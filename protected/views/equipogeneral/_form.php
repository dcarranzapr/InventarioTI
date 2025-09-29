<?php
/* @var $this EquipogeneralController */
/* @var $model Equipogeneral */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'equipogeneral-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">LOS CAMPOS CON<span class="required">*</span> SON REQUERIDOS.</p>
    <?php echo $form->errorSummary($model); ?>

    <?php
    if (empty($model->resguardoIdresguardo->nombreEquipo)) {
        if (empty($model->idPrestamo0->nombreEquipo)) {
            
        } else {
            echo("El equipo se prestó al colaborador " . $model->idPrestamo0->colaboradorIdUsuario->usuarioNombre .
            " con el nombre de equipo " . $model->idPrestamo0->nombreEquipo);
        }
    } else {
        echo("El equipo está a signado al colaborador " . $model->resguardoIdresguardo->idColaboradorEmpleado0->usuarioNombre .
        " con el nombre de equipo " . $model->resguardoIdresguardo->nombreEquipo);
    }
    ?>



    <div class='alert alert-info' id='datosGenerales'>Datos generales</div>
    <div class="row-fluid">
        <div class="span4">
<?php echo $form->labelEx($model, 'nombre_tipo'); ?>
<?php
echo $form->dropdownList($model, 'idTipoEquipo', Equipogeneral::getListTipo(), array(
    'ajax' => array(
        'type' => 'POST',
        'url' => CController::createUrl('Equipogeneral/Selectmodelo'),
        'update' => '#' . CHtml::activeId($model, 'idModelo'),
        'beforeSend' => 'function(){
                              	 $("#Equipogeneral_idModelo").find("option").remove();
                               $("#Equipogeneral_idMarca option[value=]").attr("selected",true);
                             
                              }',
    ), 'prompt' => 'SELECCIONE TIPO'
));
?>
            <?php echo $form->error($model, 'nombre_tipo'); ?>
        </div>
        <div id="marca" class="span4" style='display: none;'>
            <?php echo $form->labelEx($model, 'nombre_marca'); ?>
<?php
echo $form->dropdownList($model, 'idMarca', Equipogeneral::getListMarca(), array(
    'ajax' => array(
        'type' => 'POST',
        'url' => CController::createUrl('Equipogeneral/Selectmodelo'),
        'update' => '#' . CHtml::activeId($model, 'idModelo'),
        'beforeSend' => 'function(){
                               $("#Equipogeneral_idModelo").find("option").remove();
                              
                              }',
    ), 'prompt' => 'SELECCIONE MARCA'
));
?>
            <?php echo $form->error($model, 'nombre_marca'); ?>
        </div>
        <div id="modelo" class="span4" style='display: none;'>
            <?php echo $form->labelEx($model, 'nombre_modelo'); ?>
<?php
$lista_dos = array();
if (isset($model->idModelo)) {
    $id_marca = intval($model->idMarca);
    $lista_dos = CHtml::listData(Modelo::model()->findAll("fkidMarca = '$id_marca'"), 'idModelo', 'nombremodelo');
}
echo $form->dropDownList($model, 'idModelo', $lista_dos, array('prompt' => 'SELECCIONE MODELO')
);
?>

            <?php echo $form->error($model, 'nombre_modelo'); ?>
        </div>

    </div>

    <div class="row-fluid" >
        <div id="noSerie" class="span4" style='display: none;'>
<?php echo $form->labelEx($model, 'numeroSerie'); ?>
<?php echo $form->textField($model, 'numeroSerie', array('size' => 50, 'maxlength' => 50, 'style' => 'text-transform:uppercase',)); ?>
            <?php echo $form->error($model, 'numeroSerie'); ?>
        </div>
        <div id="hotel" class="span4" style='display: none;'>
            <?php echo $form->labelEx($model, 'nombre_hotel'); ?>
<?php echo $form->dropdownList($model, 'idHotel', Equipogeneral::getListHotel1(), array('empty' => 'SELECCIONE HOTEL')); ?>
            <?php echo $form->error($model, 'nombre_hotel'); ?>
        </div>
        <div id="estado" class="span4" style=<?php echo $model->vista === 'update' && ($model->idEstatus == 1 || $model->idEstatus == 2) ? 'display' . ':' . 'inline' : 'display' . ':' . 'none' ?>;>
            <?php echo $form->labelEx($model, 'idEstatus'); ?>
            <?php $listDataEstatus = ($model->vista === 'update' && ($model->idEstatus == 1 || $model->idEstatus == 2)) ? Equipogeneral::getListEstado() : Equipogeneral::getListEstadoAllowUpdate(); ?>
            <?php echo $form->dropdownList($model, 'idEstatus', $listDataEstatus, array('empty' => 'ESTADO DEL EQUIPO')); ?>
            <?php echo $form->error($model, 'idEstatus'); ?>
        </div>

    </div>


    <div class='alert alert-info' id='datosEquipo' style='display: none;'>Datos de equipo</div>

    <div class="row-fluid" id='facturas'>
        <div id="sistemaOperativo" class="span4" style='display: none;'>
            <?php echo $form->labelEx($model, 'nombre_sistema_operativo'); ?>
            <?php echo $form->dropdownList($model, 'idSitemaOperativo', Equipogeneral::getListSistema(), array('empty' => 'SELECCIONE SISTEMA OPERATIVO')); ?>
            <?php echo $form->error($model, 'nombre_sistema_operativo'); ?>
        </div>

        <div id="memoria" class="span4" style='display: none;'>
            <?php echo $form->labelEx($model, 'idMemoriaRam'); ?>
            <?php echo $form->dropdownList($model, 'idMemoriaRam', Equipogeneral::getListMemoriaRam(), array('empty' => 'SELECCIONE MEMORIA RAM')); ?>
            <?php echo $form->error($model, 'idMemoriaRam'); ?>
        </div>

        <div id="procesador" class="span4" style='display: none;'>
            <?php echo $form->labelEx($model, 'idProcesador'); ?>
            <?php echo $form->dropdownList($model, 'idProcesador', Equipogeneral::getListProcesador(), array('empty' => 'SELECCIONE PROCESADOR')); ?>
            <?php echo $form->error($model, 'idProcesador'); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div id="discoDuro" class="span4" style='display: none;'>
            <?php echo $form->labelEx($model, 'idTipoDiscoDuro'); ?>
            <?php echo $form->dropdownList($model, 'idTipoDiscoDuro', Equipogeneral::getListTipoDiscoDuro(), array('empty' => 'SELECCIONE DISCO DURO')); ?>
            <?php echo $form->error($model, 'idTipoDiscoDuro'); ?>
        </div>
    </div>

    <div class='alert alert-info' id='datosFactura' style='display: none;'>Datos de factura</div>
    <div class="row-fluid" id='comboFactura'>
        <div class="span8">
            <p >Seleccionar si el equipo tiene más de 3 años</p>
            <input type="checkbox" name="checkbox" id="checkbox" /> 

        </div>
    </div>
    <div class="row-fluid">
        <div id="proveedor" class="span4" style='display: none;'>
<?php echo $form->labelEx($model, 'nombre_proveedor'); ?>
<?php echo $form->dropdownList($model, 'idProveedores', Equipogeneral::getListProveedor(), array('empty' => 'SELECCIONE PROVEEDOR')); ?>
            <?php echo $form->error($model, 'nombre_proveedor'); ?>
        </div>
        <div id="factura" class="span4" style='display: none;'>
            <?php echo $form->labelEx($model, 'factura'); ?>
<?php echo $form->textField($model, 'factura', array('size' => 50, 'maxlength' => 50, 'style' => 'text-transform:uppercase')); ?>
            <?php echo $form->error($model, 'factura'); ?>
        </div>
        <div class="span4" id="fechaCompra" style='display: none;'>
            <?php echo $form->labelEx($model, 'fechaCompra'); ?>
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'id' => 'fechaC',
    'model' => $model,
    'attribute' => 'fechaCompra',
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
            <?php echo $form->error($model, 'fechaCompra'); ?>
        </div>
    </div>

    <div class="form-actions">
<?php
$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'type' => 'primary',
    'label' => $model->isNewRecord ? 'Crear' : 'Guardar',
    'disabled' => $model->vista == 'create' ? false : (empty($model->validausuario) ? true : false),
));
?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php Yii::app()->clientScript->registerScript('textfield-disabled', "
$('#checkbox').click(function(){
  if ($(this).is(':checked'))  { 
  	   $('#factura').hide();
    $('#Equipogeneral_factura').val('0');
      $('#fechaC').val('0000-00-00');

                $('#proveedor').hide();
                $('#fechaCompra').hide(); 
         
  }else{
  	       $('#Equipogeneral_factura').val('');
      $('#fechaC').val('');  
           $('#factura').show();
                $('#proveedor').show();
                $('#fechaCompra').show();  
           


  }
});
"); ?>