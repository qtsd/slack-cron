Introduction
------------
A simple tool writen in PHP using some symfony components.  
Used to execute a command and send errors or output to slack using [Incoming Webhooks](https://api.slack.com/incoming-webhooksincoming) (usefull for cron jobs).

Requirements
------------
* PHP 5.6 and up.
* cURL accessible from path.
* composer to install dependencies.


Installation
------------
Via composer
``` sh
$ composer create-project qtsd/slack-cron slack-cron dev-master --keep-vcs
```

From sources
``` sh
$ git clone git@github.com:qtsd/slack-cron.git
$ composer install -d slack-cron
```

Configuration
-------------
**Path**  
You can link the app to the path to use it from everywhere.  
The symlink should conventionally be on */usr/local/bin*, but cron path do not include it, so use */usr/bin instead*.
``` sh
$ sudo ln -s `pwd`/slack-cron/bin/console /usr/bin/slack-cron
```  
   
**Slack**  
Slack webhook url is asked on *composer install*, and will be kept in *conf/parameters.yml*.  
To get a webhook url, go on Slack > [Getting started with custom integrations](https://api.slack.com/custom-integrations) > New webhook.


Documentation
-------------
**Usage**  
run [options] \<exec\> [\<path\>]  

**Arguments**  
* **exec**  *The command to execute.*  
* **path**   *The working directory.*

**Options**
*  **--timeout[=TIMEOUT]**  *Command timeout in seconds [default: 120]*
* **--output-all** *Send all output to slack (not just errors)*

Examples
-------------
Call from path
``` sh
$ slack-cron run [...]
```
Call from install directory
``` sh
$ bin/console run [...]
```
List files of the current directory, and send output to slack
``` sh
$ slack-cron run 'ls -al' --output-all
```
Try to create a file /foo.txt, and send output to slack if encounter an error
``` sh
$ slack-cron run 'touch foo' /
```

License
-------
Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
