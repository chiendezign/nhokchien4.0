<?php

class main{
	public $db;
	public function __construct()
	{
		$this->db = new db('localhost','root','');
	}


}