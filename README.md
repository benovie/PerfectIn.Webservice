PerfectIn.Webservice is a TYPO3.Flow package to create webservices


## Example webservice

```
<?php

use PerfectIn\Webservice\Annotations as Webservice;

/**
 * some webservice
 */
class SomeWebservice {
	
	/**
	 * doSomething
	 *
	 * @Webservice\Rest(method="POST",uri="/webservice/rest/something/{something}")
	 * @Webservice\Soap(endpoint="/webservice/soap/something")
	 * @param string $something
	 * @return void
	 */
	public function doSomething($something) {
	}
}
```


## via REST

- add @PerfectIn\Webservice\Annotations\Rest annotation to a method
- annotation option: method => HTTP method to use 
- annotation option: uri => uri to use for request
- typo3 route notation for a param {paramname} is available as parameter with same name in your method


generate Routes for the rest webservices

> ./flow perfectin.webservice route:all


## via SOAP

- add @PerfectIn\Webservice\Annotations\Soap annotation to a method
- annotation option: endpoint => soap endpoint

> ./flow perfectin.webservice route:all