#!/usr/bin/php -q
<?php

error_reporting(E_ALL & ~E_NOTICE);

/*
 * TODO:
 *
 * Check for requirements
 * Aliases (shell commands) (prefix %)
 * Variables / Registers (prefix $ or @) 
 * Embed Files (prefix +)
 * Built-in Commands / Functions (no prefix)
 * Embedded Modules Commands (prefix & or :)
 * Internal options/flags (prefix - or --)
 *  ^ THESE ARE ALL ITEMS
 * 
 * Maybe, it could be good to do directories, like
 * @var1/directory/subdir
 * +file/dir/subdir
 *    ETC
 * 
 * Modular, loads only what is necessary
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

	public $IC;

	public function __construct($argc, $argv)
	{
		$this->_argc = $argc;
		$this->_argv = $argv;

		$this->IC = new MitosisInternal($this->_argc, $this->_argv);
	}

	public function Execute()
	{
		$argc = $this->_argc;
		$argv = $this->_argv;
		
		if(preg_match("/^[a-zA-Z]{1,30}$/", $argv[1])) /* regular expression matchin a-z,A-Z */
		{
			// command
			// add, list, remove
			
			$command = $argv[1];
			
			if($this->IC->ms_command("ls", $command))
			{
					if (count($this->IC->content_array['variables'])>0)
					{
							$vars = implode(", ", array_keys($this->IC->content_array['variables']));
							$vars_str = "Variables: {$vars}\r\n";
					}
					if (count($this->IC->content_array['aliases'])>0)
					{
							$aliases = implode(" ", array_keys($this->IC->content_array['aliases']));
							$aliases_str = "Aliases: {$aliases}\r\n";
					}
					if (count($this->IC->content_array['files'])>0)
					{
							$files = implode(" ", array_keys($this->IC->content_array['files']));
							$files_str = "Files: {$files}\r\n";
					}
					if (count($this->IC->content_array['options'])>0)
					{
							$options = implode(" ", array_keys($this->IC->content_array['options']));
							$options_str = "Options: {$options}\r\n";
					}
					if (count($this->IC->content_array['modules'])>0)
					{
							$modules = implode(" ", array_keys($this->IC->content_array['modules']));
							$modules_str = "Modules: {$modules}\r\n";
					}
					
					echo <<<EOT
{$vars_str}{$aliases_str}{$files_str}{$options_str}{$modules_str}
EOT;
			}
			else if($this->IC->ms_command("rm", $command))
			{
				if($argc === 2)
				{
					// print arguments
					$this->IC->ms_echo("rm: Remove items\r\nUsage: rm <item>");
				}
				else
				{
					if(preg_match("/^@[a-zA-Z]{1,30}$/", $argv[2]))
					{
						$var = $argv[2];
						
						if(isset($this->IC->content_array['variables']["$var"]))
						{
							unset($this->IC->content_array['variables']["$var"]);
							$this->IC->ms_write_data($this->IC->content_array);
						}
						else
						{
							$this->IC->ms_echo("variable '{$var}' not found");
						}
					}
					else if(preg_match("/^%[a-zA-Z]{1,30}$/", $argv[2]))
					{
						$alias = $argv[2];
						
						if(isset($this->IC->content_array['aliases']["$alias"]))
						{
							unset($this->IC->content_array['aliases']["$alias"]);
							$this->IC->ms_write_data($this->IC->content_array);
						}
						else
						{
							$this->IC->ms_echo("alias '{$alias}' not found");
						}
					}
					else if(preg_match("/^\+[a-zA-Z]{1,30}$/", $argv[2]))
					{
						// file
						//$this->IC->ms_echo("file: {$argv[1]}");
						$file = $argv[2];
						
						if(isset($this->IC->content_array['files']["$file"]))
						{
							unset($this->IC->content_array['files']["$file"]);
							$this->IC->ms_write_data($this->IC->content_array);
						}
						else
						{
							$this->IC->ms_echo("file '{$file}' not found");
						}
					}
					else if(preg_match("/^-[a-zA-Z]{1,30}$/", $argv[2]))
					{
						// USE UNSET
						// file
						//$this->IC->ms_echo("file: {$argv[1]}");
						$option = $argv[2];
						
						if(isset($this->IC->content_array['options']["$option"]))
						{
							unset($this->IC->content_array['options']["$option"]);
							$this->IC->ms_write_data($this->IC->content_array);
						}
						else
						{
							$this->IC->ms_echo("option '{$option}' not found");
						}
					}
					else if(preg_match("/^:[a-zA-Z]{1,30}$/", $argv[2]))
					{
						$module = $argv[2];
						
						if(isset($this->IC->content_array['modules']["$module"]))
						{
							unset($this->IC->content_array['modules']["$module"]);
							$this->IC->ms_write_data($this->IC->content_array);
						}
						else
						{
							$this->IC->ms_echo("module '{$module}' not found");
						}
					}
				}
			}
			else
			{
				$this->IC->ms_echo("command '{$command}' not found");
			}
		}
		else if(preg_match("/^@[a-zA-Z]{1,30}$/", $argv[1]))
		{
			// variable
			$var = $argv[1];
			
			if($argc === 2)
			{
				if (isset($this->IC->content_array['variables']["$var"]))
				{
					$value = $this->IC->content_array['variables']["$var"];
					$this->IC->ms_echo("{$var}: '{$value}'");
				}
				else
				{
					$this->IC->ms_echo("variable '{$var}' is not set");
				}
			}
			else
			{
				// attribute arvg[>1] to variable 
				array_shift($argv);
				array_shift($argv);
				$value = implode(" ", $argv);
				$this->IC->content_array['variables']["$var"] = $value;

				$this->IC->ms_write_data($this->IC->content_array);
				$this->IC->ms_read_data();
				$this->IC->ms_echo("{$var} set to '{$this->IC->content_array['variables']["$var"]}'");
			}
			
		}
		else if(preg_match("/^%[a-zA-Z]{1,30}$/", $argv[1]))
		{
			// alias
			
			$alias = $argv[1];
			
			if($argc === 2)
			{
				if (isset($this->IC->content_array['aliases']["$alias"]))
				{
					$value = $this->IC->content_array['aliases']["$alias"];
					$this->IC->ms_echo("{$value}");
					passthru($value);
				}
				else
				{
					$this->IC->ms_echo("alias '{$alias}' is not set");
				}
			}
			else
			{
				// attribute arvg[>1] to variable 
				array_shift($argv);
				array_shift($argv);
				$value = implode(" ", $argv);
				$this->IC->content_array['aliases']["$alias"] = $value;

				$this->IC->ms_write_data($this->IC->content_array);
				$this->IC->ms_read_data();
				$this->IC->ms_echo("{$alias} set to execute '{$this->IC->content_array['aliases']["$alias"]}'");
			}
		}
		else if(preg_match("/^\+[a-zA-Z]{1,30}$/", $argv[1]))
		{
			// file
			$file = $argv[1];
			//$this->IC->ms_echo("file: {$argv[1]}");
			
			if($argc === 2)
			{
				if (isset($this->IC->content_array['files']["$file"]))
				{
					$value = $this->IC->content_array['files']["$file"];
					$this->IC->ms_echo("{$file}: '{$value}'");
				}
				else
				{
					$this->IC->ms_echo("file '{$file}' not found");
				}
			}			
			if($argc === 2)
			{
				if (isset($this->IC->content_array['variables']["$var"]))
				{
					$value = $this->IC->content_array['variables']["$var"];
					$this->IC->ms_echo("{$var}: '{$value}'");
				}
				else
				{
					$this->IC->ms_echo("variable '{$var}' is not set");
				}
			}
		}
		else if(preg_match("/^-[a-zA-Z]{1,30}$/", $argv[1]))
		{
			// option / set flag
			$this->IC->ms_echo("option: {$argv[1]}");
		}
		else if(preg_match("/^:[a-zA-Z]{1,30}$/", $argv[1]))
		{
			// module
			$this->IC->ms_echo("module: {$argv[1]}");
		}
		else
		{
			$this->IC->ms_echo("'{$argv[1]}': unknown action.");
		}
	}
	public function Help()
	{
		$this->IC->ms_echo("Help");
	}
}

