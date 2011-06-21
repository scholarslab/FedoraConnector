<?php echo $this->partial('servers/admin-header.php', array('subtitle' => 'Servers')); ?>

<div id="primary">

    <?php echo flash(); ?>

    <?php if (count($servers) == 0): ?>

        <p>There are no agents. <?php echo link_to('servers', 'create', 'Create one'); ?>!</p>

    <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <?php browse_headings(array(
                            'Name' => 'name',
                            'URL' => 'url',
                            'Version' => 'version',
                            'Default?' => null
                        )); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servers as $server): ?>
                        <tr>
                            <td><a href="<?php echo uri('fedora-connector/servers/edit/' . $server->id); ?>"><strong><?php echo $server->name; ?></strong></a></td>
                            <td><a href="<?php echo $server->url; ?>" target="_blank"><?php echo $server->url; ?></a></td>
                            <td><?php echo ($server->version != null) ? $server->version : '<span style="font-size: 0.8em; color: gray;">[not available]</span>'; ?></td>
                            <td><?php if ($server->is_default) { echo 'Yes'; } ?></td>
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

    <form method="post" action="<?php echo uri('fedora-connector/servers/create') ?>" accept-charset="utf-8">
        <?php echo submit(array('name' => 'create_server'), 'Add Server'); ?>
    </form>

</div>

<?php foot(); ?>

