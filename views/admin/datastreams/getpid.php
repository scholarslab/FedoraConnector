<?php echo $this->partial('datastreams/admin-header.php', array('subtitle' => 'Create Datastream for "' . $item->item_name . '" | Step 1')); ?>

<div id="primary">

    <?php echo flash(); ?>

    <h2>Select the server and enter the PID:</h2>

    <?php echo $this->form; ?>

</div>

<?php foot(); ?>

