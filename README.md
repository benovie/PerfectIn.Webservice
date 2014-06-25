PerfectIn.Webservice is a TYPO3.Flow package to create webservices for existing code


## Example webservice - Find all TYPO3\Flow\Security\Role with REST

webservices.yaml:

```
-
  name: Roles
  operations:
    -
      name: findAll
        - 
          type: rest
          options:
            url: webservice/security/role
            method: GET
      implementation:
        class: TYPO3\Flow\Security\Policy\RoleRepository
        method: findAll 
 
```       
    
## Example webservice - Find one TYPO3\Flow\Security\Role with REST
        

## Example webservice - Find all TYPO3\Flow\Security\Roles with REST

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

> Note that {identifier} is automatically mapped to $identifier parameter
