<?php
namespace Wave\Framework\External\Phroute\Filters\Before;

use Wave\Framework\Application\Wave;

class Language
{
    public function parse()
    {
        $request = Wave::getRequest();

        preg_match_all(
            '/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',
            $request->getHeader('Accept-Language'),
            $parsed
        );

        if (count($parsed[1])) {
            $rawLanguages = array_combine($parsed[1], $parsed[4]);

            foreach ($rawLanguages as $lang => $val) {
                if ($val === '') {
                    $rawLanguages[$lang] = 1;
                }
            }

            arsort($rawLanguages, SORT_NUMERIC);
        }

        $languages = [];
        foreach (array_keys($rawLanguages) as $language) {
            $languages[] = sprintf('%s=%s', $language, $rawLanguages[$language]);
        }

        $request->withHeader('X-Accept-Language', $languages);
    }
}
