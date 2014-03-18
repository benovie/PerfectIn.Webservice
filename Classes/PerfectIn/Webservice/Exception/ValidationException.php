<?php
namespace PerfectIn\Webservice\Exception;

/**
 * validation exception
 *
 */
class ValidationException extends \Exception {
	
	/**
	 * public errors
	 * 
	 * @var array
	 */
	public $errors = array();
	
	/**
	 * set validation results
	 * 
	 * @param \TYPO3\Flow\Error\Result $results
	 */
	public function setValidationResults(\TYPO3\Flow\Error\Result $results) {
		foreach($results->getFlattenedErrors() AS $property => $errors) {
			$this->errors[$property] = array();
			foreach ($errors AS $error) {
				$this->errors[$property][] = array('code'=>$error->getCode(),'message'=>$error->getMessage());
			}	
		}
	}
}
	