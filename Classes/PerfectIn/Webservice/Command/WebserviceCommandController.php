<?php

namespace PerfectIn\Webservice\Command;

use TYPO3\Flow\Annotations as Flow;

class WebserviceCommandController extends \TYPO3\Flow\Cli\CommandController {
	
	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;
	
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\Source\YamlSource
	 */
	protected $yamlSource;
	
	/**
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 * @Flow\Inject
	 */
	protected $packageManager;
	
	/**
	 * @Flow\Inject
	 * @var \PerfectIn\Webservice\Route\RouteFactoryInterface
	 */
	protected $routeFactory;
	
	/**
	 * create routes for the webservices
	 * 
	 */
	public function routesCommand() {
		$routes = array();
		$this->configurationManager->registerConfigurationType('Webservices');
		$webservices = $this->configurationManager->getConfiguration('Webservices');
		
		foreach ($webservices AS $webservice) {
			foreach ($webservice['operations'] AS $operation) {
				if (isset($operation['bindings'])) {
					foreach($operation['bindings'] AS $binding) {
						$routes[] = $this->routeFactory->create($binding['type'])->create(
							$webservice['name'], 
							$operation['name'],
							$operation['implementation'],
							$binding['options']
						);
					}
				}
			}
		}
		$routes = array_values(array_unique($routes, SORT_REGULAR));
		$this->yamlSource->save($this->packageManager->getPackage('PerfectIn.Webservice')->getConfigurationPath() . 'Routes', $routes);
	}
	
}