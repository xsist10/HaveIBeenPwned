<?php

namespace xsist10\HaveIBeenPwned\Adapter;

interface Adapter
{
    /**
     * Is this adapter supported in this environment?
     * 
     * @return boolean
     */
    public function isSupported();

    /**
     * Perform a GET request to the remote server
     * 
     * @param  string $url The URL being requested
     * @return string      body of the response
     */
    public function get($url);
}