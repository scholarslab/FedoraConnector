<?php echo $this->partial('datastreams/admin-header.php', array('subtitle' => 'Create Datastream | Step 1')); ?>

<div id="primary">

    <?php echo flash(); ?>

    <h2>Select the datastreams and metadata format:</h2>

    <?php echo $this->form; ?>

</div>

<?php foot(); ?>

