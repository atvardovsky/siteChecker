<?php

namespace classes;

interface Request
{	
	public function setUrl($url);
	
	public function send();
	
	public function getErrors();
	
	public function getBody();
	
}
