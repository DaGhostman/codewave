<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 23/07/14
 * Time: 19:44
 */

namespace Wave\View\Parser;

/**
 * Class General
 * @package Wave\View\Parser
 */
class General
{
    protected $ext = array();
    protected $filters = array();
    protected $validators = array();

    protected $html = null;

    /**
     * Registers the components for use within templates.
     *
     * <code><!-- ext:layout ( template=head ) --></code>
     * <code><!-- filter:pattern ( regex=/<!--(.*)-->/i )</code>
     *
     * @param $type string What to register: extension, filter or validator
     * @param $name string The name of the addition
     * @param $object callable The object to register to the callback
     *
     * @return $this
     */
    public function register($type, $name, $object)
    {
        switch (strtolower($type)) {
            case 'ext':
            case 'extension':
                $this->ext[$name] = $object;
                break;
            case 'filter':
                $this->filters[$name] = $object;
                break;
        }

        return $this;
    }

    /**
     * @param $doc
     *
     * @return mixed
     */
    public function parse($doc)
    {
        $pattern = '/<!--(.*)-->/Ui';
        preg_match_all($pattern, $doc, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (1 == preg_match(
                '/(extension|filter)\:([a-z]{1,})\s*\(([^\)]*)\)(:?\@(.*))?/i',
                trim($match[1]),
                $components
            )) {

                array_push($components, null);
                list(, $type, $component, $args, $flag)=$components;


                $args = (!empty($args) ? $args : '');
                preg_match_all('/([^=]+)=([^=]+)(?:\[(.*)])?(?:,|$)/i', trim($args), $pairs, PREG_SET_ORDER);
                $arguments = array();

                foreach ($pairs as $pair) {
                    if (preg_match('/\{.*?\}/s', $pair[2]) >= 1) {
                        $arguments[$pair[1]] = json_decode($pair[2], true);
                    } else {
                        $arguments[$pair[1]] = $this->parseValue(substr($pair[2], 1, -1));
                    }

                }



                switch (strtolower($type)) {
                    case 'extension':
                        $output = $this->callExtension($component, $arguments);
                        $doc = str_replace($match[0], $output, $doc);
                        break;
                    case 'filter':
                        $output = $this->callFilter($component, $arguments);
                        $doc = str_replace($match[0], $output, $doc);
                        break;
                }
            }
        }

        return $doc;
    }

    /**
     * Parses a variable to determinate its real type
     *
     * @param $val mixed value to be parsed
     * @return mixed Variable in its real type
     */
    public function parseValue($val)
    {
        if ((substr($val, 0, 1) == '"' && substr($val, -1) == '"') ||
            (substr($val, 0, 1) == '\'' && substr($val, -1) == '\'')
        ) {
            return substr($val, 1, -1);
        } elseif (strtolower($val) == 'true') {
            return true;
        } elseif (strtolower($val) == 'false') {
            return false;
        } elseif (is_numeric($val)) {
        // Numeric value, determine if int or float and then cast
            if ((float) $val == (int) $val) {
                return (int) $val;
            } else {
                return (float) $val;
            }
        }

        return $val;
    }

    /**
     * @param       $name
     * @param array $args
     *
     * @return bool
     */
    public function callExtension($name, $args = array())
    {
        if (array_key_exists($name, $this->ext)) {
            return $this->ext[$name]($args);
        }

        return false;
    }

    /**
     * @param       $name
     * @param array $args
     *
     * @return mixed
     */
    public function callFilter($name, $args = array())
    {
        return $this->filters[$name]($args);
    }
}
