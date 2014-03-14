#!/usr/bin/php
<?php
use classes\SiteChecker;
use classes\CurlRequest;
use classes\responseBodyNotEmpty;

spl_autoload_register(function ($class) {
	
      require_once __DIR__ . '/' . str_replace('\\', '/', $class). '.php'; 
});

$pid = pcntl_fork();
if ($pid === -1) {
	die('Could not fork!');
} elseif ($pid) {	
	exit;
} else {
	// this is the child process
	posix_setsid();
	$parameters = [
		'logfile' => false,
		'interval' => 10,
		'iterations' => 10,
		'email' => false,
		'url' => ''
	];
	$inputParameters = $argv;
	foreach($inputParameters as $index=>$parameter)
	{		
		if(0 == $index)
		{
			continue;
		}
		$tmpArr = explode('=', $parameter);
		$parameters[$tmpArr[0]] = $tmpArr[1];
	}
	if(!$parameters['url'])
	{
		exit('need url');
	}
	$c = new CurlRequest();
	$c->setTimeLimit(20);

	$checker = new SiteChecker($c);
	$checker->addBodyChecker(new responseBodyNotEmpty());	
	$prevStatus = true;
	$iteration = 0;
	$logfile = $parameters['logfile'];
	$email = $parameters['email'];
	if($logfile)
	{
		file_put_contents($logfile,'');
	}
	while(1) {
		$iteration++;
		$url = $parameters['url'];
		$status = $checker->isSiteWorking($url);
		$errors = [];
		
		if(!$status)
		{
			$errors = $checker->getErrors();

		}
		
		if($prevStatus != $status)
		{
			$message = '';
			if(count($errors) && $iteration == $parameters['iterations'])
			{
				$message = 'Site ' . $url . ' is down, reasons: ' . "\n";
				
				foreach($errors as $error)
				{
					$message .= $error . "\n";
				}
			}
			if(!count($errors))
			{
				$message = 'Site '. $url . ' in normal status' . "\n";
			}
			if($message)
			{
					if($logfile)
					{
						$content = file_get_contents($logfile);
						file_put_contents($logfile, date('Y-m-d H:i:s') . "\n" . $content . "\n" . $message);
					}
					if($email)
					{
						$subject = 'the subject';
						mail($email, 'Watcher\'s report', $message);
					}
				$prevStatus = $status;
			}			
		}
		
		sleep($parameters['interval']);
		
	}
}

