<?php

/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>

<a href="<?php echo url($uriSlug . '/servers/edit/' . $server->id); ?>" class="edit"><?php echo __('Edit'); ?></a>
<a href="<?php echo url($uriSlug . '/servers/delete-confirm/' . $server->id); ?>" class="delete-confirm delete"><?php echo __('Delete'); ?></a>
