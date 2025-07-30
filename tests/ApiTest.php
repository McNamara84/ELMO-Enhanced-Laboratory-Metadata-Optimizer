<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

/**
 * Test class for the API endpoints
 */
class ApiTest extends DatabaseTestCase
{
    /**
     * @var Client HTTP client instance
     */
    private $client;

    /**
     * @var string Base URI for API requests
     */
    private $baseUri;

    /**
     * @var string Project directory name
     */
    private $projectPath;

    private const API_KEY = '1234-1234-1234-1234';

    /**
     * {@inheritdoc}
     *
     * Initializes the database (via DatabaseTestCase) and the HTTP client
     * for testing the API endpoints.
     *
     * @return void
     */
    protected function setUp(): void
    {
        // Database setup from DatabaseTestCase first
        parent::setUp();

        $this->baseUri = rtrim((string) getenv('API_BASE_URL'), '/');

        $this->projectPath = '';

        $this->client = new Client([
            'base_uri' => $this->baseUri ?: '',
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'X-API-KEY' => self::API_KEY,
            ],
        ]);
    }


    /**
     * Constructs the full API URL for a given endpoint
     *
     * @param string $endpoint The API endpoint path
     * @return string The complete API URL
     */
    private function getApiUrl($endpoint): string
    {
        if (getenv('API_BASE_URL')) {
            return '/api/v2/' . ltrim($endpoint, '/');
        }
        $path = trim($this->projectPath . '/api/v2/' . ltrim($endpoint, '/'), '/');
        return "/{$path}";
    }

    /**
     * Tests the health check endpoint
     *
     * @return void
     * @throws Exception
     */
    public function testHealthCheckShouldReturnAliveMessage(): void
    {
        $endpointUrl = $this->getApiUrl('general/alive');
        echo "\nTesting endpoint: " . $this->baseUri . $endpointUrl;

        try {
            $response = $this->client->get($endpointUrl);

            echo "\nResponse Status: " . $response->getStatusCode();
            echo "\nResponse Body: " . $response->getBody();

            $this->assertEquals(
                200,
                $response->getStatusCode(),
                'Expected status code 200. Response: ' . $response->getBody()
            );

            $data = json_decode($response->getBody(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->fail('Failed to parse JSON response: ' . json_last_error_msg());
            }

            $this->assertArrayHasKey('message', $data, 'Response body should contain a "message" key.');
            $this->assertEquals("I'm still alive...", $data['message'], 'Expected message does not match.');
        } catch (Exception $e) {
            echo "\nException: " . get_class($e);
            echo "\nMessage: " . $e->getMessage();
            if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
                $response = $e->getResponse();
                echo "\nResponse Status: " . $response->getStatusCode();
                echo "\nResponse Body: " . $response->getBody();
            }
            throw $e;
        }
    }

    /**
     * Tests the endpoint for retrieving all licenses
     *
     * @return void
     * @throws Exception
     */
    public function testGetAllLicensesShouldReturnLicenseList(): void
    {
        $endpointUrl = $this->getApiUrl('vocabs/licenses/all');
        echo "\nTesting endpoint: " . $this->baseUri . $endpointUrl;

        try {
            echo "\nSending GET request to: " . $endpointUrl;

            $response = $this->client->get($endpointUrl);

            echo "\nResponse Status: " . $response->getStatusCode();
            echo "\nResponse Headers: " . json_encode($response->getHeaders());
            echo "\nResponse Body: " . $response->getBody();

            $this->assertEquals(
                200,
                $response->getStatusCode(),
                'Expected status code 200. Full response: ' . $response->getBody() .
                "\nEndpoint: " . $endpointUrl
            );

            $data = json_decode($response->getBody(), true);
            $this->assertIsArray($data, 'Response should be an array');
            $this->assertNotEmpty($data, 'Response should not be empty');

            $firstLicense = $data[0];
            $this->assertArrayHasKey('rightsIdentifier', $firstLicense);
            $this->assertArrayHasKey('text', $firstLicense);
        } catch (Exception $e) {
            echo "\nException occurred while testing " . $endpointUrl;
            throw $e;
        }
    }

    /**
     * Tests the endpoint for retrieving software licenses
     *
     * @return void
     * @throws Exception
     */
    public function testGetSoftwareLicensesShouldReturnSoftwareLicenseList(): void
    {
        $endpointUrl = $this->getApiUrl('vocabs/licenses/software');
        echo "\nTesting endpoint: " . $this->baseUri . $endpointUrl;

        try {
            $response = $this->client->get($endpointUrl);

            echo "\nResponse Status: " . $response->getStatusCode();
            echo "\nResponse Body: " . $response->getBody();

            $this->assertEquals(
                200,
                $response->getStatusCode(),
                'Expected status code 200. Response: ' . $response->getBody()
            );

            $data = json_decode($response->getBody(), true);
            $this->assertIsArray($data, 'Response should be an array');
            $this->assertNotEmpty($data, 'Response should not be empty');

            foreach ($data as $license) {
                $this->assertArrayHasKey('forSoftware', $license);
                $this->assertEquals(
                    '1',
                    $license['forSoftware'],
                    'All returned licenses should have forSoftware=1'
                );
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Tests the MSL vocabulary update endpoint error handling
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateMslVocabShouldHandleErrors(): void
    {
        $endpointUrl = $this->getApiUrl('update/vocabs/msl');
        echo "\nTesting endpoint: " . $this->baseUri . $endpointUrl;

        try {
            $response = $this->client->get($endpointUrl);
            echo "\nResponse Status: " . $response->getStatusCode();
            echo "\nResponse Body: " . $response->getBody();

            $data = json_decode($response->getBody(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->fail('Failed to parse JSON response: ' . json_last_error_msg());
            }

            if ($response->getStatusCode() === 200) {
                $this->assertArrayHasKey('message', $data, 'Response should contain a message');
                $this->assertArrayHasKey('version', $data, 'Response should contain a version');
                $this->assertArrayHasKey('timestamp', $data, 'Response should contain a timestamp');

                $this->assertStringContainsString(
                    'Successfully updated MSL vocabularies',
                    $data['message'],
                    'Message should indicate successful update'
                );

                $this->assertMatchesRegularExpression(
                    '/^\d+\.\d+$/',
                    $data['version'],
                    'Version should be in format X.Y'
                );

                $this->assertMatchesRegularExpression(
                    '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
                    $data['timestamp'],
                    'Timestamp should be in format YYYY-MM-DD HH:mm:ss'
                );

            } else if ($response->getStatusCode() === 500) {
                $this->assertArrayHasKey('error', $data, 'Error response should contain an error message');
                $this->assertNotEmpty($data['error'], 'Error message should not be empty');

                $expectedErrors = [
                    "No vocabulary version found",
                    "Failed to download vocabulary data"
                ];
                $this->assertTrue(
                    in_array($data['error'], $expectedErrors),
                    'Error message should be one of the expected errors'
                );
            } else {
                $this->fail('Unexpected response status code: ' . $response->getStatusCode());
            }

        } catch (Exception $e) {
            echo "\nException: " . get_class($e);
            echo "\nMessage: " . $e->getMessage();
            if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
                $response = $e->getResponse();
                echo "\nResponse Status: " . $response->getStatusCode();
                echo "\nResponse Body: " . $response->getBody();
            }
            throw $e;
        }
    }

    /**
     * Tests the CGI keywords update endpoint.
     *
     * This test accepts either a successful update (status code 200)
     * or an error response (status code 500) depending on the
     * availability of the external vocabulary source.
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateCgiKeywordsEndpoint(): void
    {
        $endpointUrl = $this->getApiUrl('update/vocabs/cgi');
        echo "\nTesting endpoint: " . $this->baseUri . $endpointUrl;

        try {
            $response = $this->client->get($endpointUrl);
            echo "\nResponse Status: " . $response->getStatusCode();
            echo "\nResponse Body: " . $response->getBody();

            $data = json_decode($response->getBody(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->fail('Failed to parse JSON response: ' . json_last_error_msg());
            }

            if ($response->getStatusCode() === 200) {
                $this->assertArrayHasKey('message', $data, 'Response should contain a message');
                $this->assertArrayHasKey('timestamp', $data, 'Response should contain a timestamp');

                $this->assertStringContainsString(
                    'CGI keywords successfully updated',
                    $data['message'],
                    'Message should indicate successful update'
                );

                $this->assertMatchesRegularExpression(
                    '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/',
                    $data['timestamp'],
                    'Timestamp should be ISO 8601 format'
                );
            } elseif ($response->getStatusCode() === 500) {
                $this->assertArrayHasKey('error', $data, 'Error response should contain an error message');
                $this->assertNotEmpty($data['error'], 'Error message should not be empty');
            } else {
                $this->fail('Unexpected response status code: ' . $response->getStatusCode());
            }

        } catch (Exception $e) {
            echo "\nException: " . get_class($e);
            echo "\nMessage: " . $e->getMessage();
            if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
                $response = $e->getResponse();
                echo "\nResponse Status: " . $response->getStatusCode();
                echo "\nResponse Body: " . $response->getBody();
            }
            throw $e;
        }
    }
}