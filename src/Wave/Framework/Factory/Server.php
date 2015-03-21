<?php
/**
 * Created by PhpStorm.
 * User: dimitar
 * Date: 21/03/15
 * Time: 15:47
 */

namespace Wave\Framework\Factory;


use Wave\Framework\Http\Uri;

class Server
{
    protected $vars = [];

    protected $request = null;
    protected $response = null;

    public function __construct($serverVars = null)
    {
        if (!$serverVars) {
            $serverVars = filter_input_array(INPUT_SERVER, FILTER_FLAG_NONE);
        }

        $this->vars = $serverVars;
    }

    public function withRequest($request)
    {
        $serverVars = $this->vars;

        foreach ($serverVars as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {continue;}

            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $request = $request->withAddedHeader($header, $value);
        }

        $url = (
            (isset($serverVars['HTTPS']) && $serverVars['HTTPS'] != '') ?
                'https://' :
                'http://'
            ) . $serverVars['HTTP_HOST'] . $serverVars['REQUEST_URI'];


        $this->request = $request->withUri(new Uri($url))
            ->withMethod($serverVars['REQUEST_METHOD']);

        return $this;
    }

    public function withResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    public function build($class = '\\Wave\\Framework\\Application\\Server')
    {
        return new $class($this->request, $this->response);
    }
}