<?php echo $this->partial('servers/admin-header.php', array('subtitle' => 'Servers')); ?>

<div id="primary">

    <p id="add-item" class="add-button"><a class="add" href="<?php echo html_escape(uri('fedora-connector/servers/create')); ?>">Add a Server</a></p>

    <?php echo flash(); ?>

    <?php if (count($servers) == 0): ?>

        <p>There are no servers. <a href="<?php echo html_escape(uri('fedora-connector/servers/create')); ?>">Add one</a>!</p>

    <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <?php browse_headings(array(
                            'Name' => 'name',
                            'URL' => 'url',
                            'Status' => null,
                            'Version' => null,
                            'Default?' => 'is_default',
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
                            <?php
                                if ($server->isOnline()) {
                                    echo '<span style="font-size: 0.8em; color: green;">Online</span>';
                                } else {
                                    echo '<span style="font-size: 0.8em; color: red;">Offline</span>';
                                }
                            ?>
                            </td>
                            <td>
                            <?php
                                if ($server->isOnline()) {
                                    echo $server->getVersion();
                                } else {
                                    echo '<span style="font-size: 0.8em; color: gray;">[not available]</span>';
                                }
                            ?></td>
                            <td><?php if ($server->is_default) { echo 'Yes'; } ?></td>
                            <td><?php echo $this->partial('datastreams/servers-actions.php', array('id' => $server->id)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

    <?php endif; ?>

    <div class="pagination">

      <?php echo pagination_links(array('scrolling_style' => 'All', 
      'page_range' => '5',
      'partial_file' => 'common/pagination_control.php',
      'page' => $current_page,
      'per_page' => $results_per_page,
      'total_results' => $total_results)); ?>

    </div>

</div>

<?php foot(); ?>

