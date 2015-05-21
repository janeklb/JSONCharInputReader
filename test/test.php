<?php

use janeklb\json\JSONChunkProcessor,
	janeklb\json\JSONCharInputReader;

require __DIR__ . '/../vendor/autoload.php';

/**
 * A sample implementation of a JSON Chunk Processor
 */
class JSONChunkProcessorImpl implements JSONChunkProcessor
{
	private $processed = array();

	public function process($jsonChunk)
	{
		$this->processed[] = json_decode($jsonChunk);
	}

	public function getProcessed()
	{
		return $this->processed;
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
		$this->inputReader = new JSONCharInputReader($this->processor);

		// trigger the beginning of an array
		$this->inputReader->readChar('[');
	}

	private function sendInput($string, $addComma = true)
	{
		$string = trim($string);

		// finish with a comma to make sure all inputs are parsed
		if ($addComma && substr($string, -1) != ',')
		{
			$string .= ',';
		}

		$len = strlen($string);
		for ($i = 0; $i < $len; $i++)
		{
			$this->inputReader->readChar($string[$i]);
		}
	}

	private function assertProcessed()
	{
		$processed = $this->processor->getProcessed();

		$this->assertEquals(count($processed), func_num_args());

		foreach ($processed as $i => $object)
		{
			$this->assertEquals($object, func_get_arg($i));
		}
	}

	public function testSingleInteger()
	{
		$this->sendInput('1', false);
		$this->assertProcessed();

		$this->sendInput(',', false);
		$this->assertProcessed(1);
	}

	public function testArray()
	{
		$this->sendInput('[232,2412]');
		$this->assertProcessed(array(232, 2412));
	}

	public function testObject()
	{
		$this->sendInput('{"x": "hello"}');

		$test = new stdClass();
		$test->x = "hello";
		$this->assertProcessed($test);
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

		$this->assertProcessed(2, 3, 4, array(1, $objB, array(4, 2)), $objC);
	}

	public function testEscaped()
	{
		$this->sendInput('"abc\"def"');
		$this->assertProcessed('abc"def');
	}

	public function testEscapedInObject()
	{
		$this->sendInput('{"x": "x\"a"},{"a\"b":1}');

		$objA = new stdClass();
		$objA->x = 'x"a';

		$objB = new stdClass();
		$objB->{"a\"b"} = 1;

		$this->assertProcessed($objA, $objB);
	}

	public function testBracketsAndBracesInString()
	{
		$this->sendInput('"str}ing", "str]ing"');
		$this->assertProcessed("str}ing", "str]ing");
	}

	public function testBracketsAndBracesInArrayString()
	{
		$this->sendInput('["str}ing"], ["str]ing"], ["str]ing", "str}ing"]');
		$this->assertProcessed(array("str}ing"), array("str]ing"), array("str]ing", "str}ing"));
	}

	public function testBracketsAndBracesInObjectString()
	{
		$this->sendInput('{"bracket": "val]ue", "brace": "val}ue"}');

		$obj = new stdClass();
		$obj->bracket = "val]ue";
		$obj->brace = "val}ue";

		$this->assertProcessed($obj);
	}

	public function testNullTrueFalse()
	{
		$this->sendInput("true, false, null");
		$this->assertProcessed(true, false, null);
	}

	public function testNestedObject()
	{
		$objA = new stdClass();
		$objA->subObj = new stdClass();
		$objA->subObj->foo = "bar";
		$objA->bleep = "bloop";
		$objA->boolean = true;

		$this->sendInput(json_encode($objA));

		$this->assertProcessed($objA);
	}
}

