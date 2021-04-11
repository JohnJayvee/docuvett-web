<?php

namespace App\Libraries\GSuite\Facade;

use Illuminate\Support\Facades\Facade;

class GSuite extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'GSuite';
	}
}