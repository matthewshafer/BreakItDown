<?php

require_once("../src/BreakItDown.php");

class PerformanceTest extends UnitTest
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
	
	public function testPerformance1()
	{
		$test1 = 0;
		$runAmount = 10000;
		
		$refClass = new ReflectClass('BreakItDown', array(true, '/', $this->dCallback));
		
		$breakIt = $refClass->getReflection();
		
		$cb = function()use (&$test1)
		{
			++$test1;
		};
		
		$breakIt->registerCallback('GET', 'something/somewhere/[*]', $cb);
		
		$breakIt->uri = "/something/somewhere";
		
		$start = microtime(true);
		
		for($i = 0; $i < $runAmount; $i++)
		{
			$breakIt->run();
		}
		
		$end = microtime(true);
		
		printf("total time to run simple callback %d times was %f seconds\n", $runAmount, ($end - $start));
		
		assert($test1 === $runAmount);
	}
	
	public function testPerformanceLong()
	{
		$test1 = 0;
		$runAmount = 10000;
		
		$refClass = new ReflectClass('BreakItDown', array(true, '/', $this->dCallback));
		
		$breakIt = $refClass->getReflection();
		
		$cb = function()use (&$test1)
		{
			++$test1;
		};
		
		$breakIt->registerCallback('GET', 'something/somewhere/is/here/but/we/need/to/make/this/long/[*]', $cb);
		
		
		$breakIt->uri = "/something/somewhere/is/here/but/we/need/to/make/this/long";
		
		
		$start = microtime(true);
		
		for($i = 0; $i < $runAmount; $i++)
		{
			$breakIt->run();
		}
		
		$end = microtime(true);
		
		printf("total time to run simple callback %d times was %f seconds\n", $runAmount, ($end - $start));
		
		assert($test1 === $runAmount);
	}

}

?>