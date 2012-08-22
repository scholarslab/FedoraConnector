<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * FedoraConnector Omeka plugin allows users to reuse content managed in
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Configuration form.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?>

<div class="field">

    <label for="fedora_connector_server">Omitted Datastreams:</label>

    <textarea name="fedora_connector_omitted_datastreams" id="fedora_connector_omitted_datastreams" rows="1" cols="40"><?php echo get_option('fedora_connector_omitted_datastreams'); ?></textarea>

    <p class="explanation">List datastream IDs, comma-separated, that should be 
        omitted from the datastream selection checkbox list and object metadata 
        dropdown menu. Default: RELS-EXT,RELS-INT,AUDIT.</p>

</div>
