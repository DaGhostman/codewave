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
     * @example <code><!-- extension:layout ( template=head )@cache --></code>
     *          This is example of a valid call to the layout extension with array of
     *          arguments. The flag is to hint that it should use caching.
     *
     * More general syntax is:
     *  <code><!-- type:name (arguments,..,..)[@flag]</code>
     * Where <strong>type</strong> is either extensions or filter, name is
     *          the name of any registered component, arguments are passed as
     *          array (first argument) and flag is optional. It can be used to hint
     *          some functionality to the component.
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
     * Parses the template to evaluate the comment syntax.
     *
     * @param $doc string Full template contents
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
                        $output = $this->callExtension($component, $arguments, $flag);
                        $doc = str_replace($match[0], $output, $doc);
                        break;
                    case 'filter':
                        $output = $this->callFilter($component, $arguments, $flag);
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
     * Calls a registered extension and returns the result of the execution
     *
     * @param $name string
     * @param $args array Array of arguments
     * @param $flag mixed Flag to pass to the extension
     *
     * @return bool
     */
    public function callExtension($name, $args = array(), $flag = false)
    {
        if (array_key_exists($name, $this->ext)) {
            return $this->ext[$name]($args, $flag);
        }

        return false;
    }

    /**
     * Calls a filter and returns its output
     *
     * @param $name string
     * @param $args array Arguments to pass to the filter
     * @param $flag mixed Flag to pass to the filter
     *
     * @return mixed
     */
    public function callFilter($name, $args = array(), $flag = null)
    {
        return $this->filters[$name]($args, $flag);
    }
}
