<?php

/**
* URL handling class.
*
* Makes it easy to parse and modify a URL.
*/
class URL
{
    public $host;
    public $scheme = 'http';
    public $path = '/';
    public $query = array();
    public $port;
    public $username;
    public $password;
    public $fragment;

    function __construct($url = '')
    {
        if (!empty($url)) {
            $this->parse($url);
        }
    }

    public function parse($url)
    {
        if (!is_string($url)) {
            throw new Exception('Invalid type ' . gettype($url) . ' to parse.');
        }

        if (empty($url)) {
            throw new Exception('Empty URL.');
        }

        $parsed_url = parse_url($url);

        // host
        $this->host = (isset($parsed_url['host'])) ? $parsed_url['host'] : null;

        // scheme
        $this->scheme = (isset($parsed_url['scheme'])) ? $parsed_url['scheme'] : null;

        // path
        $this->path = (isset($parsed_url['path'])) ? $parsed_url['path'] : null;

        // query
        if (isset($parsed_url['query'])) {
            parse_str($parsed_url['query'], $this->query);
        }

        // port
        $this->port = (isset($parsed_url['port'])) ? (integer) $parsed_url['port'] : null;

        // username
        $this->username = (isset($parsed_url['user'])) ? $parsed_url['user'] : null;

        // password
        $this->password = (isset($parsed_url['pass'])) ? $parsed_url['pass'] : null;

        // fragment
        $this->fragment = (isset($parsed_url['fragment'])) ? $parsed_url['fragment'] : null;
    }

    public function assemble()
    {
        if (empty($this->host)) {
            throw new Exception('Requires a host.');
        }

        $url = '';

        if (!empty($this->scheme)) {
            $url .= $this->scheme . ':';
        }

        $url .= '//';

        if (!empty($this->username)) {
            $url .= $this->username;
        }

        if (!empty($this->username) && !empty($this->password)) {
            $url .= ':';
            $url .= $this->password;
        }

        if (!empty($this->username)) {
            $url .= '@';
        }

        $url .= $this->host;

        if (!empty($this->port)) {
            $url .= ':' . $this->port;
        }

        if (!empty($this->path)) {
            // Add beginning slash if not there.
            if (substr($this->path, 0, 1) === '/') {
                $url .= $this->path;
            } else {
                $url .= '/' . $this->path;
            }
        }

        if (!empty($this->query)) {
            $url .= '?' . http_build_query($this->query);
        }

        if (!empty($this->fragment)) {
            $url .= '#' . $this->fragment;
        }

        return $url;
    }

    public function addQueryString($key, $value)
    {
        if (isset($this->query[$key])) {
            if (!is_array($this->query[$key])) {
                $this->query[$key] = array($this->query[$key]);
            }
            $this->query[$key][] = $value;
        } else {
            $this->query[$key] = $value;
        }
    }

    public function removeQueryString($key)
    {
        unset($this->query[$key]);
    }

    public function __toString()
    {
        return $this->assemble();
    }
}