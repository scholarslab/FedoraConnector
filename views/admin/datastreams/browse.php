<?php echo $this->partial('datastreams/admin-header.php', array('subtitle' => 'Datastreams')); ?>

<div id="primary">

    <p id="add-item" class="add-button"><a class="add" href="<?php echo html_escape(uri('fedora-connector/datastreams/create')); ?>">Add Datastreams</a></p>

    <?php echo flash(); ?>

    <?php if (count($datastreams) == 0): ?>

        <p>There are no datastreams yet.</p>

    <?php else: ?>

            <table class="fedora">
                <thead>
                    <tr>
                        <?php browse_headings(array(
                            'Datastream' => 'datastream',
                            'PID' => 'pid',
                            'Mime Type' => 'mime_type',
                            'Item' => 'parent_item',
                            'Server' => 'server_name',
                            // 'Object Metadata' => 'metadata_stream',
                            'Preview' => null,
                            'Actions' => null
                        )); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datastreams as $datastream): ?>
                        <tr>
                            <td width="220">
                                <strong><a href="<?php echo $datastream->getUrl(); ?>"><?php echo $datastream->getNode()->getAttribute('label'); ?></a></strong>
                                <br />
                                <span style="color: gray; font-size: 0.8em">id: <?php echo $datastream->datastream; ?></span>
                                <br />
                                <span style="color: gray; font-size: 0.8em">format: <?php echo $datastream->metadata_stream; ?></span>
                            </td>
                            <td width="100" class="fedora-td-small"><?php echo $datastream->pid; ?></td>
                            <td width="80" class="fedora-td-small"><?php echo $datastream->mime_type; ?></td>
                            <td class="fedora-td-small"><a href="<?php echo public_uri('items/show/' . $datastream->item_id); ?>"><?php echo $datastream->parent_item; ?></a></td>
                            <td class="fedora-td-small"><a href="<?php echo uri('fedora-connector/servers/edit/' . $datastream->server_id); ?>"><?php echo $datastream->server_name; ?></a></td>
                            <!-- <td><?php echo $datastream->metadata_stream; ?></td> -->
                            <td style="text-align: center;"><?php echo $datastream->renderPreview(); ?></td>
                            <td width="60"><?php echo $this->partial('datastreams/datastreams-actions.php', array('id' => $datastream->datastream_id)); ?></td>
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
