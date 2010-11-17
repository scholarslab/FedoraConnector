<?php
    head(array('title' => 'Manage Fedora Servers', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>Manage Fedora Servers</h1>

<div id="primary">
	<?php echo flash(); ?>
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
