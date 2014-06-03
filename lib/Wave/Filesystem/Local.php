<?php

namespace Wave\Filesystem;

class Local
{
    
    protected $workdir;
    /**
     * @var array Array of already opened files which are not closed
     */
    protected $root = array();
    
    // Uses workdir as root for operations
    public function __construct($workdir)
    {
        $this->workdir = rtrim($workdir, '/');
    }

    /**
     * Creates a file or directory in the specified path.
     * If a
     * @method create
     * @access public
     * 
     * @param string $path The file path to create, including extension if file
     * @param string $mode Octal representation of the permissions
     * @param string $type Expects 'file' or 'folder'. Defaults to 'file'
     * @return mixed Returns \DirectoryIterator
     */
    public function create($path, $mode, $type = 'file')
    {
        $object = null;
        
        if ('file' === $type) {
            touch($this->workdir . $path);
            chmod($this->workdir, $mode);
            
            $object = $this->open($path, "c");
        }
        
        if ('dir' === $type) {
            mkdir($this->workdir . $path, $mode, true);
            
            $object = $this->open($path);
        }
        
        return $object;
    }
    
    // Open file or directory
    public function open($path, $mode = 'r')
    {
        $object = null;
        
        if (is_file($this->workdir . $path)) {
            $object = new \SplFileObject($this->workdir . $path, $mode);
        }
        
        if (is_dir($this->workdir . $path)) {
            $object = new Local($this->workdir . $path);
        }
        
        return $object;
    }

    /**
     * Reads the entire file contents
     */
    public function read($path)
    {
        return file_get_contents($this->workdir . $path);
    }
    
    public function readln($path, $line = 0)
    {
        $fp = $this->open($path, 'r');
        $fp->seek($line);
        
        if ($fp->eof()) {
            return false;
        }
        
        return rtrim($fp->fgets());
    }
    
    /**
     * Writes the string to a file
     */
    public function write($path, $str, $len = null)
    {
        $fp = $this->open($path, 'a');
        if ($fp instanceof Local) {
            throw new \InvalidArgumentException(
                "Unable to delete '" . $this->workdir . $path . "'." .
                "Path is a directory",
                -1
            );
        }
        $fp->fwrite($str, (is_null($len) ? strlen($str) : $len));
        
        return $fp;
    }
    
    // Manage file/folder permissions
    public function permissions($path = '', $permissions = null)
    {
        clearstatcache();
        if ($permissions == null) {
            $fi = new \SplFileInfo($this->workdir . $path);
            return substr(sprintf('%o', $fi->getPerms()), -4);
        }
        
        chmod($this->workdir . $path, $permissions);
        
        return $this;
    }
    
    /**
     * Returns a \DirectoryIterator of the current workpath
     */
    public function getDirectoryIterator()
    {
        return new \DirectoryIterator($this->workdir);
    }
    
    public function getPath()
    {
        return $this->workdir;
    }
}
