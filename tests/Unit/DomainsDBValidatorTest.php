<?php

namespace Tests\Unit;

use App\Exceptions\DomainsDBRemoteServiceException;
use App\Util\DomainsDBValidator;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class DomainsDBValidatorTest extends TestCase
{
    private Client $client;
    private MockHandler $mockHandler;
    private DomainsDBValidator $validator;
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
        $this->validator = new DomainsDBValidator($this->client);
    }
    public function test_returns_false_if_response_is_empty(): void
    {
        $this->mockDomainNotFoundResponse();

        $this->assertFalse($this->validator->validate('example.org'));
    }

    public function test_makes_http_request_to_search_domains(): void
    {
        $this->mockDomainNotFoundResponse();

        $this->validator->validate('http://example.org');

        $this->assertNotEmpty($this->requests);
        $this->assertEquals(
            'www.testing.com/v1/domains/search?domain=example.org',
            $this->getRequest()->getUri()->__toString()
        );
    }

    public function test_returns_true_if_domain_is_found(): void
    {
        $this->mockDomainListResponse();

        $this->assertTrue($this->validator->validate('http://wikipedia-russia.ru/articles'));
    }

    public function test_returns_false_if_domain_is_not_found(): void
    {
        $this->mockDomainListResponse();

        $this->assertFalse($this->validator->validate('not-wikipedia-russia.ru'));
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
        $this->mockDomainNotFoundResponse();

        $this->validator->validate($url);

        $this->assertNotEmpty($this->requests);
        $this->assertEquals(
            'www.testing.com/v1/domains/search?domain=example.org',
            $this->getRequest()->getUri()->__toString()
        );
    }

    public function test_throws_exception_on_service_error(): void
    {
        $this->mockErrorResponse(500);

        $this->expectException(DomainsDBRemoteServiceException::class);

        $this->validator->validate('test');
    }

    private function mockDomainNotFoundResponse(): void
    {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->loadMockResponseContent('domain_not_found.json')
        );
        $this->mockHandler->append($response);
    }

    private function mockDomainListResponse(): void
    {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->loadMockResponseContent('domains_search.json')
        );
        $this->mockHandler->append($response);
    }

    private function mockErrorResponse(int $statusCode = 500): void
    {
        $this->mockHandler->append(new Response($statusCode));
    }

    private function loadMockResponseContent(string $fileName): string
    {
        return file_get_contents(
            __DIR__ . '/../Resources/Responses/domainsdb/' . $fileName
        );
    }

    private function getRequest(): Request
    {
        return $this->requests[0]['request'];
    }
}
