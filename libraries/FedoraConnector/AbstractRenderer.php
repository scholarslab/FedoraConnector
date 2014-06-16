<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


abstract class FedoraConnector_AbstractRenderer
{


    /**
     * This tests whether this renderer can display a datastream.
     *
     * @param Omeka_Record $datastream The data stream.
     * @return boolean True if this can display the datastream.
     */
    abstract function canDisplay($datastream);


    /**
     * This displays a datastream.
     *
     * @param Omeka_Record $datastream The data stream.
     * @return string The display HTML for the datastream.
     */
    abstract function display($datastream, $params = null);


}
