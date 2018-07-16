<?php

namespace xsist10\HaveIBeenPwned\Tests;

use Http\Client\HttpClient;
use Http\Discovery\Strategy\DiscoveryStrategy;

/**
 * Find the Mock client.
 *
 * @author Sam Rapaport <me@samrapdev.com>
 */
final class DummyClientStrategy implements DiscoveryStrategy
{
    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        return (HttpClient::class === $type)
            ? [['class' => DummyAdapter::class, 'condition' => DummyAdapter::class]]
            : [];
    }
}
