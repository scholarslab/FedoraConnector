<?php
/**
 * @version $Id$
 * @copyright UVaLib DRS R & D, 2009
 * @package Fedora
 **/

require_once 'File.php';
require_once 'repo-soap.php'; // library containing wrappers for SOAP access to repo


/**
 * URLs for files are routed through this controller.
 **/
class FedoraConnector_FedoraController extends Omeka_Controller_Action
{
	public function getAction()
	{
		$logger = Omeka_Context::getInstance()->getLogger();

		$id = (!$id) ? $this->getRequest()->getParam('id') : $id;
		$file = $this->findById($id,"File");
		$myitem = $file->getItem();

		if ( $myitem->getItemType()->name == 'Fedora object') {
			$identifiers = $myitem->getElementTextsByElementNameAndSetName( 'Fedora PID', 'Item Type Metadata');
			$pid = $identifiers[0]->text;
			$path =  FEDORA_REPO_ROOT.'/get/'. $pid.'/' . $file->original_filename;
	        if ($logger) {
        			$logger->log(getMembers($myitem), Zend_Log.INFO);
       		}
		}

		else {
			$format = $this->_getParam('format');

			// If we don't have any images associated with this file, then use the
			// full archive path
			if (!$file->has_derivative_image) {
				$format = 'archive';
			}

			// Otherwise use the chosen format of the image
			$path = $file->getWebPath($format);
		}

		if (!headers_sent())
		header('Location: '.$path);
		else {
			echo '<script type="text/javascript">';
			echo 'window.location.href="'.$path.'";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url='.$path.'" />';
			echo '</noscript>';
		}

		//Don't render anything
		$this->_helper->viewRenderer->setNoRender();
	}

}