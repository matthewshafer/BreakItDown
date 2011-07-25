<?php

class BreakItDown
{
	protected $defaultCallback = null;
	protected $base = null;
	protected $callbacks = null;
	protected $uri = null;
	protected $requestMethod = null;
	
	
	public function __construct($htaccess, $base, $defaultCallback)
	{
		// fixes issues with exploding later on
		if(substr_compare($base, "/", -1) !== 0)
		{
			$base .= "/";
		}
		
		if(!is_callable($defaultCallback))
		{
			throw new Exception("Default callback is not callable");
		}
		
		$this->base = $base;
		$this->callbacks = array('GET' => array(), 'POST' => array());
		$this->defaultCallback = $defaultCallback;
		$this->uri = $_SERVER['REQUEST_URI'];
		
		if(!$htaccess)
		{
			$this->htaccessUri();
		}
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];
	}
	
	public function run()
	{
		$uriArr = null;
		$tmpCt = null;
		$callback = $this->defaultCallback;
		$cbArr = &$this->callbacks[$this->requestMethod];
		$uri = $this->uri;
		$base = $this->base;
		
		// adds a trailing slash to the base address if one does not exist fixes problems later with exploding strings and stuff
		if(substr_compare($base, "/", -1) !== 0)
		{
			$base .= "/";
		}
		
		// removes a trailing slash from a URI. Solves issues with array's who are exploded having somethign extra at the end
		// we go in here when the uri is / and end up making the uri ""
		// in the next statement block's else we would set the uri to false if it were to be changed to "" in this statement
		// this isn't a problem because we set the uri correctly if the substr fails in the next statements else block.
		if($uri !== "" && substr_compare($uri, "/", -1) === 0)
		{
			$uri = substr($uri, 0, -1);
		}
		
		
		// figure out what happens when using htaccess
		// if its null we are going to assume its /
		// explodes the uri around the base address
		// leaving us with the actual request uri
		if($base !== "/" && $base !== null)
		{
			$temp = explode($base, $uri);
			
			if(isset($temp[1]))
			{
				$uri = $temp[1];
			}
			else
			{
				$uri = "";
			}
		}
		// this else statement is here to strip off the first / as it will explode to nothing in an array
		else
		{	
			// fixed the above issue by checking if the substr function returned false, if it did then we set $this->uri to an empty string (else it would be set to a boolean(false) value
			if(($uri = substr($uri, 1)) === false)
			{
				$uri = "";
				//printf("here\n");
			}
			
		}
		
		$uriArr = explode("/", $uri);
		
		//var_dump($cbArr);
		//var_dump($uriArr);
		
		$tmpCt = count($uriArr);
		
		// removes the end if there is a trailing slash and we made that position empty
		// if the uri is just "" which would be if it were a / then we ignore the empty value
		// and keep it so things work correctly
		if(empty($uriArr[$tmpCt-1]) && $uri !== "")
		{
			unset($uriArr[$tmpCt-1]);
		}
		
		for($i = 0; $i < $tmpCt; $i++)
		{
			if(isset($cbArr[$uriArr[$i]]))
			{
				if(is_array($cbArr[$uriArr[$i]]))
				{
					$cbArr = &$cbArr[$uriArr[$i]];
				}
				else
				{
					$callback = $cbArr[$uriArr[$i]];
					//var_dump($callback);
					break;
				}
			}
			else
			{
				break;
			}
		}
		
		// checks for a wildcard if the current value in the uri does not match
		// something like the uri being /test/123/hey
		// and the callback is test/[*]
		if(isset($cbArr['[*]']))
		{
			$callback = $cbArr['[*]'];
		}
		// running the callback
		
		//var_dump($callback);
		$callback();
	}
	
	public function registerCallback($reqType, $uri, $callback)
	{
		$refArray = null;
		
		if(!is_callable($callback))
		{
			throw new Exception("Unable to register callback, not callable");
		}
		
		if($reqType === 'GET')
		{
			$refArray = &$this->callbacks['GET'];
		}
		else
		{
			$refArray = &$this->callbacks['POST'];
		}
		
		$regArr = explode('/', $uri);
		
		$ct = count($regArr);
		
		for($i = 0; $i < $ct; $i++)
		{
			if(!isset($refArray[$regArr[$i]]))
			{
				$refArray[$regArr[$i]] = array();
			}
			
			$refArray = &$refArray[$regArr[$i]];
		}
		$refArray = $callback;
		
	}
	
	private function cleanUri($uri)
	{
		// adds a slash to the begining
		if($uri[0] !== '/')
		{
			$uri = '/' . $uri;
		}
		
		// removing trailing slash
		if(substr_compare($uri, "/", -1) === 0)
		{
			$uri = substr($uri, 0, -1);
		}
		
		return $uri;
	}
	
	private function htaccessUri()
	{
			$temp = explode("index.php", $this->uri);
			
			if(isset($temp[1]))
			{
				$this->uri = $temp[1];
			}
			else
			{
				$this->uri = "";
			}

	}
}

?>