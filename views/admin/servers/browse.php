<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Add server.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?>

<?php
$title = __('Fedora Connector | Browse Servers');
echo head(array('content_class' => 'fedora', 'title' => $title));
?>

<div id="primary">

    <?php echo flash(); ?>

    <?php if ($servers): ?>

        <a class="add small green button" href="<?php echo url('fedora-connector/servers/add'); ?>">
            <?php echo __('Register a Server'); ?>
        </a>

        <table>
            <thead>
                <tr>
                    <?php echo browse_sort_links(array(
                        'Name' => 'name',
                        'URL' => 'url',
                        'Status' => null,
                        'Version' => null,
                        'Actions' => null
                    ), array('link_tag' => 'th scope="col"')); ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servers as $server): ?>
                    <tr>
                        <td><a href="<?php echo url('fedora-connector/servers/edit/' . $server->id); ?>"><strong><?php echo $server->name; ?></strong></a></td>
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

        <h2><?php echo __('No Fedora servers have been registered.'); ?></h2>
        <p><?php echo __('Get started by adding a new one!'); ?></p>

        <a class="add big green button"
            href="<?php echo url('fedora-connector/servers/add'); ?>">
            <?php echo __('Register a Server'); ?>
        </a>

    <?php endif; ?>

</div>

<?php echo foot(); ?>
