<?php
namespace Stub;

class MultiContentController
{
    use \Wave\Framework\Helper\Controller\MultiContentController;

    public function __construct()
    {
        $this->expectedContent = ['json', 'xml'];
    }

    private function _index()
    {
        return 'Hello, World!';
    }

    private function _badMethod()
    {
        return 'Foo';
    }

    public function json($data)
    {
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function indexXml($data)
    {
        echo '<message>' . $data . '</message>';
    }

    private function _test()
    {
        return [
            'test' => [
                'double' => true,
                'message' => 'Hello, World!'
            ]
        ];
    }

    public function testXml($data)
    {
        return '<?xml version="1.0" encoding="utf-8"?>
            <test>
                <message double="' . $data['double'] . '">' . $data['message'] . '</message>
            </test>
        ';
    }
}