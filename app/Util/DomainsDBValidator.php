<?php

namespace App\Util;

use App\Exceptions\DomainsDBRemoteServiceException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;

class DomainsDBValidator implements DomainValidator
{
    public function __construct(private Client $client)
    {

    }

    /**
     * @throws DomainsDBRemoteServiceException
     */
    public function validate(string $url): bool
    {
        $domain = $this->parseDomain($url);

        return !empty($this->searchDomain($domain));
    }

    private function parseDomain(string $url): string
    {
        $url = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $url);
        $url = new Uri('http://' . $url);

        return $url->getHost();
    }

    /**
     * @throws DomainsDBRemoteServiceException
     */
    private function searchDomain(string $domain): array
    {
        $domains = [];
        try {
            $response = $this->client->request(
                'GET',
                'v1/domains/search',
                ['query' => ['domain' => $domain]]
            );
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            return $domains;
        } catch (GuzzleException $e) {
            throw new DomainsDBRemoteServiceException(
                'Error occurred during request to domainsdb api',
                $e->getCode(),
                $e
            );
        }

        if (!empty($data['domains'])) {
            $domains = array_map(function ($domainData) {
                return $domainData['domain'];
            }, $data['domains']);
        }

        return $domains;
    }
}
