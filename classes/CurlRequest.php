<?php

namespace classes;
use classes\Request;

class CurlRequest implements Request
{

	private $errors = [];
	private $url = '';
	private $responseBody;
	private $limit = 0;
	
	public function setUrl($url)
	{
		$this->url = $url;
	}
	
	public function setTimeLimit($limit)
	{
		$this->limit = $limit;
	}
	
	public function send()
	{
		$this->errors = [];
		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->limit);
		$this->responseBody = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);		
		if($this->isHttpError($responseCode))
		{
			$this->errors[] = 'code of http response: ' . $responseCode;
		}
		$curlError = curl_error($ch);
		
		if($curlError)
		{
			$this->errors[] = $curlError;
		}
		curl_close($ch);
	}
	
	private function isHttpError($code)
	{		
		if(400 <= $code)
		{
			return true;
		}
		return false;
	}
	
	public function getErrors()
	{
		return $this->errors;
	}
	
	public function getBody()
	{
		return $this->responseBody;
	}

}
