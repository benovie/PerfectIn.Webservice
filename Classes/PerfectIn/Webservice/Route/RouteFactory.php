<?php
namespace PerfectIn\Webservice\Route;

use TYPO3\Flow\Annotations as Flow;

/**
 * factory routes
 * 
 * 
 * @Flow\Scope("singleton")
 */
class RouteFactory implements RouteFactoryInterface {
	
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * create route implementation
	 * 
	 * @param string $type
	 * @return RouteInterface
	 */
	public function create($type) {
		switch(strtolower($type)) {
			case "rest":
				return $this->objectManager->get('PerfectIn\Webservice\Route\RestRoute');
			break;
			case "soap":
				return $this->objectManager->get('PerfectIn\Webservice\Route\SoapRoute');
			break;
			default:
				throw new \Exception('can not create route');
		}
	}
	
}
