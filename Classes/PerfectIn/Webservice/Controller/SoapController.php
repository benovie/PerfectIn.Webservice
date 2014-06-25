<?php
namespace PerfectIn\Webservice\Controller;

use TYPO3\Flow\Annotations as Flow;

/**
 * handle soap webservices
 *
 * @Flow\Scope("singleton")
 */
class SoapController extends \TYPO3\Flow\Mvc\Controller\ActionController {	
	
	/**
	 * handle webservice
	 * 
	 * @return void
	 */
	public function handleAction() { 
		$class 		= $this->request->getArgument('class');		
		$proxy 		= new \PerfectIn\Webservice\WebserviceProxy($class);

		$soapserver = new \SoapServer($this->request->getArgument('wsdl'));
		$soapserver->setObject($proxy);
		
		ob_start();
		try {
			$soapserver->handle();
		} catch(\Exception $exception) {
			$soapserver->fault($exception->getCode(), $exception->getMessage());		
		}
		
		$response = ob_get_contents();
		ob_end_clean();
		
		$this->response->setHeader('Content-type','text/html');
		$this->response->setContent($response);
	}

	
	/**
	 * don't use views for this controller
	 * 
	 * @see TYPO3\Flow\Mvc\Controller.ActionController::resolveView()
	 */
	protected function resolveView() {
		return null;
	}
	
}

?>