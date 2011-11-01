<?php

require_once 'JSONChunkProcessor.php';
require_once 'JSONCharInputReader.php';

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
	// read a char at a time
	$line = fread($fd, 1);

	// and process everything but newlines
	if ($line && $line != "\n")
		$jsonReader->readChar($line);
}

echo "\n\nThanks.. you have processed " . $processor->numProcessed;
echo " JSON objects\n";

fclose($fd);