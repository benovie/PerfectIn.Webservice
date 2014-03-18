<?php

namespace PerfectIn\Webservice\Command;

use TYPO3\Flow\Annotations as Flow;

class RouteCommandController extends \TYPO3\Flow\Cli\CommandController {
	
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
	 */
	public function allCommand() {
		$restRoutes = $this->getRestRoutes();
		$soapRoutes = $this->getSoapRoutes();
		$routes = array_merge($restRoutes, $soapRoutes);
		$this->yamlSource->save($this->packageManager->getPackage('PerfectIn.Webservice')->getConfigurationPath() . 'Routes', $routes);
	}
	
	
	protected function getRestRoutes() {
		return $this->createRoutes('PerfectIn\Webservice\Annotations\Rest', function($annotation, $class, $method) {
			return array(
				'name' => 'Rest route for ' . $method,
				'uriPattern' => ltrim($annotation->uri,'/'),
				'httpMethods' => array($annotation->method),
				'defaults' => array(
					'@package' => 'PerfectIn.Webservice',
					'@controller' => 'Rest',
					'@action' => 'handle',
					'class' => $class,
					'method' => $method
				)
			);
		});
	}
	
	protected function getSoapRoutes() {
		return $this->createRoutes('PerfectIn\Webservice\Annotations\Soap', function($annotation, $class, $method) {
			return array(
				'name' => 'Soap route for ' . $method,
				'uriPattern' => ltrim($annotation->endpoint,'/'),
				'httpMethods' => array('POST'),
				'defaults' => array(
					'@package' => 'PerfectIn.Webservice',
					'@controller' => 'Soap',
					'@action' => 'handle',
					'class' => $class
				)
			);
		});
	}
		
	protected function createRoutes($annotationClass, $routeCallback) {
		$routes = array();
		$webserviceClasses = $this->reflectionService->getClassesContainingMethodsAnnotatedWith($annotationClass);
		
		foreach($webserviceClasses AS $webserviceClass) {
			foreach(get_class_methods($webserviceClass) AS $method) {
				$annotation = $this->reflectionService->getMethodAnnotation($webserviceClass, $method, $annotationClass);
				if ($annotation) {
					$routes[] = $routeCallback($annotation, $webserviceClass, $method);
				}
			}
		}
		
		return $routes;
	}

}