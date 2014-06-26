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
	 * webservice name
	 * 
	 * @var string
	 */
	protected $webservice;
	
	/**
	 * constructor
	 * 
	 * @param string $webservice
	 * @param string $method
	 */
	public function __construct($webservice) {
		$this->webservice = $webservice;
	}
	
	/**
	 * proxy to the webservice
	 * 
	 * @param string $method
	 * @param array $args
	 * @throws \Exception
	 */
	public function __call($method, $args) {
		$webservice = new Webservice($this->webservice);
		return $webservice->call($method, $args);
	}
	
}	
	