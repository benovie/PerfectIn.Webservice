PerfectIn.Webservice
==============
 is a TYPO3.Flow package to create webservices for existing code

Create webservice configuration where you define the methods that needs to be delivered as webservices

- Supports REST and SOAP



## Example webservice configurations

### Find all TYPO3\Flow\Security\Role with REST

webservices.yaml:

```
-
  name: Roles
  operations:
    -
      name: findAll
      bindings:
        - 
          type: rest
          options:
            url: webservice/security/role
            method: GET
      implementation:
        class: TYPO3\Flow\Security\Policy\RoleRepository
        method: findAll 
 
```       
    

### Find one TYPO3\Flow\Security\Roles with REST

> Note that the variable {identifier} in the url is automatically mapped to the $identifier parameter in the `findByIdentifier` method


webservices.yaml:

```
    -
      name: read
      bindings:
        - 
          type: rest
          options:
            url: webservice/security/role/{identifier}
            method: GET
      implementation:
        class: TYPO3\Flow\Security\Policy\RoleRepository
        method: findByIdentifier 
```

