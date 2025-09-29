<?php
if ($tipo == 1) {
    $this->breadcrumbs = array(
        'Servidores fisicos' => array('/servidoresfisicos'),
        'Detalle Servidor' => array('/servidoresfisicos/view', 'id' => $id),
        'Agregar Bitacora',
    );
    $this->menu = array(
        array('label' => 'Lista de Servidores fisicos', 'url' => array('/servidoresfisicos')),
    );
} else if ($tipo == 2) {
    $this->breadcrumbs = array(
        'Servidores virtuales' => array('/servidoresvirtuales'),
        'Detalle Servidor' => array('/servidoresvirtuales/view', 'id' => $id),
        'Agregar Bitacora',
    );
    $this->menu = array(
        array('label' => 'Lista de Servidores virtuales', 'url' => array('/servidoresvirtuales')),
    );
}
?>

<h2>Crear bitacora de incidencia del servidor <?php echo $nombre; ?></h2>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>