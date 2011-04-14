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
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */


/**
 * This initializes a form.
 *
 * @param string $action  The action to take on the form.
 * @param string $method  The method to use submitting the form.
 * @param string $encType The enctype to use with the form. (Optional.)
 *
 * @return Zend_Form The initializes the form.
 */
function Fedora_initForm($action, $method, $encType=null)
{
    $form = new Zend_Form();

    $form->setAction($action);
    $form->setMethod($method);
    if ($encType !== null) {
        $form->setAttrib('enctype', $encType);
    }

    return $form;
}

/**
 * This adds the submit button to the form.
 *
 * @param Zend_Form $form  The form to add the button to.
 * @param string    $label The label for the button. Defaults to 'Submit.'
 * @param string    $name  The name for the button. Defaults to 'submit.'
 *
 * @return Zend_Form_Element The submit button that was added.
 */
function Fedora_Form_addSubmit($form, $label='Submit', $name='submit')
{
    $form->addElement($name, $name);

    $submit = $form->getElement($name);
    $submit->setLabel($label);

    return $submit;
}

/**
 * This adds a hidden element to the form.
 *
 * @param Zend_Form $form  The form to add the button to.
 * @param string    $name  The name of the input element.
 * @param string    $value The value of the input element.
 *
 * @return Zend_Form_Element_Hidden The hidden form element.
 */
function Fedora_Form_addHidden($form, $name, $value)
{
    $hidden = new Zend_Form_Element_Hidden($name);
    $hidden->setValue($value);

    $form->addElement($hidden);

    return $hidden;
}

/**
 * This adds a text element to the form.
 *
 * @param Zend_Form $form     The form to add the text input to.
 * @param string    $name     The name of the input element.
 * @param string    $label    The label for the element.
 * @param string    $value    The value of the input element.
 * @param boolean   $required Is the value required? Default is false.
 *
 * @return Zend_Form_Element_Text The text element.
 */
function Fedora_Form_addText($form, $name, $label, $value, $required=false) {
    $text = new Zend_Form_Element_Text($name);
    $text->setLabel($label);
    $text->setValue($value);
    $text->setRequired($required);

    $form->addElement($text);

    return $text;
}

/**
 * This adds a checkbox element to the form.
 *
 * @param Zend_Form $form     The form to add the checkbox to.
 * @param string    $name     The name of the checkbox.
 * @param string    $label    The label for the checkbox.
 * @param string    $value    The value for the checkbox.
 * @param boolean   $required Is the value required? Default is false.
 *
 * @return Zend_Form_Element_Checkbox
 */
function Fedora_Form_addCheckbox(
    $form, $name, $label, $value, $required=false
) {
    $cbox = new Zend_Form_Element_Checkbox($name);
    $cbox->setLabel($label);
    $cbox->setValue($value);
    $cbox->setRequired($required);

    $form->addElement($cbox);

    return $cbox;
}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
