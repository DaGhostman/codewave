<?php

namespace Wave\Session\Adapter;

class BasicAdapter extends AbstractAdapter
{
    protected $path;
    protected $ttl;
    protected $session_id;
    
    public function __construct($path, $lifetime = 3600)
    {
        if (!is_dir($path)) {
            mkdir(realpath($path), 0777, true);
        }
        
        
        
        $this->path = $path;
        session_save_path($this->path);
    }
    
    public function open($session_id)
    {
        $this->session_id = $session_id;
        
        $fcontents = file_get_contents(
            $this->path . DIRECTORY_SEPARATOR . $session_id,
            'a'
        );
        
        return unserialize($fcontents);
    }
    
    public function close($data)
    {
        $fp = fopen($this->path . DIRECTORY_SEPARATOR . $this->session_id);
        flock($fp, LOCK_EX);
        $serialized = serialize($data);
        fwrite($fp, $serialized);
        flock($fp, LOCK_UN);
    }
}
