<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

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
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */

?>

<?php

class FedoraConnector_SettingsController extends Omeka_Controller_Action
{

    /**
     * Set table aliases.
     *
     * @return void
     */
    public function init()
    {

        $this->_elementSetTable = $this->getTable('ElementSet');
        $this->_elementTable = $this->getTable('Element');

    }

    /**
     * Redirect by default to setdefaults action.
     *
     * @return void
     */
    public function indexAction()
    {

        // Ping to browse by default.
        $this->_forward('setdefaults', 'settings', 'fedora-connector');

    }

    /**
     * Show instructions about how to set defaults and the stack of select
     * drop-downs for each of the DC fields.
     *
     * @return void
     */
    public function setdefaultsAction()
    {

        $dublinCoreSet = $this->_elementTable->findBySet('Dublin Core');

        $this->view->elements = $dublinCoreSet;
        $this->view->defaultImportBehavior = get_option('fedora_connector_default_import_behavior');
        $this->view->defaultAddToBlank = get_option('fedora_connector_default_add_to_blank_behavior');
        $this->view->importBehavior = $this->getTable('FedoraConnectorImportBehaviorDefault');
        $this->view->addToBlank = $this->getTable('FedoraConnectorAddToBlankDefault');

    }

    /**
     * Show instructions about how to set defaults and the stack of select
     * drop-downs for each of the DC fields.
     *
     * @return void
     */
    public function savesettingsAction()
    {

        if ($this->_request->isPost()) {

            $post = $this->_request->getPost();

            // Set defaults.
            set_option('fedora_connector_default_import_behavior', $post['behavior_default']);
            set_option('fedora_connector_default_add_to_blank_behavior', $post['addifempty_default']);

            foreach ($post['behavior'] as $field => $behavior) {

                // Query for the behavior record and the DC element.
                $behaviorRecord = $this->getTable('FedoraConnectorImportBehaviorDefault')->getBehavior($field);
                $dcElement = $this->getTable('Element')
                    ->findByElementSetNameAndElementName('Dublin Core', $field);

                if ($behavior != 'default') {

                    // If the record exists, update it.
                    if ($behaviorRecord != false) {
                        $behaviorRecord->behavior = $behavior;
                        $behaviorRecord->save();
                    }

                    // Otherwise, create a new record.
                    else {
                        $newBehaviorRecord = new FedoraConnectorImportBehaviorDefault;
                        $newBehaviorRecord->behavior = $behavior;
                        $newBehaviorRecord->element_id = $dcElement->id;
                        $newBehaviorRecord->save();
                    }

                }

                else {

                    // If the record exists, delete it.
                    if ($behaviorRecord != false) {
                        $behaviorRecord->delete();
                    }

                }

            }

            foreach ($post['addifempty'] as $field => $behavior) {

                // Query for the behavior record and the DC element.
                $behaviorRecord = $this->getTable('FedoraConnectorAddToBlankDefault')->getBehavior($field);
                $dcElement = $this->getTable('Element')
                    ->findByElementSetNameAndElementName('Dublin Core', $field);

                if ($behavior != 'default') {

                    // If the record exists, update it.
                    if ($behaviorRecord != false) {
                        $behaviorRecord->add_to_blank = ($behavior == 'yes') ? true : false;
                        $behaviorRecord->save();
                    }

                    // Otherwise, create a new record.
                    else {
                        $newBehaviorRecord = new FedoraConnectorAddToBlankDefault;
                        $newBehaviorRecord->add_to_blank = ($behavior == 'yes') ? true : false;
                        $newBehaviorRecord->element_id = $dcElement->id;
                        $newBehaviorRecord->save();
                    }

                }

                else {

                    // If the record exists, delete it.
                    if ($behaviorRecord != false) {
                        $behaviorRecord->delete();
                    }

                }

            }

            $this->flashSuccess('Settings saved.');
            $this->_forward('setdefaults', 'settings', 'fedora-connector');

        } else {

            $this->_forward('setdefaults', 'settings', 'fedora-connector');

        }

    }

}
