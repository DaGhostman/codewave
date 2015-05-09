<?php
namespace Wave\Framework\Interfaces\Http;

/**
 * A basic interface representing HTTP Request.
 * This interface could successfully represent HTTP server
 * request as well as requests which are to be sent by a client.
 * Note that in the former case, the request object should be
 * treated as immutable since the HTTP server request is by its
 * nature immutable (The client can't change anything which has
 * already been sent, without it making a new request).
 * In most cases good practice would be to ignore any changes made to the object,
 * once the application starts serving the request (Actual application logic runs)
 *
 * Interface RequestInterface
 *
 * @package Wave\Framework\Interfaces\Http
 * @license MIT
 * @author Dimitar Dimitrov <daghostman.dd@gmail.com>
 */
interface RequestInterface
{
    /**
     * The constructor of the request object should be responsible
     * for defining all mandatory fields of the request object.
     * The $uri should be either string or object instance implementing
     * the UrlInterface. This is so to ensure compatibility of all internal
     * components which rely on that object, i.e \Wave\Framework\Http\Server
     *
     * @param string $method A valid HTTP method verb
     * @param UrlInterface  $uri Object or string
     * @param array  $headers Array of headers to set up on construction
     * @param string $body The stream from which to get the HTTP body, defaults to 'php://input'
     */
    public function __construct($method, UrlInterface $uri, array $headers = [], $body = 'php://input');

    /**
     * Add a header to the current object.
     * This method should have dual behaviour, based on the $append
     * variable. If set to true the method should create/append the new
     * header value or create/overwrite the header's value.
     *
     * In case of representing server requests, this method should return a
     * new instance of the object with the new value added.
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
     * @see RequestInterface::addHeader
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
     * @param $header string name of the headers to retrieve
     *
     * @return mixed
     */
    public function getHeader($header);

    /**
     * Return all headers which are defined in the current request object
     *
     * @return mixed
     */
    public function getHeaders();

    /**
     * Return the HTTP request method verb with which the request was performed
     *
     * @return string
     */
    public function getMethod();

    /**
     * Return the current URL as object.
     *
     * @see UrlInterface
     *
     * @return UrlInterface
     */
    public function getUrl();

    /**
     * Return the raw request body of the request.
     * Handle with care as request body could be memory consuming
     * in case of file uploads for example.
     *
     * @return string|null
     */
    public function getBody();


    /**
     * Check if a header is defined
     *
     * @param $header string
     *
     * @return bool
     */
    public function hasHeader($header);
}
