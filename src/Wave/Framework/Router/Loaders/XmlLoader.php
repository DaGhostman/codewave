<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 02/02/15
 * Time: 23:18
 */
namespace Wave\Framework\Router\Loaders;

class XmlLoader
{
    use LoaderTrait;

    protected $raw = null;

    public function __construct($xml)
    {
        $this->raw = new \SimpleXMLElement($xml, 0, is_file($xml));
        
        if (! $this->raw) {
            throw new \InvalidArgumentException('Unable to load XML');
        }
    }
}
