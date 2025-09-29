<?php
/* @var $this DepartamentoController */
/* @var $model Departamento */

$this->breadcrumbs = array(
    'Departamentos' => array('index'),
    $model->iddepartamento,
);

$this->menu = array(
    array('label' => 'Control de Departamentos', 'url' => array('index')),
    array('label' => 'Crear Departamento', 'url' => array('create')),
    array('label' => 'Actualizar Departamento', 'url' => array('update', 'id' => $model->iddepartamento)),
);
?>

<h1>Detalle del departamento <?php echo $model->nombredepartamento; ?></h1>

<div class="tab-content">
    <div class="tab-pane active" id="tab1">
        <?php
        $this->widget('bootstrap.widgets.TbDetailView', array(
            'data' => $model,
            'htmlOptions' => array('style' => 'width:500px'),
            'attributes' => array(
                'iddepartamento',
                'nombredepartamento',
                'descripciondepartamento',
                array(
                    'name' => 'nombre_gerencia',
                    'type' => 'html',
                    'value' => $model->fkgerencia->nombregerencia,
                ),
                array(
                    'name' => 'nombre_direccion',
                    'value' => $model->fkgerencia->fkdireccion->nombredireccion,
                ),
            ),
        ));
        ?>
    </div>
</div>