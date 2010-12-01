<?php
    head(array('title' => 'Fedora Datastreams', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>Fedora Datastreams</h1>

<div id="primary">
	<?php echo flash(); ?>
	<?php
            if (!empty($err)) {
                echo '<p class="error">' . html_escape($err) . '</p>';
            }
        ?>
	<?php if ($count == 0): ?>
	    <div id="no-items">
	    <p>There are no datastreams yet.</p>
	    </div>    
    <?php else: ?>
		<div class="pagination"><?php echo pagination_links(); ?></div>
	    <table class="simple" cellspacing="0" cellpadding="0">
	            <thead><th>ID</th><th>PID</th><th>Datastream ID</th><th>mime-type</th><th>Object Metadata</th><th>Delete?</th></thead>
	            <tbody>
	                <?php foreach($datastreams as $datastream): ?>
	                <tr>
						<td><?php echo html_escape($datastream['id']); ?></td>
	                    <td><?php echo html_escape($datastream['pid']); ?></td>
	                    <td><?php echo html_escape($datastream['datastream']); ?></td>
	                    <td><?php echo html_escape($datastream['mime_type']); ?></td>
	                    <td><?php echo html_escape($datastream['metadata_stream']); ?></td>
	                    <td><?php echo '<a href="' . $deleteUrl . '?id=' . $datastream['id'] . '">Delete</a>'; ?></td>						
	                </tr>
	                <?php endforeach; ?>
	            </tbody>
	        </table>    
    <?php endif; ?>
</div>

<?php 
    foot(); 
?>
