<?php
namespace PerfectIn\Webservice;

use TYPO3\Flow\Annotations as Flow;

/**
 * webservice
 *
 * @Flow\Scope("prototype")
 */
class Webservice {
	
	/**
	 * class
	 *
	 * @var string
	 */
	protected $configuration;
	
	
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;	
	
	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;
	
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;
	
	
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Validation\ValidatorResolver
	 */
	protected $validatorResolver;
	
	/**
	 * constructor
	 * 
	 * @param string $name
	 */
	public function __construct($name) {
		$this->name = $name;
	}
	
	/**
	 * get name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * intialize object
	 */
	public function initializeObject() {
		$this->configurationManager->registerConfigurationType('Webservices');
		$webservices = $this->configurationManager->getConfiguration('Webservices');
		$this->configuration = $webservices[$this->name];
	}
	
	public function getOperation($name) {
		foreach ($this->configuration['operations'] AS $operation) {
			if ($operation['name'] == $name) {
				return $operation;
			}
		}	
		throw new \Exception('Operation does not exists', 1403810877);
	}
	
	/**
	 * call
	 * 
	 * @param string $operation
	 * @param array $args
	 * @throws \PerfectIn\Webservice\Exception\ValidationException
	 */
	public function call($operation, $args) {
		$operationConfiguration  = $this->getOperation($operation);
		
		$implementationClass 	= $operationConfiguration['implementation']['class'];
		$implementationMethod 	= $operationConfiguration['implementation']['method'];
		
		$call =  new WebserviceCall($implementationClass, $implementationMethod);
		$call->setArgs($args);		
		
		if (!$call->isValid()) {
			$exception = new Exception\ValidationException('ValidationFailed', 1392379627);
			$exception->setValidationResults($call->getValidationResult());
			throw $exception;
		}
		
		return $call->invoke();
	}

}	
	