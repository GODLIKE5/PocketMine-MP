#!/bin/bash
DIR="$(cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd)"
cd "$DIR"

DO_LOOP="no"

while getopts "p:f:l" OPTION; do
	case $OPTION in
		p)
			PHP_BINARY="$OPTARG"
			;;
		f)
			POCKETMINE_FILE="$OPTARG"
			;;
		l)
			DO_LOOP="yes"
			;;
		\?)
			echo "Invalid option: -$OPTION$OPTARG" >&2
			exit 1
			;;
	esac
done

if [ "$PHP_BINARY" == "" ]; then
	if [ -f ./bin/php5/bin/php ]; then
		PHP_BINARY="./bin/php5/bin/php"
	elif [ type php 2>/dev/null ]; then
		PHP_BINARY=$(type -p php)
	else
		echo "Couldn't find a working PHP binary, please use the installer."
		exit 1
	fi
fi

if [ "$POCKETMINE_FILE" == "" ]; then
	if [ -f ./src/PocketMine/PocketMine.php ]; then
		POCKETMINE_FILE="./src/pocketmine/PocketMine.php"
	elif [ -f ./PocketMine-MP.phar ]; then
		POCKETMINE_FILE="./PocketMine-MP.phar"
	else
		echo "Couldn't find a valid PocketMine-MP installation"
		exit 1
	fi
fi

LOOPS=0

set +e
while [ "$LOOPS" -eq 0 ] || [ "$DO_LOOP" == "yes" ]; do
	if [ "$DO_LOOP" == "yes" ]; then
		"$PHP_BINARY" "$POCKETMINE_FILE" $@
	else
		exec "$PHP_BINARY" "$POCKETMINE_FILE" $@
	fi
	((LOOPS++))
done

if [ ${LOOPS} -gt 1 ]; then
	echo "Restarted $LOOPS times"
fi