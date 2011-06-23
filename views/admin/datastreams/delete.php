<?php echo $this->partial('datastreams/admin-header.php', array('topnav' => 'create', 'subtitle' => 'Delete Datastream')); ?>

<div id="primary">

    <?php echo flash(); ?>

    <h2>Are you sure you want to delete the datastream "<?php echo $datastream->datastream; ?>" (PID <?php echo $datastream->pid ?>)?</h2>
    <p>The files themselves won't be deleted - just the record of the grouping of the files represented by the collection.</p>

    <form action="<?php echo uri('/fedora-connector/datastreams/' . $datastream->id . '/delete'); ?>" method="post" class="button-form">
      <input type="hidden" name="confirm" value="true" />
      <input type="submit" value="Delete" class="fedora-delete">
    </form>

</div>

<?php foot(); ?>
