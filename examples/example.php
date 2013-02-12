<?php

namespace tdt\json;
require("../vendor/autoload.php");


class JSONChunkProcessorImpl implements JSONChunkProcessor
{
	public $numProcessed = 0;
	
	public function process($jsonChunk)
	{
		echo "\n\nDecoding ";
		echo $jsonChunk;
		echo "\n";
		
		$obj = json_decode($jsonChunk);
		
		if ($obj)
			$this->numProcessed++;
		
		var_dump($obj);
		echo "\n";
	}
}


$processor = new JSONChunkProcessorImpl();
$jsonReader = new JSONCharInputReader($processor);

// This example reads from stdin and processes characters one by one
$fd = fopen("php://stdin","r");
while ( !feof($fd) )
{
	// read chars one at a time
	$char = fread($fd, 1);

	// and process everything but newlines
	if ($char && $char != "\n")
		$jsonReader->readChar($char);
}

echo "\n\nThanks.. you have processed ",  $processor->numProcessed, " JSON objects\n";

fclose($fd);
