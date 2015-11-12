# ARMimport

Provides the import mechanism for ARM data to Mongo

BUILD

After cloning the package, run `build.sh`. This will create an `armimport.phar`
in the bin folder. Note: php.ini must have phar.readonly = Off set to build 
the phar file

USAGE

There are 3 option for importing:

## roles
Imports the roles
```
armimport.phar import:roles --type <SYSTEM_TYPE> <filename.json>
```

## accounts
Imports the accounts
```
armimport.phar import:accounts --type <SYSTEM_TYPE> <filename.json>
```

## mapping
Imports the mapping of roles to accounts
```
armimport.phar import:mapping <filename.json>
```

There are also function for dumping the mongo data and restoring it.

## export
Export ARM mongo data to zipped dump
```
armimport.phar backup:export --out <output_folder>
```

## import
Restore ARM mongo data from zipped dump
```
armimport.phar backup:import <filename.zip>
```

There are functions for deleting accounts and roles.

## delete account
Delete an account by href
```
armimport.phar delete:account <href>
```

## delete role
Delete a role by href
```
armimport.phar delete:role <href>
```

