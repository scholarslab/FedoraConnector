<?php echo $this->partial('datastreams/admin-header.php', array('subtitle' => 'Create Datastream | Step 2')); ?>

<div id="primary">

    <?php echo flash(); ?>

    <h2>Select the metadata format and datastreams:</h2>

    <?php echo $this->form; ?>

</div>

<?php foot(); ?>

