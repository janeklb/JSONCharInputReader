<?php

namespace janeklb\json;

/**
 * CharInputReader uses this interface to process chunks of JSON
 *
 * @author janeklb
 */
interface ChunkProcessor
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