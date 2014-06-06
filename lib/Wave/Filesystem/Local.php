<?php
namespace Wave\Filesystem;

/**
 *
 * @author phpAcorn <phpacorn@gmail.com>
 * @copyright phpAcorn 2014
 * @link http://phpacorn.com/
 * @package Wave
 * @subpackage Filesystem
 * @version 1.0
 * @name Local
 * @uses \SplFileObject
 * @uses \DirectoryIterator
 */
class Local
{

    protected $workdir;

    /**
     * Sets the current workdir.
     * Setter injection
     *
     * @param string $workdir
     *            The work directory
     */
    public function __construct($workdir)
    {
        $this->workdir = rtrim(realpath($workdir), '/');
    }

    /**
     * Creates a file or directory in the specified path.
     *
     * By default creates a file unles $type is set.
     *
     * @access public
     *        
     * @param string $path
     *            The file path to create, including extension if file
     * @param string $mode
     *            Octal representation of the permissions
     * @param string $type
     *            Expects 'file' or 'folder'. Defaults to 'file'
     * @return mixed False or object
     * @see \Wave\Filesystem\Local::open()
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

    /**
     * Open a file or folder relative to the workdir
     * by default opens the files in read mode (the 'r' mode)
     *
     * @access public
     *        
     * @param string $path
     *            The relative path of the file/folder to open.
     * @param string $mode
     *            Used when opening files. (Optional)
     * @return mixed \SplFileObject $path is a file or
     *         new instance of \Wave\Filesystem\Local with the
     *         current path set as working directory.
     * @uses \SplFileObject
     */
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
     * Reads the entire file and returns its contents
     *
     * @access public
     *        
     * @param string $path
     *            Relative path of the file to read
     * @return mixed File contents or false on failure
     */
    public function read($path)
    {
        if (is_file($this->getPath() . $path)) {
            return file_get_contents($this->workdir . $path);
        }
        
        return false;
    }

    /**
     * Reads a line of the file $path identified by $line
     *
     * @access public
     *        
     * @param string $path
     *            The file to read from
     * @param int $line
     *            The line to read
     * @return mixed string or false when end of line is reached
     */
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
     * Writes string to file
     *
     * @access public
     *        
     * @param string $path
     *            Path of the file
     * @param string $str
     *            Content to write
     * @param int $len
     *            length to write (Optional)
     *            
     * @throws \InvalidArgumentException if $path is directory
     * @return \SplFileObject
     * @uses \SplFileObject
     */
    public function write($path, $str, $len = null)
    {
        $fp = $this->open($path, 'a');
        if ($fp instanceof Local) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Unable to delete '%s'. Path is a directory",
                    $this->workdir . $path
                ),
                -1
            );
        }
        $fp->fwrite($str, (is_null($len) ? strlen($str) : $len));
        
        return $fp;
    }

    /**
     * Gets or sets current target's permissions
     *
     * @access public
     *        
     * @param string $path
     *            Path to manage permissions
     * @param mixed $permissions
     *            Value valid for chmod
     * @return mixed The current file permissions as octals, ie
     *         '0664' or current instance
     */
    public function permissions($path = '', $permissions = null)
    {
        clearstatcache();
        if ($permissions == null) {
            $fi = new \SplFileInfo($this->workdir . $path);
            return substr(sprintf('%o', $fi->getPerms()), - 4);
        }
        
        chmod($this->workdir . $path, $permissions);
        
        return $this;
    }

    /**
     * Gets a directory iterator for the current workdir
     *
     * @access public
     *        
     * @return \DirecotryIterator
     * @uses \DirectoryIterator
     */
    public function getDirectoryIterator()
    {
        return new \DirectoryIterator($this->workdir);
    }

    /**
     * Get the current workpath
     *
     * @return string
     */
    public function getPath()
    {
        return $this->workdir;
    }
}
