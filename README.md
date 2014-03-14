siteChecker
===========
How to use:

php daemon.php [parameters]

Parameters:

1. logfile - absolute path to logfile
2. interval - interval between checkings default = 10
3. iterations - how much false iterations for change status of site default = 10
4. email - email for reports
5. url - url of site (like http://google.com)


 Example of usage:
 
 php ./daemon.php url=http://localhost/test.php email=test@te.te
