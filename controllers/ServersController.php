<?php

/**
 * @copyright  Scholars' Lab 2010
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    $Id:$
 * @package VraCoreElements
 * @author Ethan Gruber: ewg4x at virginia dot edu
 */

class FedoraConnector_ServersController extends Omeka_Controller_Action
{  
    /*public function createAction(){
    	$agent = array();
    	$form = $this->agentForm($agent);
		$this->view->form = $form;
    }*/
    
	public function indexAction(){
            $db = get_db();
            $item_id = $this->_getParam('id');
            $form = $this->getPidForm($item_id);
            $this->view->id = $item_id;
            $this->view->form = $form;
    }

    public function selectAction() 
    {
    	$form = $this->getPidForm();
    	
		if ($_POST) {
    		if ($form->isValid($this->_request->getPost())) {    
    			//get posted values		
				$uploadedData = $form->getValues();
				$item_id = $uploadedData['fedora_connector_item_id'];
				$pid = $uploadedData['fedora_connector_pid'];
				$datastreamsForm = $this->datastreamsForm($item_id, $pid);
				$this->view->form = $datastreamsForm;    		
    		}
    	}
    	else {
    			$this->flashError('Failed to gather posted data.');    			
    	}
    }
    
    public function updateAction() 
    {
    	$form = $this->datastreamsForm();
    	
		if ($_POST) {
			$uploadedData = $this->_request->getPost();
    		if ($form->isValid($uploadedData)) {    
    			//get posted values    			
				
				$item_id = $uploadedData['fedora_connector_item_id'];
				$pid = $uploadedData['fedora_connector_pid'];
				$metadataStream = $uploadedData['fedora_connector_metadata'];
				$posted = 0;
				foreach($uploadedData as $k=>$v){
					if (strstr($k, 'fedora_connector_datastream') && $v != '0'){
						$data = array();
						$exploded = explode('fedora_connector_datastream_', $k);
						$datastream = $exploded[1];
						$data = array('item_id'=>$item_id, 'pid'=>$pid, 'datastream'=>$datastream, 'mime_type'=>$v, 'metadata_stream'=>$metadataStream);
						try{
								//update the database with new values
								$db = get_db();
								$db->insert('fedora_connector_datastreams', $data);
								$posted += 1;
							} catch (Exception $err) {
								$this->flashError($err->getMessage());
	        				}
					} 
				}
				if ($posted > 0){
					$this->flashSuccess('Fedora datastreams connected to Item');
					$this->_helper->redirector->goto($item_id, 'edit', 'items');
				} else{
					$this->flashError('No datastreams selected.');
					$this->_helper->redirector->goto($item_id, 'edit', 'items');
				}
    		}
			else {
				var_dump($this->_request->getPost());
    			$this->flashError('Failed to gather posted data.');    			
    		}
    	}
    	
    }
    
    public function deleteAction()
    {
        if ($user = $this->getCurrentUser()) {
			$datastreamId = $this->_getParam('id');
			$item_id = $this->_getParam('item_id');
			$db = get_db();
            $datastream = $db->getTable('FedoraConnector_Datastream')->find($datastreamId);
            
			$datastream->delete();
			$this->flashSuccess('Fedora datastream successfully deleted.');
			$this->_helper->redirector->goto($item_id, 'edit', 'items');
        }
        else{
        	$this->_forward('forbidden');
        }
    }
    
	private function getPidForm($item_id)
	{
	    require "Zend/Form/Element.php";
    	$form = new Zend_Form();
		$form->setAction('select');    	
    	$form->setMethod('post');
    	$form->setAttrib('enctype', 'multipart/form-data');
    	  	
    	//PID
		$pid = new Zend_Form_Element_Text('fedora_connector_pid');
		$pid->setLabel('PID');
		$pid->setRequired(true);
		$form->addElement($pid);
		
		//Submit button
    	$form->addElement('submit','submit');
    	$submitElement=$form->getElement('submit');
    	$submitElement->setLabel('Submit');    	
		
		//item id 	
    	$itemId = new Zend_Form_Element_Hidden('fedora_connector_item_id');
    	$itemId->setValue($item_id);
    	$form->addElement($itemId);
    	
    	return $form;
	}
	
	private function datastreamsForm($item_id, $pid)
	{
		$server = get_option('fedora_connector_server');
		$datastreamXmlPath = $server . 'objects/' . $pid . '/datastreams?format=xml';
		$xml_doc = new DomDocument;	
		$xml_doc->load($datastreamXmlPath);
		$xpath = new DOMXPath($xml_doc);
		$datastreams = $xpath->query("//*[local-name() = 'datastream']");
	   
    	$form = new Zend_Form();
		$form->setAction('update');    	
    	$form->setMethod('post');
    	$form->setAttrib('enctype', 'multipart/form-data');
    	
    	//list available datastreams to attach.  multicheckbox.
    	foreach ($datastreams as $datastream){
	    	$datastreamElement = new Zend_Form_Element_Checkbox('fedora_connector_datastream_' . $datastream->getAttribute('dsid'));
			$datastreamElement->setLabel($datastream->getAttribute('dsid'));
			$datastreamElement->setCheckedValue($datastream->getAttribute('mimeType'));			
			$form->addElement($datastreamElement);
    	} 
    	
    	$metadataStream = new Zend_Form_Element_Select('fedora_connector_metadata');
    	$metadataStream->setLabel('Metadata Datastream:');
    	foreach ($datastreams as $datastream){
    		if (strstr($datastream->getAttribute('mimeType'), 'text/xml')){
    			$metadataStream->addMultiOption($datastream->getAttribute('dsid'), $datastream->getAttribute('dsid'));
    		}
    	}
    	$metadataStream->setRegisterInArrayValidator(false);
    	//$metadataStream->setValue('MODS');
    	$form->addElement($metadataStream);
		
		//item id 	
    	$itemIdElement = new Zend_Form_Element_Hidden('fedora_connector_item_id');
    	$itemIdElement->setValue($item_id);
    	$form->addElement($itemIdElement);
    	
    	//fedora pid
    	$pidElement = new Zend_Form_Element_Hidden('fedora_connector_pid');
    	$pidElement->setValue($pid);
    	$form->addElement($pidElement);
    	
    	//Submit button
    	$form->addElement('submit','submit');
    	$submitElement=$form->getElement('submit');
    	$submitElement->setLabel('Submit');    	
    	
    	return $form;
    	
	}
}

