<?php

namespace PerfectIn\Webservice\Annotations;

/**
 * @Annotation
 */
final class Rest {

	/**
	 * method
	 *
	 * @var string
	 */
	public $method;
	
	/**
	 * uri
	 *
	 * @var string
	 */
	public $uri;
	
	
	/**
	 * @param array $values
	 */
	public function __construct(array $values) {
		if (!isset($values['method'])) {
			throw new \InvalidArgumentException('method is required');
		}
		if (!isset($values['uri'])) {
			throw new \InvalidArgumentException('uri is required');
		}
		
		$this->method 	= $values['method'];
		$this->uri 		= $values['uri'];
	}
	
}

?>