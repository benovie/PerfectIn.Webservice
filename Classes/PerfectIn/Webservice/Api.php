<?php
namespace PerfectIn\Webservice;

use TYPO3\Flow\Annotations as Flow;

/**
 * api
 */
class Api{
		
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;
	
	
	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;
	
	/**
	 * get for all webservices
	 * 
	 * @return array
	 */
	public function getForAllWebservices() {
		
		$this->configurationManager->registerConfigurationType('Webservices');
		$webservices = $this->configurationManager->getConfiguration('Webservices');
		
		foreach ($webservices AS &$webservice) {
			foreach ($webservice['operations'] AS &$operation) {
				$methodParameters = $this->reflectionService->getMethodParameters(
					$operation['implementation']['class'], 
					$operation['implementation']['method']);
				$operation['parameters'] = array();
				foreach($methodParameters AS $methodParameterName => $methodParameter) {
					$operation['parameters'][] = array(
						'name' => $methodParameterName,
						'type' => $this->getForType($methodParameter['type'])
					);
				}
			}
		};

		return $webservices;
	}
	
	public function getForType($type) {
		
		
		
		return $type;
	}
	
}
