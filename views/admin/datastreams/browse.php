<?php echo $this->partial('datastreams/admin-header.php', array('subtitle' => 'Datastreams')); ?>

<div id="primary">

    <?php echo flash(); ?>

    <?php if (count($datastreams) == 0): ?>

        <p>There are no datastreams yet.</p>

    <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <?php browse_headings(array(
                            'PID' => 'pid',
                            'Item' => 'parent_item',
                            'Server' => 'server_name',
                            'Datastream' => 'datastream',
                            'Object Metadata' => 'metadata_stream'
                        )); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datastreams as $datastream): ?>
                        <tr>
                            <td><?php echo $datastream->pid; ?></td>
                            <td><a href="<?php echo public_uri('items/show/' . $datastream->item_id); ?>"><?php echo $datastream->parent_item; ?></a></td>
                            <td><a href="<?php echo uri('fedora-connector/servers/edit/' . $datastream->server_id); ?>"><?php echo $datastream->server_name; ?></a></td>
                            <td><?php echo $datastream->datastream; ?></td>
                            <td><?php echo $datastream->metadata_stream; ?></td>
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
<!--
    <form method="post" action="<?php echo uri('fedora-connector/servers/create') ?>" accept-charset="utf-8">
        <?php echo submit(array('name' => 'create_server'), 'Add Server'); ?>
    </form>
-->
</div>

<?php foot(); ?>

