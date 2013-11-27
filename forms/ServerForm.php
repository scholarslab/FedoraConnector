<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnector_Form_Server extends Omeka_Form
{


    /**
     * Build the form.
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

        // Submit.
        $this->addElement('submit', 'submit', array(
            'label' => __('Save')
        ));

        // Group the data fields.
        $this->addDisplayGroup(array(
            'name', 'url'
        ), 'server');

        // Group the submit button sparately.
        $this->addDisplayGroup(array('submit'), 'submit_button');

    }


}
