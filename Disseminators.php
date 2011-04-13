<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FedoraConnector Omeka plugin allows users to reuse content managed in
 * institutional repositories in their Omeka repositories.
 *
 * The FedoraConnector plugin provides methods to generate calls against Fedora-
 * based content disemminators. Unlike traditional ingestion techniques, this
 * plugin provides a facade to Fedora-Commons repositories and records pointers
 * to the "real" objects rather than creating new physical copies. This will
 * help ensure longer-term durability of the content streams, as well as allow
 * you to pull from multiple institutions with open Fedora-Commons
 * respositories.
 *
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      Ethan Gruber <ewg4x@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      Wayne Graham <wayne.graham@virginia.edu>
 * @author      Eric Rochester <err8n@virginia.edu>
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */

// XXX -> view


/**
 * This creates the HTML to display a JPEG image represented in a datastream.
 *
 * This ignores the options.
 *
 * @param Omeka_Record $datastream This is the datastream representing the 
 * JPEG.
 * @param array $options Options for displaying the image.
 *
 * @return string The HTML to display the JPEG.
 */
function fedora_disseminator_imagejpeg($datastream, $options)
{
	$url = fedora_connector_content_url($datastream);
	$html = '<img alt="image" src="' . $url . '"/>';
	return $html;
}

/**
 * This creates the HTML to display a JPEG-2 image represented in a datastream.
 *
 * This pays attention to these options:
 *
 * <dl>
 * <dt>size</dt> <dd>The size to display the image. One of 'thumb', 'screen', 
 * or anything else (for default).
 * </dl>
 *
 * @param Omeka_Record $datastream This is the datastream representing the 
 * image.
 * @param array $options Options for displaying the image.
 *
 * @return string The HTML to display the image.
 */
function fedora_disseminator_imagejp2($datastream,$options)
{
    $server = fedora_connector_get_server($datastream);
    $url = $server . 'get/' . $datastream->pid . '/djatoka:jp2SDef/getRegion';

    switch($options['size']) {
    case 'thumb':
        $html = '<img alt="image" src="' . $url . '?scale=120,120"/>';
        break;
    case 'screen':
        $html = '<img alt="image" src="' . $url . '?scale=600,600"/>';
        break;
    default:
        $html = '<img alt="image" src="' . $url . '?scale=400,400"/>';
    }	

    return $html;
}

/**
 * This creates the HTML to display a TEI-encoded XML document represented in a 
 * datastream.
 *
 * This just dispatches to the TeiDisplay plugin.
 *
 * XXX: Oh, wow. This needs to reflect on whether TeiDisplay is installed, and 
 * only exist if it does. I probably need to move these display functions into 
 * a small framework that can tell whether they need to be used or not.
 *
 * @param Omeka_Record $datastream This is the datastream representing the 
 * document.
 * @param array $options Options for displaying the image.
 *
 * @return string The HTML to display the image.
 */
function fedora_disseminator_tei($datastream,$options)
{
    $teiFiles = get_db()
        ->getTable('TeiDisplay_Config')
        ->findbySql('item_id = ?', array($datastream->item_id));

    ob_start();
	foreach ($teiFiles as $teiFile) {
		echo render_tei_file($teiFile->id, $_GET['section']);
	}
    $html = ob_get_contents();
    ob_end_clean();

	return $html;
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
?>
