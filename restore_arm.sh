rm -rf dump
unzip arm_backup
mongorestore --host 127.0.0.1 --port 27017 dump/
rm -rf dump