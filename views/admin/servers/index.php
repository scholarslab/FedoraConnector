<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * FedoraConnector Omeka plugin allows users to reuse content managed in
 * institutional repositories in their Omeka repositories.
 *
 * The FedoraConnector plugin provides methods to generate calls against Fedora-
 * based content disemminators. Unlike traditional ingestion techniques, this
 * plugin provides a facade to Fedora-Commons repositories and records pointers
 * to the "real" objects rather than creating new physical copies. This will
 * help ensure longer-term durability of the content streams, as well as allow
 * you to pull from multiple institutions with open Fedora-Commons
 * respositories.
 *
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      Ethan Gruber <ewg4x@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      Wayne Graham <wayne.graham@virginia.edu>
 * @author      Eric Rochester <err8n@virginia.edu>
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */

head(array(
    'title'         => 'Manage Fedora Servers',
    'bodyclass'     => 'primary',
    'content_class' => 'horizontal-nav'
));
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
                    <th>URL</th>
                    <th>Version</th>
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
                    <td><?php echo html_escape($server['version']); ?></td>
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

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
