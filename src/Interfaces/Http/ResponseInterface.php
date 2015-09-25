<?php
namespace Wave\Framework\Interfaces\Http;

interface ResponseInterface
{
    /**
     * Constructs an object and populates it with the headers
     * provided in $headers
     *
     * @param array $headers list of headers
     */
    public function __construct(array $headers = []);

    /**
     * Add a header to the current object.
     * This method should have dual behaviour, based on the $append
     * variable. If set to true the method should create/append the new
     * header value or create/overwrite the header's value.
     *
     * @param string $header
     * @param string $value
     * @param bool $append
     *
     * @return mixed
     */
    public function addHeader($header, $value, $append = true);

    /**
     * Same behaviour as addHeader method, but instead of adding single
     * header, it adds multiple headers. Respectively from the key => value
     * pairs in the $headers argument.
     *
     * @see ResponseInterface::addHeader
     * @param array $headers
     * @param bool  $append
     *
     * @return mixed
     */
    public function addHeaders(array $headers, $append = true);

    /**
     * Return the value of a header identified by $header.
     * If the header does not exist it is recommended to be handled
     * gracefully, i.e no need to throw an exception as it is acceptable
     * for a client to not send a given header. Exception throwing should
     * come from the application if the headers if required for its normal
     * operation.
     *
     * @param string $header
     *
     * @return mixed
     */
    public function getHeader($header);

    /**
     * Returns all headers defined
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Check if a header exists in the current set of headers
     *
     * @param string $header
     *
     * @return mixed
     */
    public function hasHeader($header);

    /**
     * Set the status code of the response
     *
     * @param int $code
     *
     * @return mixed
     */
    public function setStatus($code);

    /**
     * Return the status code of the request as an integer
     *
     * @return array
     */
    public function getStatus();

    /**
     * Defines the HTTP version being used for the response
     * @param $version
     *
     * @return float
     */
    public function setVersion($version);

    /**
     * Retrieve the version of HTTP being used
     *
     * @return float
     */
    public function getVersion();

    /**
     * Return the contents of the Object body
     *
     * @return mixed
     */
    public function getBody();
}
