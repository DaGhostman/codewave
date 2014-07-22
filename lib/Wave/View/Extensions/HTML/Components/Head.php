<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 21/07/14
 * Time: 14:45
 */

namespace Wave\View\Extensions\HTML\Components;


class Head
{
    protected $meta = array();

    protected $link = array();
    protected $script = array();
    protected $custom = array();
    protected $title = "Untitled";

    protected $doc = null;

    protected $content = '';

    public function __construct(
        $title,
        $meta = array(),
        $link = array(),
        $script = array(),
        $custom = null
    ) {
        $this->title = (is_string($title) ? $title : "Unknown");
        foreach ($meta as $element) {
            array_push($this->meta, $element);
        }

        foreach ($link as $element) {
            array_push($this->link, $element);
        }

        foreach ($script as $element) {
            array_push($this->script, $element);
        }

        if (!is_null($custom)) {
            $this->custom = $custom;
        }

        $this->doc = new \DOMDocument();
    }

    public function addMeta($args)
    {
        array_push($this->meta, $args);

        return $this;
    }

    public function addCustom($tag, $args)
    {
        $this->custom[$tag] = $args;

        return $this;
    }

    public function addLink($args)
    {
        array_push($this->link, $args);

        return $this;
    }

    public function addScript($args)
    {
        array_push($this->script, $args);

        return $this;
    }


    public function generateTag($name, $args, $value = '')
    {

        $doc = $this->doc;
        $fragment = $doc->createDocumentFragment();
        $element =  $doc->createElement($name, (string) $value);

        foreach ($args as $key => $value) {
            $element->setAttribute($key, $value);
        }
        $fragment->appendChild($element);
        $doc->appendChild($fragment);
        return $element;
    }

    public function __toString()
    {
        $doc = $this->doc;
        $head = $doc->createElement('head');
        foreach ($this->meta as $meta) {
            $el = $this->generateTag('meta', $meta);
            $doc->importNode($el);
            $head->appendChild($el);
        }

        foreach ($this->link as $link) {
            $head->appendChild($this->generateTag('link', $link));
        }

        foreach ($this->script as $script) {
            $src = new Script($doc, $script);
            $head->appendChild($src->getScript());
        }

        foreach ($this->custom as $tag => $custom) {
            $content = '';
            if (isset($custom['content'])) {
                $content = $custom['content'];
                unset($custom['content']);
            }

            $head->appendChild($this->generateTag($tag, $custom, $content));
        }

        $doc->importNode($head, true);
        $doc->appendChild($head);
        $head->appendChild($doc->createElement('title', $this->title));

        return trim($doc->saveHTML());
    }
}
