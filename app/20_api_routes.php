<?php
namespace App;



use Brace\Core\AppLoader;
use Brace\Core\BraceApp;
use Laminas\Diactoros\ServerRequest;
use Micx\Whois\Config\Config;
use Micx\Whois\WhoisFacet;

use Psr\Http\Message\ServerRequestInterface;

AppLoader::extend(function (BraceApp $app) {


    $app->router->on("GET@/v1/whois/whois.js", function (BraceApp $app, string $subscriptionId, Config $config, ServerRequestInterface $request) {
        $data = file_get_contents(__DIR__ . "/../src/whois.js");

        $error = "";
        $origin = $request->getHeader("referer")[0] ?? null;
        if ($origin !== null && ! in_array(substr($origin, 0, -1), $config->allow_origins)) {
            $origin = substr($origin, 0, -1);
            $error = "Invalid origin: '$origin' - not allowed for subscription_id '$subscriptionId'";
        }

        $data = str_replace(
            ["%%ENDPOINT_URL%%", "%%ERROR%%", "%%TLDS%%"],
            [
                "//" . $app->request->getUri()->getHost() . "/v1/whois/query?subscription_id=$subscriptionId",
                $error,
                json_encode(array_keys(WhoisFacet::DOMAINS))
            ],
            $data
        );

        return $app->responseFactory->createResponseWithBody($data, 200, ["Content-Type" => "application/javascript"]);
    });

    $app->router->on("GET@/v1/whois/query", function(ServerRequest $request, Config $config) {
        $facet = new WhoisFacet();
        usleep(mt_rand(100000, 200000));
        return $facet->query($request->getQueryParams()["q"] ?? throw new \InvalidArgumentException("Parameter 'q' missing."));
    });



});
