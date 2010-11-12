<?php
    head(array('title' => 'Import Fedora Datastream', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>Import Fedora Datastream</h1>
<h2>Step 1: Select PID</h2>

<div id="primary">
	<?php
            if (!empty($err)) {
                echo '<p class="error">' . html_escape($err) . '</p>';
            }
        ?>
     <?php echo $form; ?>
</div>

<?php 
    foot(); 
?>
