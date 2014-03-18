<?php
namespace PerfectIn\Webservice;

use TYPO3\Flow\Annotations as Flow;

/**
 * argument
 */
class Argument extends \TYPO3\Flow\Mvc\Controller\Argument {
	
	/**
	 * position
	 * 
	 * @var integer
	 */
	protected $position;
	
	/**
	 * set position
	 * 
	 * @param integer $position
	 */
	public function setPosition($position) {
		$this->position = $position;
	}
	
	/**
	 * get position
	 * 
	 * @return integer
	 */
	public function getPosition() {
		return $this->position;
	}
	
	
	/**
	 * Return the Property Mapping Configuration used for this argument; can be used by the initialize*action to modify the Property Mapping.
	 *
	 * @return \TYPO3\Flow\Mvc\Controller\MvcPropertyMappingConfiguration
	 * @api
	 */
	public function getPropertyMappingConfiguration() {
		if ($this->propertyMappingConfiguration === NULL) {
			$this->propertyMappingConfiguration = new \TYPO3\Flow\Mvc\Controller\MvcPropertyMappingConfiguration();
			$this->propertyMappingConfiguration->allowAllProperties();
		}
		return $this->propertyMappingConfiguration;
	}
}