<?php

require_once("../src/BreakItDown.php");

class MainTest extends UnitTest
{
	private $dCallback = null;
	
	public function runOnce()
	{
		
	}
	
	public function setUpTest()
	{
		// for the tests we are changing this after the test is created by using a reflection to change the value of the variables
		$_SERVER['REQUEST_URI'] = "/";
		$_SERVER['REQUEST_METHOD'] = "GET";
		$this->dCallback = function(){};
	}
	
	public function testInitialSetup()
	{
		$refClass = new ReflectClass('BreakItDown', array(true, '/', $this->dCallback));
		
		$breakIt = $refClass->getReflection();
		
		//var_dump($breakIt->callbacks);
	}
	
	public function testRegisterCallback1()
	{
		$test1 = false;
		
		$refClass = new ReflectClass('BreakItDown', array(true, '/', $this->dCallback));
		
		$breakIt = $refClass->getReflection();
		
		$cb = function()use (&$test1)
		{
			$test1 = true;
		};
		
		$breakIt->registerCallback('GET', 'something/somewhere/[*]', $cb);
		
		$breakIt->uri = "/something/somewhere";
		$breakIt->run();
		
		assert($test1 === true);
	}
	
	public function testRegisterCallback2()
	{
		$test1 = false;
		
		$refClass = new ReflectClass('BreakItDown', array(true, '/', $this->dCallback));
		
		$breakIt = $refClass->getReflection();
		
		$cb = function()use (&$test1)
		{
			$test1 = true;
		};
		
		$breakIt->registerCallback('GET', 'something/[*]', $cb);
		
		$breakIt->uri = "/something/somewhere";
		$breakIt->run();
		
		assert($test1 === true);
	}

	public function testRegisterCallback3()
	{
		$test1 = false;
		$test2 = false;
		$test3 = false;
		
		$refClass = new ReflectClass('BreakItDown', array(true, '/', $this->dCallback));
		
		$breakIt = $refClass->getReflection();
		
		$cb = function()use (&$test1)
		{
			$test1 = true;
		};

		$cb2 = function()use (&$test2)
		{
			$test2 = true;
		};

		$cb3 = function()use (&$test3)
		{
			$test3 = true;
		};
		
		$breakIt->registerCallback('GET', 'something', $cb);
		$breakIt->registerCallback('GET', 'something/somewhere', $cb2);
		$breakIt->registerCallback('GET', 'something/somewhere/somehow', $cb3);
		
		$breakIt->uri = "/something/somewhere";
		$breakIt->run();
		
		assert($test1 === false);
		assert($test2 === true);
		assert($test3 === false);
	}
	
	public function testUriHtaccess1()
	{
		$test1 = false;
		
		$refClass = new ReflectClass('BreakItDown', array(false, '/', $this->dCallback));
		
		$breakIt = $refClass->getReflection();
		
		$cb = function()use (&$test1)
		{
			$test1 = true;
		};
		
		$breakIt->registerCallback('GET', 'something/[*]', $cb);
		
		$breakIt->uri = "/index.php/something/somewhere";
		
		$breakIt->htaccessUri();
	}
	
	public function testUriHtaccess2()
	{
		$test1 = false;
		
		$refClass = new ReflectClass('BreakItDown', array(false, '/', $this->dCallback));
		
		$breakIt = $refClass->getReflection();
		
		$cb = function()use (&$test1)
		{
			$test1 = true;
		};
		
		$breakIt->registerCallback('GET', '', $cb);
		
		$breakIt->uri = "/index.php";
		
		$breakIt->htaccessUri();
		
		$breakIt->run();
		
		assert($test1 === true);
	}

	public function testGetParams1()
	{
		$test1 = false;
		$_SERVER['REQUEST_URI'] = "/something/somewhere?test=5";
		
		$refClass = new ReflectClass('BreakItDown', array(true, '/', $this->dCallback));
		
		$breakIt = $refClass->getReflection();
		
		$cb = function()use (&$test1)
		{
			$test1 = true;
		};
		
		$breakIt->registerCallback('GET', 'something/[*]', $cb);
		
		//$breakIt->uri = "/something/somewhere";
		$breakIt->run();
		
		assert($test1 === true);
		assert($breakIt->uri === "/something/somewhere");
	}
}

?>