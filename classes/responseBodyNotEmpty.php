<?php

namespace classes;

use classes\bodyChecker;

class responseBodyNotEmpty implements bodyChecker
{
	public function check($source)
	{
		if('' == $source)
		{
			return 'Page is empty';
		}
		
		return true;
	}
}
