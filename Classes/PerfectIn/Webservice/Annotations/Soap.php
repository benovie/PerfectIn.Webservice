<?php

namespace PerfectIn\Webservice\Annotations;

/**
 * @Annotation
 */
final class Soap {

	
	/**
	 * endpoint
	 *
	 * @var string
	 */
	public $endpoint;
	
	
	/**
	 * @param array $values
	 */
	public function __construct(array $values) {
		$this->endpoint 	= isset($values['endpoint']) ? $values['endpoint'] : null;
	}
	
}

?>