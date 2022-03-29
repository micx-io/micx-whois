<?php

namespace Micx\Whois;

use newrelic\DistributedTracePayload;

class WhoisResult
{
    public function __construct(
        public $domain,
        public string $tld = "",
        public bool $valid = false,
        public ?bool $isRegistered = null,
        public $error = null
    ){}
}
