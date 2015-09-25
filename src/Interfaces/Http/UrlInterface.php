<?php
namespace Wave\Framework\Interfaces\Http;

/**
 * Interface UrlInterface
 *
 * @package Wave\Framework\Interfaces\Http
 */
interface UrlInterface
{
    /**
     * Adds all mandatory fields for the URL, the only needed part is $path,
     * as it is always present, others can be safely omitted, although $path
     * can be set to default to '/' it is not recommended to make the code
     * more self-explanatory.
     *
     * @param string $path The path of the request
     * @param QueryInterface $query Object representing the current query
     * @param string $host the host name to use in the URL
     * @param int $port A valid port number from 1 - 65535
     * @param string $scheme the scheme of the request (in case of 'file://' urls,
     *                       make sure to use a standard http port [80|443])
     * @param string $fragment
     */
    public function __construct($path, QueryInterface $query, $host, $port, $scheme, $fragment);

    /**
     * @return string|null
     */
    public function getScheme();

    /**
     * @return string|null
     */
    public function getHost();

    /**
     * @return int|null
     */
    public function getPort();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return QueryInterface
     */
    public function getQuery();

    /**
     * @return string|null
     */
    public function getFragment();

    /**
     * @param string $scheme
     *
     * @return mixed
     */
    public function setScheme($scheme);

    /**
     * @param string $host
     *
     * @return mixed
     */
    public function setHost($host);

    /**
     * @param int $port
     *
     * @return mixed
     */
    public function setPort($port);

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function setPath($path);

    /**
     * @param QueryInterface $query
     *
     * @return mixed
     */
    public function setQuery(QueryInterface $query);

    /**
     * @param string $fragment
     *
     * @return mixed
     */
    public function setFragment($fragment);

    /**
     * @return string
     */
    public function __toString();
}
