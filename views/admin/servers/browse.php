<?php

/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>

<?php
  echo head(array(
    'title' => __('Fedora Connector | Browse Servers'),
    'bodyclass' => 'browse'
  ));
?>

<div id="primary">

  <?php echo flash(); ?>

  <?php if ($servers): ?>

    <a class="add small green button"
      href="<?php echo url('fedora-connector/servers/add'); ?>">
      <?php echo __('Register a Server'); ?>
    </a>

    <table>

      <thead>
        <tr>
          <?php echo browse_sort_links(array(
            'Name'    => 'name',
            'URL'     => 'url',
            'Status'  => null,
            'Version' => null,
            'Actions' => null
          ), array('link_tag' => 'th scope="col"')); ?>
        </tr>
      </thead>

      <tbody>

        <?php foreach ($servers as $s): ?>
          <tr>

            <!-- Title. -->
            <td>
              <a href="<?php echo url('fedora-connector/servers/edit/'.$s->id); ?>">
                <strong><?php echo $s->name; ?></strong>
              </a>
            </td>

            <!-- URL. -->
            <td>
              <a href="<?php echo $s->url; ?>" target="_blank">
                <?php echo $s->url; ?>
              </a>
            </td>

            <!-- Status. -->
            <td>
              <?php if ($s->isOnline()): ?>
                <span class="status online">Online</span>
              <?php else: ?>
                <span class="status offline">Offline</span>
              <?php endif; ?>
            </td>

            <!-- Version. -->
            <td>
              <?php if ($s->isOnline()): ?>
                <span><?php echo $s->getVersion(); ?></span>
              <?php else: ?>
                <span class="status unavailable">[not available]</span>
              <?php endif; ?>
            </td>

            <!-- Actions. -->
            <td>
              <?php echo $this->partial('servers/_action_buttons.php', array(
                'uriSlug' => 'fedora-connector', 'server'  => $s
              )); ?>
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
