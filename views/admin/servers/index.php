<?php
    head(array('title' => 'Manage Fedora Servers', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>Manage Fedora Servers</h1>
<p id="add-item" class="add-button"><a class="add" href="<?php echo html_escape(uri('fedora-connector/servers/create')); ?>">Add a Server</a></p>
<div id="primary">

	<?php
            if (!empty($err)) {
                echo '<p class="error">' . html_escape($err) . '</p>';
            }
        ?>
	<?php echo flash(); ?>
     <?php if ($count == 0): ?>
	    <div id="no-items">
	    <p>There are no agents yet.
	    
	    <?php  if (get_acl()->checkUserPermission('FedoraConnector_Server', 'index')): ?>
	          Why don&#8217;t you <?php echo link_to('servers', 'create', 'add one'); ?>?</p>
	    </div>
    <?php endif; ?>
    <?php else: ?>
		<div class="pagination"><?php echo pagination_links(); ?></div>
	    <table class="simple" cellspacing="0" cellpadding="0">
	            <thead>
	                <tr>
	                	<th>ID</th>
	                    <th>Name</th>
	                    <th>Url</th>
	                    <th>Default</th>
						<th>Edit?</th>
	                </tr>
	            </thead>
	            <tbody>
	                <?php foreach($servers as $server): ?>
	                <tr>
						<td><?php echo html_escape($server['id']); ?></td>
	                    <td><?php echo html_escape($server['name']); ?></td>
	                    <td><?php echo html_escape($server['url']); ?></td>
	                    <td><?php if ($server['is_default'] == 1){ echo 'Yes'; } ?></td>
						<td><a href="<?php echo html_escape(uri('fedora-connector/servers/edit')); ?>?id=<?php echo $server['id']; ?>" class="edit">Edit</a></td>
	                </tr>
	                <?php endforeach; ?>
	            </tbody>
	        </table>    
    <?php endif; ?>
</div>

<?php 
    foot(); 
?>
