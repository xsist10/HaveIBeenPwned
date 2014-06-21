<?php

namespace xsist10\HaveIBeenPwned;

class HaveIBeenPwned
{
	protected function _get($url) {
		if (!ini_get('allow_url_fopen')) {
            throw new UnavailableException('allow_url_fopen disabled.');
        }

        $context = stream_context_create([
            'http' => [
                'user_agent'          => 'xsist10-PHP-client'
            ],
            'ssl' => [
            	'method'              => 'GET',
                'verify_peer'         => true,
                'verify_depth'        => 5,
                'cafile'              => __DIR__ . '/../cacert.pem',
                'CN_match'            => 'www.haveibeenpwned.com',
                'disable_compression' => true
            ]
        ]);
        
        $result = @file_get_contents($url, false, $context);
        if (empty($result))
        {
            $result = '[]';
        }

        return $result;
	}

	public function checkAccount($account) {
		$url = "https://haveibeenpwned.com/api/v2/breachedaccount/" . urlencode($account);
		return json_decode($this->_get($url), true);
	}

	public function getBreaches() {
		$url = "https://haveibeenpwned.com/api/v2/breaches";
        return json_decode($this->_get($url), true);
	}

    public function getBreach($name) {
        $url = "https://haveibeenpwned.com/api/v2/breach/" . urlencode($name);
        return json_decode($this->_get($url), true);
    }

    public function getDataClasses()
    {
        $url = "https://haveibeenpwned.com/api/v2/dataclasses";
        return json_decode($this->_get($url), true);
    }
}