<?php

namespace PerfectIn\Webservice\Command;

use TYPO3\Flow\Annotations as Flow;

/**
 * Rest command controller
 *
 * @Flow\Scope("singleton")
 */
class RestCommandController extends \TYPO3\Flow\Cli\CommandController {
	
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
	 * create routes for the rest webservices
	 * 
	 * @return void
	 */
	public function routeCommand() {
		$routes = array();
		$webserviceClasses = $this->reflectionService->getClassesContainingMethodsAnnotatedWith('PerfectIn\Webservice\Annotations\Rest');
		
		foreach($webserviceClasses AS $webserviceClass) {
			foreach(get_class_methods($webserviceClass) AS $method) {
				$annotation = $this->reflectionService->getMethodAnnotation($webserviceClass, $method, 'PerfectIn\Webservice\Annotations\Rest');
				if ($annotation) {
					$routes[] = array(
						'name' => 'Rest route for ' . $method,
						'uriPattern' => ltrim($annotation->uri,'/'),
						'httpMethods' => array($annotation->method),
						'defaults' => array(
							'@package' => 'PerfectIn.Webservice',
							'@controller' => 'Rest',
							'@action' => 'handle',
							'class' => $webserviceClass,
							'method' => $method
						)
					);
				}
			}
		}
		
		
		$this->yamlSource->save($this->packageManager->getPackage('PerfectIn.Webservice')->getConfigurationPath() . 'Routes', $routes);
	}
}