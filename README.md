tdt/json
========

JSONCharInputReader processes JSON data streams character-by-character

The data stream *must* be in the form of a JSON array (atm).
ie. [1, 2, [3, 4], {"five": "six"}, ...


examples
========

To run the examples/example.php execute the following in your terminal:
```bash
$ cd examples 
$ cat | php example.php
```

Testing //todo
=======

We have a few tests in the tests directory. You can run them as follows if you have phpunit installed:

```bash
$ phpunit tests/
```

Or you can watch our travis-ci.org page when you have pushed to this repository.
