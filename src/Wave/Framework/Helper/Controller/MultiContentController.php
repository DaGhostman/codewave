<?php
namespace Wave\Framework\Helper\Controller;

trait MultiContentController
{
    protected $expectedContent = [];

    public function __call($name, $args)
    {
        if (!array_key_exists('contentType', $args[1])) {
            throw new \RuntimeException(sprintf(
                'Method "%s" not found in class "%s"',
                $name,
                get_called_class()
            ));
        }


        // Enforce camelCase method names
        $type = ucfirst($args[1]['contentType']);

        if (!in_array(strtolower($type), $this->expectedContent)) {
            throw new \RuntimeException(sprintf(
                'The format "%s" is not supported',
                strtolower($type)
            ));
        }

        if (!method_exists($this, '_' . $name)) {
            throw new \RuntimeException(sprintf(
                'Method "%s" not found in class "%s"',
                $name,
                get_called_class()
            ));
        }

        // Invokes the main method
        $result = call_user_func_array([$this, '_' . $name], $args);

        // Invokes the transformation method, which should prepare the content
        if (method_exists($this, $name . $type)) { // Method name in camelCase format
            return call_user_func([$this, $name . $type], $result);
        }

        // Alternative general transformation method, lowercase type name
        if (method_exists($this, strtolower($type))) {
            return call_user_func([$this, strtolower($type)], $result);
        }

        // No handler for the transformation of the result,
        // This is a no-no.
        throw new \LogicException(sprintf(
            'Type "%s" is supported, but there is no handler defined. Handlers: "%s" or "%s"',
            $type,
            get_called_class() . '::' . $name . $type,
            get_called_class() . '::' . strtolower($type)
        ));
    }
}
