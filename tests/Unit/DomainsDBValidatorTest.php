<?php

namespace Tests\Unit;

use App\Util\DomainsDBValidator;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class DomainsDBValidatorTest extends TestCase
{
    private Client $client;
    private MockHandler $mockHandler;
    private array $requests = [];

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $handlerStack->push(Middleware::history($this->requests));
        $this->client = new Client([
            'handler' => $handlerStack,
            'base_uri' => 'www.testing.com/',
        ]);
    }
    public function test_returns_false_if_response_is_empty(): void
    {
        $domainNotFoundResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            file_get_contents(__DIR__ . '/../Resources/Responses/domainsdb/domain_not_found.json')
        );
        $this->mockHandler->append($domainNotFoundResponse);
        $validator = new DomainsDBValidator($this->client);

        $this->assertFalse($validator->validate('example.org'));
    }

    public function test_makes_http_request_to_search_domains(): void
    {
        $domainNotFoundResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            file_get_contents(__DIR__ . '/../Resources/Responses/domainsdb/domain_not_found.json')
        );
        $this->mockHandler->append($domainNotFoundResponse);
        $validator = new DomainsDBValidator($this->client);

        $validator->validate('http://example.org');

        $this->assertNotEmpty($this->requests);
        $this->assertEquals(
            'www.testing.com/v1/domains/search?domain=example.org',
            $this->requests[0]['request']->getUri()->__toString()
        );
    }

    public function test_returns_true_if_domain_is_found(): void
    {
        $domainsResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            file_get_contents(__DIR__ . '/../Resources/Responses/domainsdb/domains_search.json')
        );
        $this->mockHandler->append($domainsResponse);
        $validator = new DomainsDBValidator($this->client);

        $this->assertTrue($validator->validate('http://wikipedia-russia.ru/articles'));
    }

    public function test_returns_false_if_domain_is_not_found(): void
    {
        $domainsResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            file_get_contents(__DIR__ . '/../Resources/Responses/domainsdb/domains_search.json')
        );
        $this->mockHandler->append($domainsResponse);
        $validator = new DomainsDBValidator($this->client);

        $this->assertFalse($validator->validate('not-wikipedia-russia.ru'));
    }

    public function urlsToParse(): array
    {
        return [
            ['http://example.org'],
            ['https://example.org'],
            ['www.example.org'],
            ['http://www.example.org'],
            ['https://www.example.org'],
            ['example.org'],
        ];
    }

    /**
     * @dataProvider urlsToParse
     * @param string $url
     * @return void
     */
    public function test_parses_correct_domain_from_url(string $url): void
    {
        $domainNotFoundResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            file_get_contents(__DIR__ . '/../Resources/Responses/domainsdb/domain_not_found.json')
        );
        $this->mockHandler->append($domainNotFoundResponse);
        $validator = new DomainsDBValidator($this->client);

        $validator->validate($url);

        $this->assertNotEmpty($this->requests);
        $this->assertEquals(
            'www.testing.com/v1/domains/search?domain=example.org',
            $this->requests[0]['request']->getUri()->__toString()
        );
    }

    public function test_throws_exception_on_service_error(): void
    {
        $this->mockHandler->append(new Response(403));
        $validator = new DomainsDBValidator($this->client);

        $this->expectException(\Throwable::class);

        $validator->validate('test');
    }
}
