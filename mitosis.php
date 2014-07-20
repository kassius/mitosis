#!/usr/bin/php -q
<?php

/*
 * TODO:
 *
 * Check for requirements
 * Aliases (shell commands) (prefix %)
 * Variables (prefix $ or @) 
 * Embed Files (prefix +)
 * Built-in Commands / Functions (no prefix)
 * Embedded Modules Commands (prefix & or :)
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
	public $_argc, $_argv;

	public $InternalCommands;

	public function __construct($argc, $argv)
	{
		$this->_argc = $argc;
		$this->_argv = $argv;

		$this->InternalCommands = new MitosisInternal($this->_argc, $this->_argv);
	}

	public function Help()
	{
		$this->InternalCommands->ms_echo("Help");
	}
}

class MitosisInternal
{	
	public $_argc, $_argv;

	public $aux_line_end;

	public $the_file, $the_data;

	public $open_string, $close_string, $empty_string;
	
	public $where_open, $where_close, $where_empty;
	public $open_tag_length, $close_tag_length, $real_data_start, $length;

	public $is_empty;

	public function __construct($argc, $argv)
	{
		$this->_argc = $argc;
		$this->_argv = $argv;

		$this->aux_line_end = "";

		$this->opening_string = "/* --- start data ---" . $this->aux_line_end;
		$this->closing_string = "--- end data --- */" . $this->aux_line_end;
		$this->empty_string = "/* --- data --- */" . $this->aux_line_end;
	
	
		echo $this->the_data = $this->ms_read_data();
		$this->ms_write_data("ok");
	}

	public function ms_echo($string)
	{
		echo "{$string}{$this->aux_line_end}";
	}

	public function ms_read_data()
	{
		$this->the_file = file_get_contents($this->_argv[0]);

		$this->where_open = strpos($this->the_file, strtoupper($this->opening_string));
		$this->where_close = strrpos($this->the_file, strtoupper($this->closing_string));
		$this->where_empty = strpos($this->the_file, strtoupper($this->empty_string));

		if($this->where_empty !== false && ( $this->where_open === false && $this->where_close === false ))
		{
			// data is empty
			$this->is_empty = true;
			
			return 0;
		}
		else if($this->where_open !== false && $this->where_close !== false && ($this->where_open < $this->where_close) )
		{
			// there is data
			$this->open_tag_length = strlen($this->opening_string); // strpos starts from begin of opening tag
			$this->close_tag_length = strlen($this->closing_string); // strpos starts from begin of opening tag
			$this->real_data_start = $this->where_open + $this->open_tag_length;
			$this->length = $this->where_close - $this->real_data_start;
			$this->the_data = substr($this->the_file, $this->real_data_start, $this->length);

			return $data;
		}
		else
		{
			// there is error
			return -1;
		}

	}

	public function ms_write_data($data)
	{
		if($this->is_empty === true)
		{
			// $data = "Okay";
			$data_full = <<<EOT
{$this->opening_string}
{$data}
{$this->closing_string}
EOT;

			$close_tag_end = $this->where_close + $this->close_tag_length;
			$file = substr_replace($this->the_file, $data_full, $this->where_open, $close_tag_end);
			//file_put_contents($this->_argv[0], $file);
			echo <<<EOT
WHERE_OPEN {$this->where_open}
where_close {$this->where_close}
close_tag_end {$close_tag_end}
close_tag_length {$this->close_tag_length}
EOT;
		}
	}
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
