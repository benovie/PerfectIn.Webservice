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
	public function __construct($class, $method) {
		$this->class= $class;
		$this->method= $method;
		$this->validationResult = new \TYPO3\Flow\Error\Result();
	}
	
	/**
	 * get class
	 * 
	 * @return string
	 */
	public function getClass() {
		return $this->class;
	}	
	
	/**
	 * get method
	 *
	 * @return string
	 */	
	public function getMethod() {
		return $this->method;
	}

	/**
	 * intialize object
	 */
	public function initializeObject() {
		$this->initializeArguments();
	}
	
	/**
	 * intialize the arguments
	 * 
	 */
	protected function initializeArguments() {
		$methodParameters 	= $this->reflectionService->getMethodParameters($this->class, $this->method);
		$validators 		= $this->validatorResolver->buildMethodArgumentsValidatorConjunctions($this->class, $this->method);
		$validationGroups 	= array('Default', ucfirst($this->method));

		foreach ($methodParameters AS $name => $methodParameter) {
			$methodParameterType = $methodParameter['type'] == 'mixed' ? 'string' : $methodParameter['type'];

			$validator 		= $validators[$name];
			$validator->addValidator($this->validatorResolver->getBaseValidatorConjunction($methodParameterType, $validationGroups));

			$argument = new Argument($name, $methodParameterType);
			$argument->setPosition($methodParameter['position']);
			$argument->setRequired(!$methodParameter['optional']);
			$argument->setDefaultValue($methodParameter['defaultValue']);
			$argument->setValidator($validator);
			
			$this->arguments[$name] = $argument;
		}
	}
	
	/**
	 * get args to use
	 * 
	 * @param array $args
	 */
	public function setArgs($args) {
		$this->validationResult = new \TYPO3\Flow\Error\Result();
		$this->parameters = array();
		foreach ($this->arguments AS $name => $argument) {
			if (isset($args[$argument->getPosition()])) {
				$argument->setValue($args[$argument->getPosition()]);
				$this->parameters[$argument->getPosition()] = $argument->getValue();
				if (!$argument->isValid()) {
					$this->validationResult->forProperty($argument->getName())->merge($argument->getValidationResults());
				}
			}
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
	public function invoke() {
		$response = call_user_func_array(array($this->objectManager->get($this->class), $this->method), $this->getParameters());
		$response = $this->mapToStructure($response);
		return $response;
	}

	/**
	 * map data to simple data structures
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	protected function mapToStructure($data) {
		if (is_array($data) || is_object($data) && $data instanceof \Traversable) {
			$return = array();
			foreach ($data AS $index => $item) {
				$return[$index] = $this->mapToStructure($item);
			}
		} else if(is_object($data)) {
			$return = new \stdClass();
			$properties = \TYPO3\Flow\Reflection\ObjectAccess::getGettablePropertyNames($data);
			foreach ($properties AS $property) {
				$return->{$property} = $this->mapToStructure(\TYPO3\Flow\Reflection\ObjectAccess::getProperty($data, $property));
			}
		} else {
			$return = $data;
		}

		return $return;
	}
}
