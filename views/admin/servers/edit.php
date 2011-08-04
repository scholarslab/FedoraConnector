<?php echo $this->partial('servers/admin-header.php', array('subtitle' => 'Servers')); ?>

<div id="primary">

    <?php echo flash(); ?>

    <h2>Edit Server "<?php echo $server->name; ?>":</h2>

    <?php echo $this->form; ?>

</div>

<?php foot(); ?>

