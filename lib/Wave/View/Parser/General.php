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
     * <code><!-- ext layout ( template=head ) --></code>
     * <code><!-- filter pattern ( regex=/<!--(.*)-->/i --> )</code>
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
            preg_match('/(extension|filter)\:([a-z]{1,})\s*\(([^\)]*)\)/i', trim($match[1]), $components);

            list(, $type, $component, $args)=$components;
            $args = (!empty($args) ? $args : '');
            preg_match_all('/([^=]+)=([^=]+)(?:,|$)/i', trim($args), $pairs, PREG_SET_ORDER);
            $arguments = array();

            foreach ($pairs as $pair) {
                if (preg_match('/\{.*?\}/s', $pair[2]) >= 1) {
                    $arguments[$pair[1]] = json_decode($pair[2], true);
                } else {
                    $arguments[$pair[1]] = str_replace(array('\'', '"'), '', $pair[2]);
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
                default:
                    break;
            }

        }

        return $doc;
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
