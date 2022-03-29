<?php

namespace Micx\Whois;

class WhoisFacet
{

    const DOMAINS = [
        "de" => ["free" => "/Status: free/im", "nonfree"=>"/Status: connect/im", "dnscheck"=>true],
        "at" => ["free" => "/nothing found/im", "nonfree"=>"/registrar:/im", "dnscheck"=>true],


        "com" => ["free" => "/(No match for domain|Not Found)/im", "nonfree"=>"/Domain Name:/im", "dnscheck"=>true],
        "net" => ["free" => "/(No match for domain|Not Found)/im", "nonfree"=>"/Domain Name:/im", "dnscheck"=>true],
        "org" => ["free" => "/(No match for domain|Not Found)/im", "nonfree"=>"/Domain Name:/im", "dnscheck"=>true],
        "eu" => ["free" => "/(Status: Available)/im", "nonfree"=>"/Registrar:/im", "dnscheck"=>true],
        "info" => ["free" => "/(Domain not found)/im", "nonfree"=>"/Domain Name:/im", "dnscheck"=>true],
        "biz" => ["free" => "/(No Data found)/im", "nonfree"=>"/Domain Name:/im", "dnscheck"=>true],

        "clinic" => ["free" => "/(Domain not found)/im", "nonfree"=>"/Domain Name:/im", "dnscheck"=>true],

        "nrw" => ["free" => "/(The queried object does not exist)/im", "nonfree"=>"/Registry Domain Id:/im", "dnscheck"=>true],
        "bayern" => ["free" => "/(The queried object does not exist)/im", "nonfree"=>"/Registry Domain Id:/im", "dnscheck"=>true],
        "ruhr" => ["free" => "/(The queried object does not exist)/im", "nonfree"=>"/Registry Domain Id:/im", "dnscheck"=>true],
        "saarland" => ["free" => "/(The queried object does not exist)/im", "nonfree"=>"/Registry Domain Id:/im", "dnscheck"=>true],
    ];


    public function query(string $domain) : WhoisResult
    {
        out($domain);
        $sdl = explode(".", $domain, 2)[0] ?? null;
        $tld = explode(".", $domain, 2)[1] ?? null;
        out($sdl, $tld);
        if ( ! isset (self::DOMAINS[$tld])) {
            return new WhoisResult($domain, valid: false, error: "Invalid tld: '$tld'");
        }
        $tldConfig = self::DOMAINS[$tld];

        if ( ! preg_match("/^[a-z0-9][a-z0-9\-]+[a-z0-9]+$/", $sdl)) {
            return new WhoisResult($domain, valid: false, error: "Invalid domain name");
        }

        if ($tldConfig["dnscheck"] === true) {
            if ($domain !== gethostbyname($domain))
                return new WhoisResult($domain, valid: true, isRegistered: true);
        }

        try {
            $output = phore_exec("whois ?", [$domain]);
        } catch (\Exception $e) {
            if ($e->getCode() !== 1)
                throw $e;
            $output = $e->getMessage();
        }

        $isFree = preg_match($tldConfig["free"], $output);
        $isNonFree = preg_match($tldConfig["nonfree"], $output);

        if ($isFree && ! $isNonFree) {
            return new WhoisResult($domain, valid: true, isRegistered: false);
        }
        if ($isNonFree && ! $isFree)
            return new WhoisResult($domain, valid: true, isRegistered: true);

        throw new \RuntimeException("Ambigious result: '$output'");

    }


}
