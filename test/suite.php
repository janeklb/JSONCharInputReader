<?php

$testDir = dirname(__FILE__);

require_once $testDir . '/../JSONChunkProcessor.php';
require_once $testDir . '/../JSONCharInputReader.php';

class JSONChunkProcessorImpl implements JSONChunkProcessor
{
	private $objects = array();

	public function process($jsonChunk)
	{
		$this->objects[] = json_decode($jsonChunk);
	}

	public function getObjects()
	{
		return $this->objects;
	}
}

$suite = new TestSuite('JSONCharInputReader test suite');
$suite->addFile($testDir . '/test.php');