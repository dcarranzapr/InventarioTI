<?php
/* @var $this EquipogeneralController */
/* @var $model Equipogeneral */

$this->breadcrumbs = array(
    'Equipogenerals' => array('index'),
    'Transferencia entre hoteles',
);

$this->menu = array(
    array('label' => 'Captura de inventario', 'url' => array('create')),
    array('label' => 'Control de inventario', 'url' => array('index')),
    array('label' => 'Aceptar transferencia entre hoteles', 'url' => array('autorizaciones')),
);
?>

<h1>Tranferencia de equipo entre hoteles</h1>




<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'equipogeneral-grid',
    'dataProvider' => $model->searchCambio(),
    'filter' => $model,
    'type' => 'striped bordered condensed hover',
    'enablePagination' => true,
    'ajaxUpdate' => true,
    'htmlOptions' => array('style' => 'word-wrap:break-word;'),
    'columns' => array(
        array(
            'name' => 'nombre_hotel',
            'value' => '$data->idHotel0->nombreHotel',
            'filter' => Equipogeneral::getListHotel(),
            'htmlOptions' => array('style' => 'width:90px'),
        ),
        array(
            'name' => 'nombre_tipo',
            'value' => '$data->idTipoEquipo0->nombreTipoEquipo',
            'filter' => Equipogeneral::getListTipo1(),
            'htmlOptions' => array('style' => 'width:80px'),
        ),
        array(
            'name' => 'nombre_marca',
            'value' => '$data->idMarca0->nombremarca',
            'filter' => $model->nombre_tipo ?
                    Equipogeneral::getListprueba($model->nombre_tipo) :
                    Equipogeneral::getListprueba(),
            'htmlOptions' => array('style' => 'width:70px'),
        ),
        array(
            'name' => 'nombre_modelo',
            'value' => '$data->idModelo0->nombremodelo',
            'filter' => $model->nombre_marca ?
                    CHtml::listData(Modelo::model()->with('fkidMarca0')->findAllByAttributes(
                                    array(), "nombremarca = :marca", array(':marca' => $model->nombre_marca)), 'nombremodelo', 'nombremodelo') :
                    CHtml::listData(Modelo::model()->with('fkidMarca0')->findAll(), 'nombremodelo', 'nombremodelo'),
            'htmlOptions' => array('style' => 'width:90px'),
        ),
        array(
            'name' => 'numeroSerie',
            'htmlOptions' => array('style' => 'width:60px'),
        ),
        /*
          array(
          'name'=>'nombrePC',
          'htmlOptions'=>array('style'=>'width:50px'),
          'headerHtmlOptions'=>array('style'=>'width:50px'),
          ),

          array(
          'name'=>'fechaCompra',
          'htmlOptions'=>array('style'=>'width:50p'),
          ),
          array(
          'name'=>'factura',
          'htmlOptions'=>array('style'=>'width:50'),
          ),



          'id',
          'idModelo',
          'idProveedores',
          'idEstatus',
          'idTipoEquipo',
          'idHotel',
          'idMarca',
          'idProcesador',


          'idSitemaOperativo',

          'fechaIngreso',

          'discoDuro',
          'memoria',

          'capturaColaboradorId',
          'Plataforma_idPlataforma',
         */
        array(
            'class' => 'editable.EditableColumn',
            'name' => 'idHotelCambio',
            'headerHtmlOptions' => array('style' => 'width: 100px'),
            'editable' => array(
                'type' => 'select',
                'url' => $this->createUrl('Equipogeneral/updateEquipo'),
                'source' => $this->createUrl('Equipogeneral/getListHotel'),
                'options' => array(//custom display 
                ),
                //onsave event handler 
                'onSave' => 'js: function(e, params) {
                      console && console.log("saved value: "+params.newValue);
                 }',
                'success' => 'function(data){
	                	$.fn.yiiGridView.update("equipogeneral-grid");

	                               
	                                }',
            //source url can depend on some parameters, then use js function:
            /*
              'source' => 'js: function() {
              var dob = $(this).closest("td").next().find(".editable").text();
              var username = $(this).data("username");
              return "?r=site/getStatuses&user="+username+"&dob="+dob;
              }',
              'htmlOptions' => array(
              'data-username' => '$data->user_name'
              )
             */
            )
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Acciones',
            'template' => '{baja}',
            'htmlOptions' => array('style' => 'width:40px'),
            'buttons' => array(
                'baja' => array(
                    'visible' => '$data->idHotelCambio!=null',
                    'label' => 'Cancelar cambio',
                    'icon' => 'trash',
                    'click' => "function(){
                                    $.fn.yiiGridView.update('equipogeneral-grid', {
                                        type:'POST',
                                        url:$(this).attr('href'),
                                 
                                    })
                                 
                              }
                              ",
                    'url' => 'Yii::app()->controller->createUrl("cancelarCambio",array("id"=>$data->id))',
                ),
            ),
        ),
    ),
    'pagerCssClass' => 'pagination pagination-centered',
));
?>

