<?php

class Callback extends MX_Controller
{
	public function index($callback)
	{
		$this->plugins->$callback->callback();

		die();
	}
}