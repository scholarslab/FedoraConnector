<?php
    head(array('title' => 'Manage Fedora Servers', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>Manage Fedora Servers</h1>
<h2>Edit Server</h2>
<div id="primary">
	<?php
            if (!empty($err)) {
                echo '<p class="error">' . html_escape($err) . '</p>';
            }
        ?>
     <?php echo $form; ?>
     <?php if ($count > 1) { ?>
		<p id="delete_item_link">
			<a class="delete delete-item" href="<?php echo html_escape(uri('fedora-connector/servers/delete?id=')) . $id ; ?>">Delete This Server</a>
		</p>
	<?php } ?>
</div>

<?php 
    foot(); 
?>
