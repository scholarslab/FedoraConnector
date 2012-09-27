<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Main portal view for Neatline.
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
 * @subpackage  neatline
 * @author      Scholars' Lab <>
 * @author      Bethany Nowviskie <bethany@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?>

<?php
$title = __('Fedora Connector | Browse Servers');
head(array('content_class' => 'fedora', 'title' => $title));
?>

<?php echo $this->partial('servers/_header.php', array(
    'title' => $title,
    'add_button_uri' => 'fedora-connector/servers/add',
    'add_button_text' => __('Add a Server')
)); ?>

<div id="primary">

    <?php echo flash(); ?>

    <?php if ($servers): ?>

        <table>
            <thead>
                <tr>
                    <?php browse_headings(array(
                        'Name' => 'name',
                        'URL' => 'url',
                        'Status' => null,
                        'Version' => null,
                        'Actions' => null
                    )); ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servers as $server): ?>
                    <tr>
                        <td><a href="<?php echo uri('fedora-connector/servers/edit/' . $server->id); ?>"><strong><?php echo $server->name; ?></strong></a></td>
                        <td><a href="<?php echo $server->url; ?>" target="_blank"><?php echo $server->url; ?></a></td>
                        <td>
                            <?php if ($server->isOnline()): ?><span class="online status">Online</span>
                            <?php else: ?><span class="offline status">Offline</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($server->isOnline()): echo $server->getVersion(); ?>
                            <?php else: ?><span class="unavailable status">[not available]</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $this->partial('servers/_action_buttons.php', array(
                                'uriSlug' => 'fedora-connector',
                                'server' => $server)); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>

        <p class="neatline-alert"><?php echo __('There are no Fedora servers yet.'); ?>
        <a href="<?php echo uri('fedora-connector/servers/add'); ?>"><?php echo __('Create one!'); ?></a>
        </p>

    <?php endif; ?>

</div>

<?php foot(); ?>
