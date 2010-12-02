<?php

/**
 * @copyright  Scholars' Lab 2010
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    $Id:$
 * @package FedoraConnector
 * @author Ethan Gruber: ewg4x at virginia dot edu
 */

//Require Zend Form Elements
require "Zend/Form/Element.php";

class FedoraConnector_DatastreamsController extends Omeka_Controller_Action
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
    
    public function browseAction() 	
    {
    	$db = get_db();
    	
    	$currentPage = $this->_getParam('page', 1);
    	$deleteUrl = WEB_ROOT . '/admin/fedora-connector/datastreams/delete/';
    	$count = $db->getTable('FedoraConnector_Datastream')->count();
    	
    	$this->view->datastreams =  FedoraConnector_Datastream::getDatastream($currentPage);
    	$this->view->count = $count;
    	$this->view->deleteUrl = $deleteUrl;
		
        /** 
         * Now process the pagination
         * 
         **/
        $paginationUrl = $this->getRequest()->getBaseUrl().'/datastreams/browse/';

        //Serve up the pagination
        $pagination = array('page'          => $currentPage, 
                            'per_page'      => 10, 
                            'total_results' => $count, 
                            'link'          => $paginationUrl);
        
        Zend_Registry::set('pagination', $pagination);
    }

    public function selectAction() 
    {
    	$form = $this->getPidForm();
    	
		if ($_POST) {
    		if ($form->isValid($this->_request->getPost())) {    
    			//get posted values		
				$uploadedData = $form->getValues();
				$item_id = ($uploadedData['fedora_connector_item_id'] > 0) ? $uploadedData['fedora_connector_item_id'] : 0;
				$pid = $uploadedData['fedora_connector_pid'];
				$server_id = $uploadedData['fedora_connector_server_id'];
				$datastreamsForm = $this->datastreamsForm($item_id, $pid, $server_id);
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
    		
    			if ($uploadedData['fedora_connector_item_id'] > 0){
    				$item_id = $uploadedData['fedora_connector_item_id'];
    			} else {
    				$item = new Item;    				
    				$item->save();
    				$item_id = $item->id;
    			}
				
				$pid = $uploadedData['fedora_connector_pid'];
				$metadataStream = $uploadedData['fedora_connector_metadata'];
				$server_id = $uploadedData['fedora_connector_server_id'];
				
				$posted = 0;
				foreach($uploadedData as $k=>$v){
					if (strstr($k, 'fedora_connector_datastream') && $v != '0'){
						$data = array();
						$exploded = explode('fedora_connector_datastream_', $k);
						$datastream = $exploded[1];
						$data = array('item_id'=>$item_id, 'pid'=>$pid, 'datastream'=>$datastream, 'mime_type'=>$v, 'metadata_stream'=>$metadataStream, 'server_id'=>$server_id);
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
			$db = get_db();
            $datastream = $db->getTable('FedoraConnector_Datastream')->find($datastreamId);
            $item_id = $datastream->item_id;
			$datastream->delete();
			$this->flashSuccess('Fedora datastream successfully deleted.');
			$this->_helper->redirector->goto($item_id, 'edit', 'items');
        }
        else{
        	$this->_forward('forbidden');
        }
    }
    
    public function importAction(){
    	$db = get_db();
    	$id = $this->_getParam('id');
    	$datastream = $db->getTable('FedoraConnector_Datastream')->find($id);
    	
    	echo fedora_connector_import_metadata($datastream);
		
		$this->flashSuccess('Metadata succesfully imported.');
		$this->_helper->redirector->goto($datastream->item_id, 'edit', 'items');
    }
    
	private function getPidForm($item_id)
	{
		$db = get_db();
		$servers = $db->getTable('FedoraConnector_Server')->findAll();
	    
    	$form = new Zend_Form();
		$form->setAction('select');    	
    	$form->setMethod('post');
    	$form->setAttrib('enctype', 'multipart/form-data');
    	
    	$serverElement = new Zend_Form_Element_Select('fedora_connector_server_id');
    	$serverElement->setLabel('Server:');
    	foreach ($servers as $server){
    		$serverElement->addMultiOption($server->id, $server->name);
    		if ($server->is_default > 0) {
    			$is_default = $server->id;
    		}
    	}
    	$serverElement->setValue($is_default);
    	$serverElement->setRegisterInArrayValidator(false);
    	//$metadataStream->setValue('MODS');
    	$form->addElement($serverElement);
    	  	
    	//PID
		$pid = new Zend_Form_Element_Text('fedora_connector_pid');
		$pid->setLabel('PID:');
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
	
	private function datastreamsForm($item_id, $pid, $server_id)
	{
		//get the server from the FedoraConnecter_Server table
		$db = get_db();
		$server = $db->getTable('FedoraConnector_Server')->find($server_id)->url;
	
		//get excluded datastreams
		$ommittedString = get_option('fedora_connector_omitted_datastreams');
		
		//get the datastreams from Fedora REST
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
    		//skip datastream if in omitted list.  example: RELS-INT, RELS-EXT, AUDIT
    		if (!strstr($ommittedString, $datastream->getAttribute('dsid'))){
		    	$datastreamElement = new Zend_Form_Element_Checkbox('fedora_connector_datastream_' . $datastream->getAttribute('dsid'));
				$datastreamElement->setLabel($datastream->getAttribute('dsid'));
				$datastreamElement->setCheckedValue($datastream->getAttribute('mimeType'));			
				$form->addElement($datastreamElement);
    		}
    	} 
    	
    	$metadataStream = new Zend_Form_Element_Select('fedora_connector_metadata');
    	$metadataStream->setLabel('Object Metadata:');
    	foreach ($datastreams as $datastream){
    		//skip datastream if in omitted list.  example: RELS-INT, RELS-EXT, AUDIT.  applies to text/xml mime-types only
    		if (strstr($datastream->getAttribute('mimeType'), 'text/xml') && !strstr($ommittedString, $datastream->getAttribute('dsid'))){
    			$metadataStream->addMultiOption($datastream->getAttribute('dsid'), $datastream->getAttribute('dsid'));
    		}
    	}
    	$metadataStream->setValue('DC');
    	$metadataStream->setRegisterInArrayValidator(false);
    	$form->addElement($metadataStream);
		
    	//Submit button
    	$form->addElement('submit','submit');
    	$submitElement=$form->getElement('submit');
    	$submitElement->setLabel('Submit');  
    	
		//item id 	
    	$itemIdElement = new Zend_Form_Element_Hidden('fedora_connector_item_id');
    	$itemIdElement->setValue($item_id);
    	$form->addElement($itemIdElement);
    	
    	//fedora pid
    	$pidElement = new Zend_Form_Element_Hidden('fedora_connector_pid');
    	$pidElement->setValue($pid);
    	$form->addElement($pidElement);
    	
    	//server id
		$serverId = new Zend_Form_Element_Hidden('fedora_connector_server_id');
    	$serverId->setValue($server_id);
    	$form->addElement($serverId);
    	
    	return $form;    	
	}
}

