janeklb\json\CharInputReader
====================

This is a stream reader used to process JSON data character-by-character and execute callbacks
after parsing complete chunks.

The data stream must be in the form of a JSON array.
ie. `[1, 2, [3, 4], {"five": "six"}, ...`


Example
=======

To get a feel for it, run the following in your terminal:
```bash
$ cat | php example/example.php
```

[Terminate with CTRL^D or CTRL^C]

Testing
=======

Install composer with dev dependencies `composer install --dev` and run
```bash
$ ./vendor/bin/phpunit test/
```

Todo
====

- fire callbacks on deeper nested values, rather than "top level" entries in the stream array

License
=======

Distributed under the [MIT Licence](LICENSE)

