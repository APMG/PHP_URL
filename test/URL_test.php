<?php

include('../URL.php');

class URL_test extends PHPUnit_Framework_TestCase
{
    public function testAssembleAll()
    {
        $url = new URL();

        $url->host = 'example.com';
        $url->scheme = 'https';
        $url->path = '/watch';
        $url->query = array(
            'v' => '1234',
            't' => '5678',
        );
        $url->port = 8080;
        $url->username = 'testuser';
        $url->password = 'testpassword';
        $url->fragment = 'testfragment';

        $assembled_url = $url->assemble();

        $this->assertEquals('https://testuser:testpassword@example.com:8080/watch?v=1234&t=5678#testfragment', $assembled_url);

        $this->assertEquals('https://testuser:testpassword@example.com:8080/watch?v=1234&t=5678#testfragment', (string) $url);
    }

    public function testAssemblePasswordNoUser()
    {
        $url = new URL();

        $url->host = 'example.com';
        $url->path = '/watch';
        $url->password = 'testpassword';

        $assembled_url = $url->assemble();

        $this->assertEquals('http://example.com/watch', $assembled_url);
    }

    public function testAssembleMissingSlash()
    {
        $url = new URL();

        $url->host = 'example.com';
        $url->path = 'watch';

        $assembled_url = $url->assemble();

        $this->assertEquals('http://example.com/watch', $assembled_url);
    }

    public function testAssembleAddQueryString()
    {
        $url = new URL();

        $url->host = 'example.com';
        $url->path = '/watch';

        $url->addQueryString('v', 'hello');

        $assembled_url = $url->assemble();

        $this->assertEquals('http://example.com/watch?v=hello', $assembled_url);
    }

    public function testAssembleAddArrayQueryString()
    {
        $url = new URL();

        $url->host = 'example.com';
        $url->path = '/watch';

        $url->addQueryString('v', array('v1', 'v2'));

        $assembled_url = $url->assemble();

        $this->assertEquals('http://example.com/watch?v%5B0%5D=v1&v%5B1%5D=v2', $assembled_url);
    }

    public function testAssembleAddMultipleQueryString()
    {
        $url = new URL();

        $url->host = 'example.com';
        $url->path = '/watch';

        $url->addQueryString('v', 'v1');
        $url->addQueryString('v', 'v2');

        $assembled_url = $url->assemble();

        $this->assertEquals('http://example.com/watch?v%5B0%5D=v1&v%5B1%5D=v2', $assembled_url);
    }

    public function testAssembleNoSchema()
    {
        $url = new URL();

        $url->host = 'example.com';
        $url->scheme = '';
        $url->path = '/watch';

        $assembled_url = $url->assemble();

        $this->assertEquals('//example.com/watch', $assembled_url);


        $url = new URL();

        $url->host = 'example.com';
        $url->scheme = null;
        $url->path = '/watch';

        $assembled_url = $url->assemble();

        $this->assertEquals('//example.com/watch', $assembled_url);


        $url = new URL();

        $url->host = 'example.com';
        $url->scheme = false;
        $url->path = '/watch';

        $assembled_url = $url->assemble();

        $this->assertEquals('//example.com/watch', $assembled_url);
    }

    public function testAssembleMissingHost()
    {
        $url = new URL();

        $url->path = '/watch';

        try {
            $assembled_url = $url->assemble();
        } catch (Exception $e) {
            return;
        }

        $this->fail('A lack of hostname should cause the assemble method to fail.');
    }

    public function testParse()
    {
        $url = new URL();

        $url->parse('https://testuser:testpassword@example.com:8080/watch?v=1234&t=5678#testfragment');

        $this->assertEquals('example.com', $url->host);
        $this->assertEquals('https', $url->scheme);
        $this->assertEquals('/watch', $url->path);
        $this->assertEquals(array('v' => '1234','t' => '5678'), $url->query);
        $this->assertEquals(8080, $url->port);
        $this->assertEquals('testuser', $url->username);
        $this->assertEquals('testpassword', $url->password);
        $this->assertEquals('testfragment', $url->fragment);


        $url = new URL('https://testuser:testpassword@example.com:8080/watch?v=1234&t=5678#testfragment');

        $this->assertEquals('example.com', $url->host);
        $this->assertEquals('https', $url->scheme);
        $this->assertEquals('/watch', $url->path);
        $this->assertEquals(array('v' => '1234','t' => '5678'), $url->query);
        $this->assertEquals(8080, $url->port);
        $this->assertEquals('testuser', $url->username);
        $this->assertEquals('testpassword', $url->password);
        $this->assertEquals('testfragment', $url->fragment);
    }

    public function testParseQueryArray()
    {
        $url = new URL();

        $url->parse('http://example.com/watch?v=1234&t=5678&arr[]=test1&arr[]=test2');

        $this->assertEquals('example.com', $url->host);
        $this->assertEquals('http', $url->scheme);
        $this->assertEquals('/watch', $url->path);
        $this->assertEquals(array(
            'v' => '1234',
            't' => '5678',
            'arr' => array(
                'test1',
                'test2',
            ),
        ), $url->query);
    }

    public function testParseNoScheme()
    {
        $url = new URL();

        $url->parse('//example.com/watch');

        $this->assertEquals('example.com', $url->host);
        $this->assertEmpty($url->scheme);
        $this->assertEquals('/watch', $url->path);
    }

    public function testRemoveQueryString()
    {
        $url = new URL();

        $url->parse('http://example.com/watch?v1=test&v2=hello');

        $this->assertEquals(array('v1' => 'test', 'v2' => 'hello'), $url->query);

        $url->removeQueryString('v1');

        $this->assertEquals(array('v2' => 'hello'), $url->query);

        $this->assertEquals('http://example.com/watch?v2=hello', $url->assemble());
    }

    public function testComplete()
    {
        $url_string = 'https://testuser:testpassword@example.com:8080/watch?v=1234&t=5678#testfragment';
        $url = new URL($url_string);
        $this->assertEquals($url->assemble(), $url_string);
    }
}