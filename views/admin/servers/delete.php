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

<?php echo $this->partial('servers/admin-header.php', array('subtitle' => 'Servers')); ?>

<div id="primary">

    <?php echo flash(); ?>

    <h2>Are you sure you want to delete server "<?php echo $name; ?>"?</h2>

    <form enctype="application/x-www-form-urlencoded" method="post"><dl class="zend_form">
        <dd id="delete_submit-element">
        <input type="submit" name="deleteconfirm_submit" id="delete_submit" value="Delete"></dd></dl>
    </form>

</div>

<?php foot(); ?>
