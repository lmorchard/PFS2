Mozilla Plugin Finder Service (PFS) 2
=====================================
* https://wiki.mozilla.org/PFS2
* lorchard@mozilla.com

INSTALLATION
------------

mysql> CREATE DATABASE pfs2
$ mysql pfs2 < conf/schema.sql 

Configure Apache's document root to be pfs2/trunk/htdocs

cp config.php-dist config.php

Edit config.php and update
* primary_database, shadow_databases, and memcache_config

Ensure memcached is running.

Finally, to load the DB with data from JSON plugin definitions:

$ php bin/update-db.php

USAGE
-----

$ curl -s 'http://pfs2.example.com/?appID={ec8030f7-c20a-464f-9b0e-13a3a9e97384}&mimetype=application/x-shockwave-flash&appVersion=2008052906&appRelease=3.0&clientOS=Windows%20NT%205.1&chromeLocale=en-US&callback=during_dinner'

during_dinner({
    "adobe-flash-player": {
        "releases": {
            "11.0.0.0": {
                "status": "latest", 
                "app_release": "3.5", 
                "app_version": "*", 
                "vendor": "Adobe", 
                "pfs_id": "adobe-flash-player", 
                "url": "http://www.adobe.com/go/getflashplayer", 
                "modified": "2009-09-16T00:45:46+00:00", 
                "app_id": "{ec8030f7-c20a-464f-9b0e-13a3a9e97384}", 
                "locale": "ja-JP", 
                "version": "11.0.0.0", 
                "license_url": "http://www.adobe.com/go/eula_flashplayer_jp", 
                "guid": "{89977581-9028-4be0-b151-7c4f9bcd3211}", 
                "xpi_location": "http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-mac.xpi", 
                "os_name": "mac", 
                "name": "Adobe Flash Player"
            }, 
            "10.0.32.18": {
                "status": "vulnerable", 
                "app_release": "3.5", 
                "app_version": "*", 
                "vendor": "Adobe", 
                "pfs_id": "adobe-flash-player", 
                "url": "http://www.adobe.com/go/getflashplayer", 
                "modified": "2009-09-16T00:45:46+00:00", 
                "app_id": "{ec8030f7-c20a-464f-9b0e-13a3a9e97384}", 
                "vulnerability_description": "Makes your computer kick puppies", 
                "vulnerability_url": "http://google.com", 
                "version": "10.0.32.18", 
                "license_url": "http://www.adobe.com/go/eula_flashplayer_jp", 
                "locale": "ja-JP", 
                "guid": "{89977581-9028-4be0-b151-7c4f9bcd3211}", 
                "xpi_location": "http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-mac.xpi", 
                "os_name": "mac", 
                "name": "Adobe Flash Player"
            }, 
            "10.0.22.87": {
                "status": "vulnerable", 
                "app_release": "*", 
                "os_name": "*", 
                "vendor": "Adobe", 
                "pfs_id": "adobe-flash-player", 
                "url": "http://www.adobe.com/go/getflashplayer", 
                "modified": "2009-09-16T00:45:46+00:00", 
                "app_id": "{ec8030f7-c20a-464f-9b0e-13a3a9e97384}", 
                "vulnerability_url": "http://www.adobe.com/support/security/bulletins/apsb09-10.html", 
                "version": "10.0.22.87", 
                "license_url": "http://www.adobe.com/go/eula_flashplayer", 
                "locale": "*", 
                "app_version": "*", 
                "name": "Adobe Flash Player"
            }, 
            "9.0.159.0": {
                "status": "vulnerable", 
                "app_release": "*", 
                "os_name": "*", 
                "vendor": "Adobe", 
                "pfs_id": "adobe-flash-player", 
                "url": "http://www.adobe.com/go/getflashplayer", 
                "modified": "2009-09-16T00:45:46+00:00", 
                "app_id": "{ec8030f7-c20a-464f-9b0e-13a3a9e97384}", 
                "vulnerability_url": "http://www.adobe.com/support/security/bulletins/apsb09-10.html", 
                "version": "9.0.159.0", 
                "license_url": "http://www.adobe.com/go/eula_flashplayer", 
                "locale": "*", 
                "app_version": "*", 
                "name": "Adobe Flash Player"
            }, 
            "9.0.115.0": {
                "status": "vulnerable", 
                "app_release": "*", 
                "os_name": "*", 
                "vendor": "Adobe", 
                "pfs_id": "adobe-flash-player", 
                "url": "http://www.adobe.com/go/getflashplayer", 
                "modified": "2009-09-16T00:45:46+00:00", 
                "app_id": "{ec8030f7-c20a-464f-9b0e-13a3a9e97384}", 
                "vulnerability_url": "http://documents.iss.net/whitepapers/IBM_X-Force_WP_final.pdf", 
                "version": "9.0.115.0", 
                "license_url": "http://www.adobe.com/go/eula_flashplayer", 
                "locale": "*", 
                "app_version": "*", 
                "name": "Adobe Flash Player"
            }
        }, 
        "aliases": [
            "Adobe Flash Player"
        ]
    }
})

