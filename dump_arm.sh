rm -f arm_backup.zip
rm -rf dump
mongodump --db arm --collection roles
mongodump --db arm --collection accounts
zip -r arm_backup.zip dump
rm -rf dump
