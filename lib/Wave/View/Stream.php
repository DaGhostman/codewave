<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 06/07/14
 * Time: 02:32
 */

namespace Wave\View;

/**
 * Class Stream
 * @package Wave\View
 *
 * @codeCoverageIgnore
 */
class Stream
{
    protected $pos = 0;
    protected $_data = 0;
    protected $_stat = null;

    public function stream_open($path, $mode = "r", $options, &$opened_path)
    {
        $path = str_replace('view://', '', $path);
        if (!is_readable($path)) {
            throw new \RuntimeException(sprintf("Unable to read %s", $path));
        }

        $this->_data = file_get_contents($path);

        if ($this->_data === false) {
            $this->_stat = stat($path);
            return false;
        }

        $this->_stat = stat($path);

        $this->_data = preg_replace('/\<\?\=/', "<?php echo ", $this->_data);
        $this->_data = preg_replace('/<\?(?!xml|php)/s', '<?php ', $this->_data);

        return true;
    }

    public function stream_stat()
    {
        return $this->_stat;
    }

    public function url_stat()
    {
        return $this->_stat;
    }

    public function stream_read($count)
    {
        $result = substr($this->_data, $this->pos, $count);
        $this->pos += $count;

        return $result;
    }

    public function stream_write()
    {
        return 0;
    }

    public function stream_tell()
    {
        return $this->pos;
    }

    public function stream_eof()
    {
        return $this->pos >= strlen($this->_data);
    }

    public function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen($this->_data) && $offset >= 0) {
                    $this->pos = $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_CUR:
                if ($offset >= 0) {
                    $this->pos += $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_END:
                if (strlen($this->_data) + $offset >= 0) {
                    $this->pos = strlen($this->_data) + $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            default:
                return false;
        }
    }
}
