<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Server form.
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
 * @subpackage  fedoraconector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnector_Form_Server extends Omeka_Form
{

    private $_server;

    /**
     * Build the form.
     *
     * @return void.
     */
    public function init()
    {

        parent::init();

        $this->setMethod('post');
        $this->setAttrib('id', 'server-form');
        $this->addElementPrefixPath('FedoraConnector', dirname(__FILE__));

        // Title.
        $this->addElement('text', 'name', array(
            'label'         => __('Name'),
            'description'   => __('An internal (non-public) identifier for the server.'),
            'size'          => 40,
            'required'      => true,
            'validators'    => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'messages' => array(
                            Zend_Validate_NotEmpty::IS_EMPTY => __('Enter a name.')
                        )
                    )
                )
            )
        ));

        // URL.
        $this->addElement('text', 'url', array(
            'label'         => __('URL'),
            'description'   => __('The location of the server.'),
            'size'          => 40,
            'required'      => true,
            'validators'    => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'messages' => array(
                            Zend_Validate_NotEmpty::IS_EMPTY => __('Enter a URL.')
                        )
                    )
                ),
                array('validator' => 'IsUrl', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'messages' => array(
                            FedoraConnector_Validate_IsUrl::INVALID_URL => ('Enter a valid URL.')
                        )
                    )
                )
            )
        ));

        // Active.
        $this->addElement('checkbox', 'active', array(
            'label'         => __('Active'),
            'description'   => __('Should this server be used to handle new GeoTiff uploads?'),
            'checked'       => true
        ));

        // Submit.
        $this->addElement('submit', 'submit', array(
            'label' => __('Save')
        ));

        // Group the data fields.
        $this->addDisplayGroup(array(
            'name', 'url', 'active'
        ), 'server');

        // Group the submit button sparately.
        $this->addDisplayGroup(array('submit'), 'submit_button');

    }

    /**
     * Set the active server record.
     *
     * @return void.
     */
    public function setServer(FedoraConnectorServer $server)
    {
        $this->_server = $server;
    }

}
