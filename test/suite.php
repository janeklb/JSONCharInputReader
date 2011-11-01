<?php

$testDir = dirname(__FILE__);

require_once $testDir . '/../JSONChunkProcessor.php';
require_once $testDir . '/../JSONCharInputReader.php';

class JSONChunkProcessorImpl implements JSONChunkProcessor
{
	private $objects = array();

	public function process($jsonChunk)
	{
		echo "\n processing: $jsonChunk\n";
		$obj = json_decode($jsonChunk);
		var_dump($obj);
		$this->objects[] = $obj;
	}

	public function getObjects()
	{
		return $this->objects;
	}
}

$suite = new TestSuite('JSONCharInputReader test suite');
$suite->addFile($testDir . '/test.php');