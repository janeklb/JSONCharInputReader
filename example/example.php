<?php

namespace janeklb\json;

require_once __DIR__ . '/../vendor/autoload.php';

class JSONChunkProcessorImpl implements ChunkProcessor
{
	public $numProcessed = 0;

	public function process($jsonChunk)
	{
		echo "\n\nDecoding ";
		echo $jsonChunk;
		echo "\n";

		$obj = json_decode($jsonChunk);

		if ($obj)
		{
			$this->numProcessed++;
		}

		var_dump($obj);
		echo "\n";
	}
}


$processor = new JSONChunkProcessorImpl();
$jsonReader = new CharInputReader($processor);

// This example reads from stdin and processes characters one by one
$fd = fopen("php://stdin","r");
while ( !feof($fd) )
{
	// read chars one at a time
	$char = fread($fd, 1);

	// read this character in (edgecase: pesky zero string)
	if ($char || $char == '0')
	{
		$jsonReader->readChar($char);
	}
}

echo "\n\nThanks.. you have processed ",  $processor->numProcessed, " JSON entities\n";

fclose($fd);

