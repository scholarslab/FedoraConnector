<?php echo $this->partial('datastreams/admin-header.php', array('subtitle' => 'Create Datastream')); ?>

<div id="primary">

    <?php echo flash(); ?>

    <?php if (count($items) == 0): ?>

        <p>There are no items yet.</p>

    <?php else: ?>

            <table>

                <h2 class="fedora-select-item">Select an item to add a datastream to:</h2>

                <div id="simple-search-form">
                    <form id="simple-search" action="<?php echo uri('fedora-connector/datastreams/create'); ?>" method="get">
                        <fieldset>
                            <input type="text" name="search" id="search" value="<?php echo $search; ?>" class="textinput">
                            <input type="submit" name="submit_search" id="submit_search" value="Search">
                        </fieldset>
                    </form>
                </div>

                <p class="fedora-connector-search-reset"><a href="<?php echo uri('fedora-connector/datastreams/create'); ?>">Reset</a></p>

                <thead>
                    <tr>
                        <?php browse_headings(array(
                            'Title' => 'item_title',
                            'Type' => 'name',
                            'Creator' => 'creator',
                            'Date Added' => 'added',
                            'Add to Item' => null
                        )); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><a href="<?php echo public_uri('items/show/' . $item->id); ?>"><?php echo $item->item_name; ?></a></td>
                            <td><?php echo $item->name != '' ? $item->name : '<span style="font-size: 0.8em; color: gray;">[not available]</span>'; ?></td>
                            <td><?php echo $item->creator != '' ? $item->creator : '<span style="font-size: 0.8em; color: gray;">[not available]</span>'; ?></td>
                            <td><?php echo fedorahelpers_formatDate($item->added); ?></td>
                            <td><?php echo $this->partial('datastreams/datastreams-add-action.php', array('id' => $item->id)); ?></td>
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

