#!/usr/bin/php -q
<?php

/*
 * TODO:
 *
 * Check for requirements
 * Aliases (shell commands) (prefix @)
 * Variables (prefix $)
 * Embed Files (prefix +)
 * Built-in Commands / Functions (no prefix)
 * Embedded Modules Commands (prefix &)
 *
 * PREFIXES ARE JUST AN IDEA. COULD NOT BE USED THIS BECAUSE ARE USED BY BASH
 *
 *   These items (commands, files, vars, &c):
 *   * Add (new)
 *   * Remove
 *   * List
 *
 * */
class MitosisCommands
{
	public $InternalCommands;

	public function __construct()
	{
		$this->InternalCommands = new MitosisInternal();
	}

	public function Help()
	{
		$this->InternalCommands->ms_echo("Help");
	}
}

class MitosisInternal
{	
	private $aux_line_end;

	public $the_data;

	public $open_string, $close_string, $empty_string;

	public function __construct($argc, $argv)
	{
		$this->opening_string = "--- start data ---";
		$this->closing_string = "--- end data ---";
		$this->empty_string = "--- data ---";
	
		$this->aux_line_end = "\n\r";
	
		$this->the_data = $this->ms_read_data($argc, $argv);
	}

	public function ms_echo($string)
	{
		echo "{$string}{$this->aux_line_end}";
	}

	public function ms_read_data($argc, $argv)
	{
		$the_file = file_get_contents($argv[0]);

		$where_open = strpos($the_file, strtoupper($this->opening_string));
		$where_close = strpos($the_file, strtoupper($this->closing_string));
		$where_empty = strpos($the_file, strtoupper($this->empty_string));

		if($where_empty !== false && ( $where_open === false && $where_close === false ))
		{
			// data is empty
		}
		else if($where_open !== false && $where_close !== false && () )
		else

	}

	public function ms_write();
}

class Mitosis
{
	//public $MitosisCommands;
	public $command;

	public function __construct($argc, $argv)
	{
		$MC = new MitosisCommands($argc, $argv);

		if($argc < 2)
		{
			$MC->Help();
		}
		else if($argc == 2)
		{
		}
		else if($argc > 2)
		{
		}
	}
}

/* --- DATA --- */
	
$OurMitosis = new Mitosis($argc, $argv);

?>
