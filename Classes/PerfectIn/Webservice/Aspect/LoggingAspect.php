<?php
namespace PerfectIn\Webservice\Aspect;

use TYPO3\Flow\Annotations as Flow;

/**
 * An aspect which centralizes the logging of webservice relevant actions.
 *
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class LoggingAspect {
	
	
	/**
	 * @var \TYPO3\Flow\Log\LoggerInterface
	 * @Flow\Inject
	 */
	protected $logger;

	/**
	 * 
	 * @var string
	 */
	protected $logIdentifier;	

	/**
	 * Logs calls
	 *
	 * @Flow\Before("method(PerfectIn\Webservice\Webservice->call())")
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The current joinpoint
	 */
	public function logStartServiceCall(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		$arguments = array();
		foreach ($joinPoint->getMethodArgument('args') AS $arg) {
			$arguments[] = $this->getLogMessageForVariable($arg);
		}
		$this->logIdentifier = uniqid();
		$message = $this->logIdentifier. ' - request - ' .$joinPoint->getProxy()->getName().'->'.$joinPoint->getMethodArgument('operation') . '(' . implode(', ',$arguments) .')'; 
		$this->logger->log($message, LOG_INFO);
	}

	/**
	 * Logs calls
	 *
	 * @Flow\After("method(PerfectIn\Webservice\Webservice->call())")
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The current joinpoint
	 */
	public function logFinishServiceCall(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		$callIdentifier = $joinPoint->getProxy()->getName().'::'.$joinPoint->getMethodArgument('operation');
		if ($joinPoint->hasException()) {
			$this->logger->log($this->logIdentifier. ' - error - '.$joinPoint->getException()->getMessage() . '('.$joinPoint->getException()->getCode().')', LOG_ERR);
		} else {
			$this->logger->log($this->logIdentifier. ' - response - '.$this->getLogMessageForVariable($joinPoint->getResult()), LOG_INFO);
		}
	}

	/**
	 * get a log message for a variable
	 * 
	 * @param mixed $data
	 * @return string
	 */
	protected function getLogMessageForVariable($data) {
		if (is_scalar($data)) {
			return $data;
		} elseif (is_array($data)) {
			return 'array() ['.count($data).']';
		} elseif (is_object($data)) {
			return get_class($data);
		} else {
			return gettype($data);
		}
	}
	
}