#!/bin/bash

. include.sh
. take-previous.sh

if [ "$SB" != "" ]; then
    if [ -d "$SB/websites" ]; then
        log "Copying websites..."
	cp -a "$SB/websites" "$BUILD_NUMBER"
	check_failure $?
    fi

    if [ -d "$SB/media" ]; then
        log "Copying media..."
	cp -a "$SB/media" "$BUILD_NUMBER" 
        check_failure $?
    fi

    echo 'SHOW DATABASES;' | mysql -u root | grep $SB_DB > /dev/null
    if [ "$?" -eq 0 ] ; then
	log "Copying DB..."
        mysqldump -u root $SB_DB | mysql -u root $DB_NAME
        check_failure $? 
        ch_baseurl $BUILD_NUMBER $DB_NAME
    fi
fi
