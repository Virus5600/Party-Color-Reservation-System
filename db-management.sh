#!/bin/bash
function goto
{
	label=$1
	cmd=$(sed -n "/$label:/{:a;n;p;ba};" $0 | grep -v ':$')
	eval "$cmd"
	exit
}

callback=${1:-"help"}

goto $callback

help:
echo ""
echo "help          Shows the list of sub commands."
echo "drop          Drops the tables from the cloud database."
echo "migrate       Migrates the tables to the cloud database."
echo "backup        Backups the tables from the cloud database."
goto endFile

mysqlTest:
{
	mysql -V &&
	echo "" &&
	goto $callback
} || {
	echo "Setting MySql..."
	echo ""
	export PATH=$PATH:"C:\wamp64\bin\mysql\mysql5.7.36\bin"
	goto $callback
}
goto endFile

drop:
callback="dropContinue"
goto mysqlTest
dropContinue:
echo ""
echo "Dropping..."
mysql -h migae5o25m2psr4q.cbetxkdyhwsb.us-east-1.rds.amazonaws.com -u pamw67f0epuedsl6 -pmeu96nwt8vec2d4p kyzi6brs12iblq22 < drop.sql
echo "Done."
goto endFile

migrate:
callback="migrateContinue"
goto mysqlTest
migrateContinue:
echo ""
echo "Migrating..."
mysql -h migae5o25m2psr4q.cbetxkdyhwsb.us-east-1.rds.amazonaws.com -u pamw67f0epuedsl6 -pmeu96nwt8vec2d4p kyzi6brs12iblq22 < migration.sql
echo "Done."
goto endFile

backup:
callback="backupContinue"
goto mysqlTest
backupContinue:
echo ""
echo "Doing Backup..."
mysql -h migae5o25m2psr4q.cbetxkdyhwsb.us-east-1.rds.amazonaws.com -u pamw67f0epuedsl6 -pmeu96nwt8vec2d4p kyzi6brs12iblq22 > backup.sql
echo "Done."
goto endFile

endFile:
exit