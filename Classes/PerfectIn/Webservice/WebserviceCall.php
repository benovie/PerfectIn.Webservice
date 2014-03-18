<?php
namespace PerfectIn\Webservice;

use TYPO3\Flow\Annotations as Flow;

/**
 * webservice call
 *
 * @Flow\Scope("prototype")
 */
class WebserviceCall {
	
	/**
	 * class
	 *
	 * @var string
	 */
	protected $class;
	
	/**
	 * method
	 *
	 * @var string
	 */
	protected $method;
	
	/**
	 * arguments
	 * 
	 * @var array
	 */
	protected $args = array();
	
	/**
	 * arguments
	 * 
	 * @var array
	 */
	protected $arguments = array();
	
	/**
	 * parameters
	 * 
	 * @var array
	 */
	protected $parameters = array();
	
	
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
	 * @param array $args
	 */
	public function __construct($class, $method, $args) {
		$this->class= $class;
		$this->method= $method;
		$this->args = $args;
		$this->validationResult = new \TYPO3\Flow\Error\Result();
	}
	
	/**
	 * intialize object
	 */
	public function initializeObject() {
		$methodParameters 	= $this->reflectionService->getMethodParameters($this->class, $this->method);
		$validators 		= $this->validatorResolver->buildMethodArgumentsValidatorConjunctions($this->class, $this->method);
		$validationGroups 	= array('Default', ucfirst($this->method));
			
		foreach ($methodParameters AS $name => $methodParamater) {
			$validator 		= $validators[$name];
			$validator->addValidator($this->validatorResolver->getBaseValidatorConjunction($methodParamater['type'], $validationGroups));
			
			$argument = new Argument($name, $methodParamater['type']);
			$argument->setPosition($methodParamater['position']);
			$argument->setRequired(!$methodParamater['optional']);
			$argument->setDefaultValue($methodParamater['defaultValue']);		
			$argument->setValidator($validator);
			
			if (isset($this->args[$argument->getPosition()])) {
				$argument->setValue($this->args[$argument->getPosition()]);
				if (!$argument->isValid()) {
					$validationResult->forProperty($argument->getName())->merge($argument->getValidationResults());
				}
			}
			$this->parameters[$argument->getPosition()] = $argument->getValue();
			$this->arguments[$name] = $argument;
		}
	}
	
	/**
	 * is valid
	 * 
	 * @return boolean
	 */
	public function isValid() {
		return !$this->validationResult->hasErrors();
	}
	
	
	/**
	 * get validationResult
	 *
	 * @return \TYPO3\Flow\Error\Result
	 */
	public function getValidationResult() {
		return $this->validationResult;
	}
	
	
	/**
	 * get parameters
	 *
	 * @return array
	 */
	public function getParameters() {
		return $this->parameters;
	}	
	
	/**
	 * perform call to method
	 * 
	 * @return mixed
	 */
	public function __invoke() {
		return call_user_func_array(array($this->objectManager->get($this->class), $this->method), $this->getParameters());
	}
	
}	
	