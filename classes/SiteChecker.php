<?php

namespace classes;

class SiteChecker
{
	private $request;
	private $bodyCheckers = [];
	private $errors = [];
	
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	
	public function addBodyChecker(bodyChecker $bodyChecker)
	{
		$this->bodyCheckers[] = $bodyChecker;
	}
	
	public function isSiteWorking($url)
	{
		$this->request->setUrl($url);
		$this->request->send();
		$this->errors = $this->request->getErrors();
		
		if(count($this->errors))
		{
			return false;
		}
		
		$this->checkResponseBody($this->request->getBody());

		if(count($this->errors))
		{
			return false;
		}
		
		return true;
	}
	
	private function checkResponseBody($body)
	{
		foreach($this->bodyCheckers as $bodyChecker)
		{
			$result = $bodyChecker->check($body);
			if(true !== $result)
			{
				$this->errors[] = $result;
			}
		}
	}
	
	public function getErrors()
	{
		return $this->errors;
	}

}
