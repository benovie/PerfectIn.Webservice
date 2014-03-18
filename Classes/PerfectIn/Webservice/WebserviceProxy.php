<?php
namespace PerfectIn\Webservice;

use TYPO3\Flow\Annotations as Flow;

/**
 * handle rest webservices
 *
 * @Flow\Scope("prototype")
 */
class WebserviceProxy {
	
	/**
	 * class to proxy
	 * 
	 * @var string
	 */
	protected $class;
	
	/**
	 * method to proxy
	 *
	 * @var string
	 */
	protected $method;
	
	/**
	 * arguments
	 * 
	 * @var array
	 */
	protected $arguments = array();
		
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;
	
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
	 * @param string $class
	 * @param string $method
	 */
	public function __construct($class, $method) {
		$this->class= $class;
		$this->method = $method;
	}
	
	/**
	 * initialize object
	 * 
	 */
	public function initializeObject() {		
		$this->initializeArguments();
		$this->initializeArgumentValidation();
	}
	
	/**
	 * initialize arguments
	 *
	 * @return void
	 */
	protected function initializeArguments() {
		$methodParameters = $this->reflectionService->getMethodParameters($this->class, $this->method);
	
		foreach ($methodParameters AS $name => $methodParamater) {
			$argument = new Argument($name, $methodParamater['type']);
			$argument->setPosition($methodParamater['position']);
			$argument->setRequired(!$methodParamater['optional']);
			$argument->setDefaultValue($methodParamater['defaultValue']);
			$this->arguments[$name] = $argument;
		}
	}
	
	/**
	 * initialize argument validators
	 * 
	 * @return void
	 */
	protected function initializeArgumentValidation() {
	
		$methodParameters = $this->reflectionService->getMethodParameters($this->class, $this->method);
	
		$validators = $this->validatorResolver->buildMethodArgumentsValidatorConjunctions($this->class, $this->method);
		$validationGroups = array('Default', ucfirst($this->method));
	
		foreach ($methodParameters AS $name => $methodParamater) {
			$validator 		= $validators[$name];
			$validator->addValidator($this->validatorResolver->getBaseValidatorConjunction($methodParamater['type'], $validationGroups));
			$this->arguments[$name]->setValidator($validator);
		}
	}
	
	/**
	 * proxy to the webservice
	 * 
	 * @param string $method
	 * @param array $args
	 * @throws \Exception
	 */
	public function __call($method, $args) {
		$parameters = array();
		$validationResult = new \TYPO3\Flow\Error\Result();
		foreach($this->arguments AS $argument) {
			if (isset($args[$argument->getPosition()])) {
				$argument->setValue($args[$argument->getPosition()]);
				if (!$argument->isValid()) {
					$validationResult->forProperty($argument->getName())->merge($argument->getValidationResults());
				}
			}
			$parameters[$argument->getPosition()] = $argument->getValue();
		}
		
		if ($validationResult->hasErrors()) {
			$exception = new Exception\ValidationException('ValidationFailed', 1392379627);
			$exception->setValidationResults($validationResult);
			throw $exception;
		}
		
		return call_user_func_array(array($this->objectManager->get($this->class), $method), $parameters);
	}
	
}	
	