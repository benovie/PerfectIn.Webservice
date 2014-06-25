<?php
namespace PerfectIn\Webservice\Route;

use TYPO3\Flow\Annotations as Flow;

/**
 * class to create routes for rest
 * 
 * @Flow\Scope("singleton")
 */
class SoapRoute implements RouteInterface {
	

	/**
	 * create rest route
	 * 
	 * @param string $webservice
	 * @param string $operation
	 * @param array $implementation
	 * @param array $options
	 * @return array
	 */
	public function create($webservice, $operation, $implementation, $options = array()) {
		return array(
			'name' => 'Soap route for ' . $webservice,
			'uriPattern' => ltrim($options['endpoint'],'/'),
			'httpMethods' => array('POST'),
			'defaults' => array(
				'@package' => 'PerfectIn.Webservice',
				'@controller' => 'Soap',
				'@action' => 'handle',
				'class' => $implementation['class']
			)
		);
	}
	
}
