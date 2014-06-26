<?php
namespace PerfectIn\Webservice\Controller;

use TYPO3\Flow\Annotations as Flow;

/**
 * handle rest webservices
 *
 * @Flow\Scope("singleton")
 */
class RestController extends \TYPO3\Flow\Mvc\Controller\ActionController {	
	
	/**
	 * handle webservice
	 * 
	 * @return void
	 */
	public function handleAction() { 
		$class 		= $this->request->getArgument('class');	
		$method 	= $this->request->getArgument('method');
		$webservice 	= $this->request->getArgument('webservice');	
		$operation 		= $this->request->getArgument('operation');	
		
		try {
			$proxy 		= new \PerfectIn\Webservice\WebserviceProxy($webservice);
			$result 	= call_user_func_array(array($proxy, $operation) , $this->getArguments($class, $method));	
			$this->response->setHeader('Content-type','application/json');
			$this->response->setContent(json_encode($result));
		} catch(\Exception $exception) {
			$this->handleException($exception);
		}
	}
	
	/**
	 * handle exceptions
	 * 
	 * @param \Exception $exception
	 */
	protected function handleException($exception) {
		$exceptionResponse = new \stdClass();
		$exceptionResponse->message = $exception->getMessage();
		$exceptionResponse->code = $exception->getCode();
		$this->response->setStatus(400);
		$this->response->setHeader('Content-type','application/json');
		$this->response->setContent(json_encode($exceptionResponse));
	}
	
	/**
	 * get indexed arguments for method
	 * 
	 * @param string $class
	 * @param string $method
	 * @return array
	 */
	protected function getArguments($class, $method) {
		$arguments = array();		
		$parameters = $this->reflectionService->getMethodParameters($class, $method);
		
		foreach ($parameters AS $name => $parameter) {
			$arguments[$parameter['position']] = $this->request->hasArgument($name) ? $this->request->getArgument($name) : null;
		}
		
		return $arguments;
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