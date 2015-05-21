<?php

namespace janeklb\json;

/**
 * JSONCharInputReader
 *
 * @author janeklb
 */
class JSONCharInputReader
{
	const STATE_OUTSIDE 	= -1; // Outside of a JSON stream
	const STATE_WAITING 	= 0;  // Waiting for JSON input
	const STATE_INCURLY 	= 1;  // Processing inside a curly brace
	const STATE_INSQUARE 	= 2;  // Processing inside a square bracket
	const STATE_INSTRING 	= 3;  // Processing inside a string

	private $lastEscaped;
	private $buffer;
	private $outputInterface;
	private $state;
	private $depth;
	private $quoteCount;

	/**
	 * Create a JSONCharInputReader object
	 *
	 * @param JSONChunkProcessor $outputInterface the output interface that will be used
	 *                                            for processing json 'chunks'
	 */
	public function __construct(JSONChunkProcessor $outputInterface)
	{
		$this->lastEscaped = FALSE;
		$this->buffer = '';
		$this->state = self::STATE_OUTSIDE;
		$this->outputInterface = $outputInterface;
		$this->depth = 0;
		$this->quoteCount = 0;
	}

	/**
	 * Read a character
	 *
	 * @param string $char the character to read
	 * @throws InvalidArgumentException if $char is a string of length not equal to 1
	 * @return void
	 */
	public function readChar($char)
	{
		if (!is_string($char) || strlen($char) != 1)
		{
			throw new \InvalidArgumentException(__CLASS__ . ': readChar requires a single charater as its input argument');
		}

		switch ($this->state)
		{
			// Waiting on the opening square bracket..
			case self::STATE_OUTSIDE:
				if ($char == '[')
					$this->state = self::STATE_WAITING;
				break;

			// Inside some braces/brackets
			case self::STATE_INCURLY:
			case self::STATE_INSQUARE:

				if ($char == '"' && !$this->lastCharIs('\\'))
					$this->quoteCount++;

				$this->buffer .= $char;

				// if quote count is odd we're inside a string....
				if ($this->quoteCount % 2)
					break;

				$closing = $this->state == self::STATE_INCURLY ? '}' : ']';
				$opening = $this->state == self::STATE_INCURLY ? '{' : '[';

				if ($char == $opening)
					// if this is another opening brace/bracket character, increase the depth
					$this->depth++;
				else if ($char == $closing && --$this->depth == 0)
				{
					// if this is a closing character, decrease the depth and process the buffer if
					// the bottom was reached
					$this->processBuffer();
					$this->quoteCount = 0;
				}

				break;

			// Inside a string
			case self::STATE_INSTRING:

				if ($char == '"' && !$this->lastCharIs('\\'))
					$this->state = self::STATE_WAITING;

				$this->buffer .= $char;

				break;

			// Waiting on any input within a JSON stream
			case self::STATE_WAITING:

				if ($this->lastEscaped)
				{
					// The last character was escaped -- doesn't matter what this one is
					// just add it to the buffer and continue
					$this->buffer .= $char;
					$this->lastEscaped = FALSE;
					return;
				}

				// Adjust the state based on the current character
				switch ($char)
				{
					case '[':
						$this->depth = 1;
						$this->state = self::STATE_INSQUARE;
						break;
					case '{':
						$this->depth = 1;
						$this->state = self::STATE_INCURLY;
						break;
					case '"':
						$this->state = self::STATE_INSTRING;
						break;
					case '\\':
						$this->lastEscaped = TRUE;
						break;

					// This will either mark the end of the JSON data stream itself
					case ']':
					// Or the data contained in the buffer
					case ',':
						// Either way we process the buffer
						$this->processBuffer();

						// The JSON stream was closed
						if ($char == ']')
							$this->state = self::STATE_OUTSIDE;

						// return early
						return;
				}

				// Add the current character to the buffer
				$this->buffer .= $char;
				break;
		}
	}

	private function lastCharIs($char) {
		$len = strlen($this->buffer);
		if ($len == 0)
			return false;

		$lastChar = $this->buffer[$len - 1];
		return $lastChar === $char;
	}

	/**
	 * Process the JSON data stream's buffer and reset the state
	 *
	 * @return void;
	 */
	private function processBuffer()
	{
		if ($this->buffer !== '')
		{
			$this->outputInterface->process($this->buffer);
			$this->buffer = '';
		}
		$this->state = self::STATE_WAITING;
	}
}
