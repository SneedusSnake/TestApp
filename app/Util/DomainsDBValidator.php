<?php

namespace App\Util;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;

class DomainsDBValidator implements DomainValidator
{
    public function __construct(private Client $client)
    {

    }

    /**
     * @throws GuzzleException
     */
    public function validate(string $url): bool
    {
        $url = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $url);
        $url = new Uri('http://' . $url);
        $domain = $url->getHost();
        $response = $this->client->request(
            'GET',
            'v1/domains/search',
            ['query' => ['domain' => $domain]]
        );

        $data = json_decode($response->getBody()->getContents(), true);

        if (!empty($data['domains'])) {
            foreach($data['domains'] as $domainData) {
                if ($domainData['domain'] === $domain) {
                    return true;
                }
            }
        }

        return false;
    }
}
