<?php
namespace PerfectIn\Webservice;

use TYPO3\Flow\Annotations as Flow;
use PerfectIn\Webservice\Annotations as Webservice;

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
	 * @Webservice\Rest(method="GET",uri="webservice/api/rest")
	 * @return array
	 */
	public function readAll() {
		$webservices 		= array();	
		$annotationClass 	= 'PerfectIn\Webservice\Annotations\Rest';
		$webserviceClasses 	= $this->reflectionService->getClassesContainingMethodsAnnotatedWith($annotationClass);
		
		foreach($webserviceClasses AS $webserviceClass) {	
			$webserviceClassMethods = get_class_methods($webserviceClass);
			if ($webserviceClassMethods) {
				foreach($webserviceClassMethods AS $method) {
					$annotation = $this->reflectionService->getMethodAnnotation($webserviceClass, $method, $annotationClass);
					if ($annotation) {
						$webservices[] = array('configuration'=>$annotation,'params'=>$this->reflectionService->getMethodParameters($webserviceClass, $method));
					}
				}
			}
		}
		
		return $webservices;
	}
	
}
