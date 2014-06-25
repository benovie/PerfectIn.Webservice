<?php
namespace PerfectIn\Webservice\Route;

use TYPO3\Flow\Annotations as Flow;

/**
 * class to create routes for rest
 * 
 * @Flow\Scope("singleton")
 */
class RestRoute implements RouteInterface {
	

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
			'name' => 'Rest route for ' . $webservice.':'.$operation,
			'uriPattern' => ltrim($options['url'],'/'),
			'httpMethods' => array(strtoupper($options['method'])),
			'defaults' => array(
					'@package' => 'PerfectIn.Webservice',
					'@controller' => 'Rest',
					'@action' => 'handle',
					'class' => $implementation['class'],
					'method' => $implementation['method']
			)
		);
	}
	
}
