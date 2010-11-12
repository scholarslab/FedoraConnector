<?php
    head(array('title' => 'Delete Fedora Datastream', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>Delete Fedora Datastream</h1>

<div id="primary">
	<?php echo flash(); ?>
	<?php
            if (!empty($err)) {
                echo '<p class="error">' . html_escape($err) . '</p>';
            }
        ?>       
</div>

<?php 
    foot(); 
?>
