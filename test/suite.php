<?php

require 'PHPUnit/Autoload.php';

$suite = new PHPUnit_Framework_TestSuite('JSONCharInputReader Test Suite');
$suite->addTestFile(__DIR__ . "/test.php");
PHPUnit_TextUI_TestRunner::run($suite);
