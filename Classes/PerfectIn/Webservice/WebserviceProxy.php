<?php
namespace PerfectIn\Webservice;

use TYPO3\Flow\Annotations as Flow;

/**
 * proxy webservice class
 *
 * @Flow\Scope("prototype")
 */
class WebserviceProxy {
	
	/**
	 * class to proxy
	 * 
	 * @var string
	 */
	protected $class;
	
	/**
	 * constructor
	 * 
	 * @param string $class
	 * @param string $method
	 */
	public function __construct($class) {
		$this->class= $class;
	}
	
	/**
	 * proxy to the webservice
	 * 
	 * @param string $method
	 * @param array $args
	 * @throws \Exception
	 */
	public function __call($method, $args) {
		$webserviceCall = new WebserviceCall($this->class, $method, $args);
		if ($webserviceCall->isValid()) {
			return $webserviceCall();	
		} else {
			$exception = new Exception\ValidationException('ValidationFailed', 1392379627);
			$exception->setValidationResults($webserviceCall->getValidationResult());
			throw $exception;
		}
	}
	
}	
	