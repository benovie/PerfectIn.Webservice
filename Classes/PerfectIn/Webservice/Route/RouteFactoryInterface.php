<?php
namespace PerfectIn\Webservice\Route;

use TYPO3\Flow\Annotations as Flow;

/**
 * interface factory routes
 */
interface RouteFactoryInterface {

	/**
	 * create route implementation
	 * 
	 * @param string $type
	 * @return RouteInterface
	 */
	public function create($type);
	
}
