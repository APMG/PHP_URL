A URL Parsing Library
=====================

This is a simple library for parsing, modifying, and reassembling a URL.


Examples
--------

	$url = new URL('http://example.com/hello?a=123');
	$url->addQueryString('b', '456');
	echo $url->assemble();
	# Output: http://example.com/hello?a=123&b=456

To add array query strings:

	$url = new URL('http://example.com/hello?a=123');
	$url->addQueryString('b', array('456', '789'));
	echo $url->assemble();
	# Outputs: http://example.com/hello?a=123&b[]=456&b[]=789

	$url = new URL('http://example.com/hello?a=123');
	$url->addQueryString('b', '456');
	$url->addQueryString('b', '789');
	echo $url->assemble();
	# Outputs: http://example.com/hello?a=123&b[]=456&b[]=789

See tests for more possibilities.

To test
-------

Go into `test/` and run `phpunit URL_test.php`


To generate docs
----------------

In the root dir, run: `phpdoc run -d . -t docs/ -i test/`