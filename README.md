# Test Task for ROISTAT

## Main Task
Create a parser of logs file. Example file is in access_log.

## Examples of usage

* ### Help:
        php parser.php -h
        // Usage: php parser -f FILE_OF_LOGS [-s FILE_TO_SAVE -p]
              
           Required flags:
           
           -f FILE_OF_LOGS - log's path file to parse
           
           Optional flags:
           
           -s FILE_TO_SAVE - path of file to save
           -p - print result to terminal

* ### Print JSON result:
        php parser.php -f ./access_log -p
        // {
               "views": 16,
               "urls": 5,
               "traffic": 187990,
               "crawlers": {
                   "Google": 2,
                   "Bing": 0,
                   "Baidu": 0,
                   "Yandex": 0
               },
               "statusCodes": {
                   "200": 14,
                   "301": 2
               }
           }

* ### Save JSON to file:
        php parser.php -f ./access_log -s output.json
        cat output.json
        // {
               "views": 16,
               "urls": 5,
               "traffic": 187990,
               "crawlers": {
                   "Google": 2,
                   "Bing": 0,
                   "Baidu": 0,
                   "Yandex": 0
               },
               "statusCodes": {
                   "200": 14,
                   "301": 2
               }
           }%
           
* ### Save JSON to file and print result:
        php parser.php -f ./access_log -s output.json -p
        // {
               "views": 16,
               "urls": 5,
               "traffic": 187990,
               "crawlers": {
                   "Google": 2,
                   "Bing": 0,
                   "Baidu": 0,
                   "Yandex": 0
               },
               "statusCodes": {
                   "200": 14,
                   "301": 2
               }
           }

## In cases of incorrect lines in log file:
        // Total was created but errors were in the head of output

        Server's status code is incorrect in line 12
        {
            "views": 15,
            "urls": 5,
            "traffic": 141448,
            "crawlers": {
                "Google": 2,
                "Bing": 0,
                "Baidu": 0,
                "Yandex": 0
            },
            "statusCodes": {
                "200": 13,
                "301": 2
            }
        }
