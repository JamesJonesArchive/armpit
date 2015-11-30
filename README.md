# ARMpit

Provides the import mechanism for ARM data to Mongo

BUILD

After cloning the package, run `build.sh`. This will create an `armpit.phar`
in the bin folder. Note: php.ini must have phar.readonly = Off set to build 
the phar file

USAGE

There are 3 option for importing:

## roles
Imports the roles
```
armpit.phar import:roles --type <SYSTEM_TYPE> <filename.json>
```

## accounts
Imports the accounts
```
armpit.phar import:accounts --type <SYSTEM_TYPE> <filename.json>
```

## mapping
Imports the mapping of roles to accounts
```
armpit.phar import:mapping <filename.json>
```

There are also function for dumping the mongo data and restoring it.

## export
Export ARM mongo data to zipped dump
```
armpit.phar backup:export --out <output_folder>
```

## import
Restore ARM mongo data from zipped dump
```
armpit.phar backup:import <filename.zip>
```

There are functions for deleting accounts and roles.

## delete account
Delete an account by href
```
armpit.phar delete:account <href>
```

## delete role
Delete a role by href
```
armpit.phar delete:role <href>
```
## set review
Set a review on accounts by a system type
```
armpit.phar review:accounts --type <type>
```

Set a review on an identity by a system type
```
armpit.phar review:accounts --type <type> --usfid <usfid>
```

Set a review for all accounts of an identity
```
armpit.phar review:accounts --usfid <usfid>
```

Set a review on an account by a system type and identifier
```
armpit.phar review:accounts --type <type> --identifier <identifier>
```
