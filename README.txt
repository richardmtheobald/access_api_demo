To install:

1. unzip into "document root"/rtdemo folder. That is, conf.php should be located at ...wherever.../www/rtdemo/conf.php

2. execute rtDemo.sql from within mysql to create the database and all of the tables

3. update the database object information variables in conf.php so that it is able to connect to the newly created database

4. In your browser, navigate to http://localhost/rtdemo/index.php

Notes:
* There is no default user, so you will need to create a user
* zip code based rather than city based
* it will re-use the temperature request for up to an hour, so you may want to manufacture database records for testing purposes
* If a user has 3 failed requests within a 5 minute window, that user will not be able to log in. They will need to wait for the 5 minutes to pass before being able to try again.

Future development that I wanted to do, but was running out of time for:
* There is a lot of html markup that is very similar. This should be abstracted out using some kind of framework.
* Add Facebook/Twitter oauth support.
* Add Remember Me support with a cookie, or at least localStorage.

Possible bugs:
Using Ampps on my Windows machine I was having trouble using cURL (I've never encountered this problem before, so you may or may not receive it). I kept receiving the error "SSL certificate problem: unable to get local issuer certificate". I was able to solve this solution by using the instructions on http://promincproductions.com/blog/how-to-fix-ssl-certificate-problem-unable-to-get-local-issuer-certificate/ .