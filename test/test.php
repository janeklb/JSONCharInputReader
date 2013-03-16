<?php

use janeklb\json\ChunkProcessor,
	janeklb\json\CharInputReader;

require __DIR__ . '/../vendor/autoload.php';

/**
 * A sample implementation of a JSON Chunk Processor
 */
class JSONChunkProcessorImpl implements ChunkProcessor
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

/**
 * A few very simple simpletests
 * @author janeklb
 *
 */
class JSONCarInputReaderTest extends PHPUnit_Framework_TestCase
{
	private $processor;
	private $inputReader;

	public function setUp()
	{
		$this->processor = new JSONChunkProcessorImpl();
		$this->inputReader = new CharInputReader($this->processor);

		// trigger the beginning of an array
		$this->inputReader->readChar('[');
	}

	private function sendInput($string)
	{
		$len = strlen($string);
		for ($i = 0; $i < $len; $i++)
		{
			$this->inputReader->readChar($string[$i]);
		}
	}

	private function assertObjects()
	{
		$objects = $this->processor->getObjects();

		$this->assertEquals(count($objects), func_num_args());

		foreach ($objects as $i => $object)
		{
			$this->assertEquals($object, func_get_arg($i));
		}
	}

	public function testSingleInteger()
	{
		$this->sendInput('1');
		$this->assertObjects();

		$this->sendInput(',');
		$this->assertObjects(1);
	}

	public function testArray()
	{
		$this->sendInput('[232,2412]');
		$this->assertObjects(array(232, 2412));
	}

	public function testObject()
	{
		$this->sendInput('{"x": "hello"}');

		$test = new stdClass();
		$test->x = "hello";
		$this->assertObjects($test);
	}

	public function testMixed()
	{
		$this->sendInput('2, 3, 4, [1, {"y": [1, {"b": "x"}, 3], "o" : 2}, [4, 2]], {"ob": "bo"}');

		$objA = new stdClass();
		$objA->b = "x";

		$objB = new stdClass();
		$objB->y = array(1, $objA, 3);
		$objB->o = 2;

		$objC = new stdClass();
		$objC->ob = "bo";

		$this->assertObjects(2, 3, 4, array(1, $objB, array(4, 2)), $objC);
	}

	public function testEscaped()
	{
		$this->sendInput('{"x": "x\"a"},{"a\"b":1}');

		$objA = new stdClass();
		$objA->x = 'x"a';

		$objB = new stdClass();
		$objB->{"a\"b"} = 1;

		$this->assertObjects($objA, $objB);
	}
}

