<?php
/**
 * The primary file for the URL handling class.
 *
 * @author William Johnston <wjohnston@mpr.org>
 * @copyright 2013 American Public Media Group
 * @license http://opensource.org/licenses/MIT MIT
 */


/**
* URL handling class.
*
* Makes it easy to parse and modify a URL.
*/
class URL
{
    /**
     * The host/domain name.
     *
     * @var string
     */
    public $host;

    /**
     * The URL scheme, such as http, https, et cetera.
     *
     * @var string
     */
    public $scheme = 'http';

    /**
     * The URL path. Should begin with a slash.
     *
     * @var string
     */
    public $path = '/';

    /**
     * A keyed list of query strings.
     *
     * Can be set directly or via the addQueryString() and removeQueryString() methods. If the format array('key' => 'value'). The value can be an array.
     *
     * @var mixed[]
     */
    public $query = array();

    /**
     * The port number
     *
     * @var integer
     */
    public $port;

    /**
     * The username
     *
     * @var string
     */
    public $username;

    /**
     * The password
     *
     * If the password is set without a username, then it will not be part of the assembled url.
     *
     * @var string
     */
    public $password;

    /**
     * The fragment string, e.g. the #hash
     *
     * @var string
     */
    public $fragment;

    /**
     * Create a URL object
     *
     * @param string $url The url string to be parsed.
     * @return object
     */
    function __construct($url = '')
    {
        if (!empty($url)) {
            $this->parse($url);
        }
    }

    /**
     * Parse an existing URL
     *
     * @param string $url The url to be parsed. Cannot be blank.
     * @throws Exception if the type is not a string.
     * @throws Exception if the string is empty.
     */
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

    /**
     * Assemble the URL string.
     *
     * @throws Exception if no host is defined.
     * @return string
     */
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

    /**
     * Add a query string to the list.
     *
     * If called multiple times with the same key, the value will be turned into an array of values.
     *
     * @param string $key    The key in the query string.
     * @param mixed  $value  The value. Can be a string or an array of strings.
     */
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

    /**
     * Remove a query string from the list.
     *
     * @param string $key The key to remove.
     */
    public function removeQueryString($key)
    {
        unset($this->query[$key]);
    }

    /**
     * Magic function to allow this class to be assigned as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->assemble();
    }
}