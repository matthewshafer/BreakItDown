<?php
require_once('../../teency/Teency/Teency.php');

class AllTests extends TestSuite
{
	public function tests()
	{
		require_once('MainTest.php');
		$this->load('MainTest');
		
		require_once('PerformanceTest.php');
		$this->load('PerformanceTest');
	}
}

$run = new AllTests();
$run->tests();

?>