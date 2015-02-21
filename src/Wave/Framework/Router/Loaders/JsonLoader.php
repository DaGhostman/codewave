<?php
namespace Wave\Framework\Router\Loaders;

class JsonLoader
{
    use LoaderTrait;

    protected $raw = null;

    public function __construct($raw)
    {
        if (file_exists($raw)) {
            $fpointer = fopen($raw, 'rb');
            $raw = '';
            while (! feof($fpointer)) {
                $raw .= fread($fpointer, 1024);
            }
        }
        
        if (($this->raw = json_decode($raw)) == false) {
            throw new \InvalidArgumentException("he argument is not a JSON string, nor a file.");
        }
    }
}