class MitosisInternal
{	
	public $_argc, $_argv;

	public $aux_line_end;

	public $the_file, $the_data;

	public $open_string, $close_string, $empty_string;
	
	public $where_open, $where_close, $where_empty;
	public $open_tag_length, $close_tag_length, $empty_tag_length, $real_data_start, $stripped_length, $full_length;

	public $is_empty;
	
	public $content_array, $content_empty_model;

	public function __construct($argc, $argv)
	{
		$this->_argc = $argc;
		$this->_argv = $argv;

		$this->aux_line_end = "\n";

		$this->opening_string = "/* --- start data ---" . $this->aux_line_end;
		$this->closing_string = "--- end data --- */" . $this->aux_line_end;
		$this->empty_string = "/* --- data --- */" . $this->aux_line_end;
	
		$this->content_empty_model = array(
			'variables' => array(),
			'aliases' => array(),
			'files' => array(),
			'options' => array(),
			'modules' => array(),
		);
			
		$this->the_data = $this->ms_read_data();
		//$this->ms_write_data("ok");
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
			$this->empty_tag_length = strlen($this->empty_string);
			
			return 0;
		}
		else if($this->where_open !== false && $this->where_close !== false && ($this->where_open < $this->where_close) )
		{
			// there is data
			$this->is_empty = false;
			
			$this->open_tag_length = strlen($this->opening_string); // strpos starts from begin of opening tag
			$this->close_tag_length = strlen($this->closing_string); // strpos starts from begin of opening tag

			$this->real_data_start = $this->where_open + $this->open_tag_length;
			$this->stripped_length = $this->where_close - $this->real_data_start;
			$this->full_length = ($this->where_close + $this->close_tag_length) - $this->where_open;
			
			$this->the_data = substr($this->the_file, $this->real_data_start, $this->stripped_length);

			$content_json = base64_decode($this->the_data);
			$this->content_array = json_decode($content_json, true);
			
			return $this->the_data;
		}
		else
		{
			// there is error
			return -1;
		}

	}

	public function ms_write_data($content_array)
	{
		if(!$this->Is_Array_Empty($content_array)) // file will be written
		{
			$content_json = json_encode($content_array);
			$content_towrite = base64_encode($content_json);
	
			$op_str_upper = strtoupper($this->opening_string);
			$cl_str_upper = strtoupper($this->closing_string);
				
			if($this->is_empty === true) // file cur empty
			{
				$begin = $this->where_empty;
				$end = $this->empty_tag_length;
			}
			else if($this->is_empty === false) // file not cur empty
			{
				$begin = $this->where_open; // start data
				$end = $this->full_length;
			}

			$data_full = <<<EOT
{$op_str_upper}
{$content_towrite}
{$cl_str_upper}
EOT;
			$file = substr_replace($this->the_file, $data_full, $begin, $end);
			file_put_contents($this->_argv[0], $file);
		}
		else
		{
			if($this->is_empty === true) // file cur empty
			{
				// DO NOTHING: IS EMPTY AND WILL CONTINUE
			}
			else if($this->is_empty === false) // file not cur empty
			{
				$begin = $this->where_open; // start data
				//$end = $this->where_close + $this->close_tag_length;
				$end = $this->full_length;

				$data_full = strtoupper($this->empty_string);
				
				$file = substr_replace($this->the_file, $data_full, $begin, $end);
				file_put_contents($this->_argv[0], $file);
			}
		}
	}
	
	public function ms_command($command, $argument)
	{
		if( strcmp($command, $argument) === 0 ) return true;
		else return false;
	}

	public function Is_Array_Empty($content_array)
	{
		if( count($content_array['variables']) === 0 &&
			count($content_array['aliases']) === 0 &&
			count($content_array['files']) === 0 &&
			count($content_array['options']) === 0 &&
			count($content_array['modules']) === 0 )
		{
			return true;
		}
		else
		{
			return false;
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
		else
		{
			$return = $MC->Execute();
		}
	}
}

/* --- DATA --- */

$OurMitosis = new Mitosis($argc, $argv);

?>
