<?php
    //Alertas es un array dentro de otro.
    //El primer array es el tipo 'error' por ejemplo, esa es la key. Dentro tiene los mensajes.
    //Primero recorremos los tipos de alerta y luego los mensajes
    foreach ($alertas as $key => $mensajes):
        foreach ($mensajes as $mensaje):

?>
        <div class="alerta <?php echo $key; ?>">
            <?php echo $mensaje; ?>
        </div>
<?php
        endforeach;

    endforeach;
?>




