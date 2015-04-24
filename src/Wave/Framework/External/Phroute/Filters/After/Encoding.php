<?php
namespace Wave\Framework\External\Phroute\Filters\After;

use Wave\Framework\Application\Wave;

class Encoding
{
    const ENC_JSON = 'application/json';
    const ENC_XML = 'text/xml';
    const ENC_HTML = 'text/html';
    const ENC_PLAIN = 'text/plain';

    private function verifyAcceptance($type)
    {
        $header = '*/*';
        if (Wave::getRequest()->hasHeader('Accept')) {
            $header = Wave::getRequest()->getHeader('Accept');
        }

        /**
         * if wildcard is not provided (* / *) and the content type is not found
         * in the Accept header, set the response to 406 (Not Acceptable).
         *
         * Assumes that if no content type is provided, the client accepts anything (* / *)
         */
        if (strpos($header, $type) === false && strpos($header, '*/*') === false) {
            Wave::getResponse()->setStatus(406);
        }
    }


    /**
     * Sets the content type to `application/json`
     */
    public function useJSON()
    {
        $this->verifyAcceptance(self::ENC_JSON);
        Wave::getResponse()->setHeader('content-type', 'application/json');
    }

    /**
     * Sets the content type to `text/xml`
     */
    public function useXML()
    {
        $this->verifyAcceptance(self::ENC_XML);
        Wave::getResponse()->setHeader('content-type', 'text/xml');
    }

    /**
     * Sets the content type to `text/html`
     */
    public function useHTML()
    {
        $this->verifyAcceptance(self::ENC_HTML);
        Wave::getResponse()->setHeader('content-type', 'text/html');
    }

    public function usePlain()
    {
        $this->verifyAcceptance(self::ENC_PLAIN);
        Wave::getResponse()->setHeader('content-type', 'text/plain');
    }
}
