<?php
namespace Wave\Framework\External\Phroute\Filters\After;

use Wave\Framework\Application\Wave;

class Encoding {
    /**
     * Sets the content type to `application/json`
     */
    public function useJSON()
    {
        Wave::getResponse()->withHeader('content-type', 'application/json');
    }

    /**
     * Sets the content type to `text/xml`
     */
    public function useXML()
    {
        Wave::getResponse()->withHeader('content-type', 'text/xml');
    }

    /**
     * Sets the content type to `text/html`
     */
    public function useHTML()
    {
        Wave::getResponse()->withHeader('content-type', 'text/html');
    }

    public function usePlain()
    {
        Wave::getResponse()->withHeader('content-type', 'text/plain');
    }
}