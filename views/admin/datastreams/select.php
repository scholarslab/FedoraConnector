<?php
    head(array('title' => 'Import Fedora Datastream', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>Import Fedora Datastream</h1>
<h2>Step 1: Select Datastreams</h2>

<div id="primary">
	<?php
            if (!empty($err)) {
                echo '<p class="error">' . html_escape($err) . '</p>';
            }
        ?>
        <p>Select the datastreams listed below you wish to be imported for the selected item.</p>
     <?php echo $form; ?>
</div>

<?php 
    foot(); 
?>
