<?php

namespace xsist10\HaveIBeenPwned\Adapter;

use \RuntimeException;

class FileGetContents implements Adapter
{
    public function get($url) {
        if (!ini_get('allow_url_fopen')) {
            throw new RuntimeException('allow_url_fopen disabled.');
        }

        $context = stream_context_create(array(
            'http' => array(
                'user_agent'          => 'xsist10-PHP-client'
            ),
            'ssl' => array(
                'method'              => 'GET',
                'verify_peer'         => true,
                'verify_depth'        => 5,
                'cafile'              => __DIR__ . '/../../cacert.pem',
                'CN_match'            => 'www.haveibeenpwned.com',
                'disable_compression' => true
            )
        ));
        
        $result = @file_get_contents($url, false, $context);
        if (empty($result))
        {
            $result = '[]';
        }

        return $result;
    }
}