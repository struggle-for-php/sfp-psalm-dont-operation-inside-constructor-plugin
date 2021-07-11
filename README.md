Don't operation inside constructor!
===================================

```
$ ./vendor/bin/psalm
Scanning files...
Analyzing files...

░░░░░░░░░░░░░

ERROR: MethodOperationInsideConstructorIssue - src/app/Controllers/BlogController.php:15:9 - 'SplFileInfo::openfile' not allowed inside __construct()
        $log->openFile('w+');


ERROR: MethodOperationInsideConstructorIssue - src/app/models/Model.php:26:13 - 'PDO::query' not allowed inside __construct()
            $this->pdo->query($query);


------------------------------
2 errors found
------------------------------
```


## Features
* check operation **resources**
    * resource operation function list is provided by [struggle-for-php/resource-operations](https://github.com/struggle-for-php/resource-operations)
    * fork of [sebastianbergmann/resource-operations](https://github.com/sebastianbergmann/resource-operations)
    
## Todo
* support flexible setting xml to mark vendor libraries

## Disclaimer
this plugin is very experimental status.

## Installation
```
$ composer require --dev struggle-for-php/sfp-psalm-dont-operation-inside-constructor-plugin:dev-master
$ vendor/bin/psalm-plugin enable struggle-for-php/sfp-psalm-dont-operation-inside-constructor-plugin
```