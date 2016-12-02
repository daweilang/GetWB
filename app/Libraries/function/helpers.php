<?php

if (! function_exists('dw_microtime')) {
	
	/**
	 * 获得微秒
	 *
	 */
	function dw_microtime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($usec)+floatval($sec))*1000);
	}
}
