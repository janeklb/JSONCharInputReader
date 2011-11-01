<?php

/**
 * JSONCharInputReader uses this interface to process chunks of JSON 
 * 
 * @author janeklb
 *
 */
interface JSONChunkProcessor
{
	/**
	 * Subclasses can use this function to process complete "chunks" of data
	 * from a JSON data stream
	 * 
	 * @param string $jsonChunk a chunk of JSON data
	 * @return void
	 */
	public function process($jsonChunk);
}