<?php
namespace PerfectIn\Webservice\Route;

use TYPO3\Flow\Annotations as Flow;

/**
 * interface to create routes
 */
interface RouteInterface {
	

	/**
	 * create route
	 * 
	 * @param string $webservice
	 * @param string $operation
	 * @param array $implementation
	 * @param array $options
	 * @return array
	 */
	public function create($webservice, $operation, $implementation, $options = array());
	
}
