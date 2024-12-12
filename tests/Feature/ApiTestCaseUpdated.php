<?php

namespace Tests\Feature;

use Tests\CreatesApplication;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;

abstract class ApiTestCaseUpdated extends TestCase
{
    use CreatesApplication;

    use WithFaker;

    // use RefreshDatabase;

    /**
     * If marked as true, a bearer token will be passed with Bearer in the Authorization Header
     * 
     * @var bool
     */
    protected $isAuthenticated = false;

    /**
     * Add Prefix to all routes
     * 
     * @var string
     */
    protected $apiPrefix = '/api';

    /**
     * Response Object
     * 
     * @var \Illuminate\Testing\TestResponse
     */
    protected $response;

    /**
     * Module route
     * 
     * @var string
     */
    protected $route;


    /**
     * Define the full data that should be fully valid.
     * This includes required and optional data
     * 
     * @return array
     */
    abstract protected function fullData(): array;

    /**
     * Define the record shape that will be returned
     * It must contain the entire record shape even if not present in all requests
     * 
     * @return array
     */
    abstract protected function recordShape(): array;

    /**
     * Get full data but replace the given array keys
     * 
     * @param array $newData
     * @return array
     */
    protected function fullDataReplace(array $newData): array
    {
        return $this->fullDataWith($newData);
    }

    /**
     * Get full data except the given keys
     * 
     * @param array $exceptKeys
     * @return array
     */
    protected function fullDataExcept(array $exceptKeys): array
    {
        return collect($this->fullData())->except($exceptKeys)->toArray();
    }

    /**
     * Merge the given array with the full data
     * 
     * @param array $otherData
     * @return array
     */
    protected function fullDataWith(array $otherData): array
    {
        return array_merge($this->fullData(), $otherData);
    }

    /**
     * Create success create request
     * 
     * @param   array $data
     * @param   array $responseRecordShape 
     * @return  void
     */
    protected function successCreate($mainKey = 'record.', array $data, array $responseRecordShape)
    {
        $response = $this->post($this->route, $data);
        // dd(json_decode($response->decodeResponseJson()->json));
        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($responseRecordShape, $mainKey) {
            $data = Arr::dot($responseRecordShape);

            foreach ($data as $key => $type) {
                $json->whereType('data.' . $mainKey . $key, $type);
            }

            $json->etc();
        });
    }

    /**
     * Method successUpdate
     *
     * @param int $id
     * @param array $data
     *
     * @return void
     */
    protected function successUpdate(int $id, array $data)
    {
        $response = $this->put($this->route . '/' . $id, $data);
        $response->assertStatus(200);
    }

    /**
     * Create success create request
     * 
     * @param array $data
     * @param array $errorKeys
     * @param bool $ignoreOtherKeys | if set to true, it will ignore other keys
     * @return void
     */
    protected function assertFailCreate(array $data, array $errorKeys, bool $ignoreOtherKeys = false, array $headers = [])
    {
        $response = $this->post($this->route, $data, $headers);
        $response->assertStatus(400);

        $jsonResponse = json_decode($response->decodeResponseJson()->json);

        $this->assertObjectHasAttribute('errors', $jsonResponse);

        $errors = $jsonResponse->errors;

        $this->assertIsArray($errors);

        $responseErrorKeys = [];

        foreach ($errors as $error) {
            // check for the key property to be exists
            $this->assertObjectHasAttribute('key', $error);
            $this->assertObjectHasAttribute('value', $error);
            // check if the error key is listed 
            $errorKeyMustBeNotListed = in_array($error->key, $errorKeys);

            $this->assertTrue($errorKeyMustBeNotListed, ($error->key) . ' error key asserted to be not exist, Error message for the key:' . $error->value);

            $responseErrorKeys[] = $error->key;
        }

        foreach ($errorKeys as $errorKey) {
            $this->assertTrue(in_array($errorKey, $responseErrorKeys), ($errorKey) . ' error key asserted to be exist');
        }
    }

    /**
     * Create success Delete request
     * 
     * @param   int $id
     * @return  void
     */
    protected function successDelete(int $id)
    {
        $response = $this->delete($this->route . '/' . $id);

        $response->assertStatus(200);
    }

    /**
     * Check success not found record
     * 
     * @param   int $id
     * @return  void
     */
    protected function successNotFoundRecord(int $id)
    {
        $response = $this->get($this->route . '/' . $id);

        $response->assertStatus(404);
    }

    /**
     * Mark the request as authorized request
     * 
     * @param bool $isAuthenticated 
     * @return $this
     */
    public function isAuthorized(bool $isAuthenticated = true): self
    {
        $this->isAuthenticated = $isAuthenticated;

        return $this;
    }

    /**
     * Handle Authorization Header
     * 
     * @param array $headers
     * @return void
     */
    protected function handleAuthorizationHeader(array &$headers)
    {
        if (!empty($headers['Authorization'])) return;

        $headers['Authorization'] = $this->isAuthenticated ? 'Bearer ' . env('BEARER_TOKEN') : 'key ' . env('API_KEY');
    }

    /**
     * Visit the given URI with a GET request, expecting a JSON response.
     *
     * @param  string  $uri
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function get($uri, array $headers = [])
    {
        $this->handleAuthorizationHeader($headers);

        return parent::getJson($uri, $headers);
    }

    /**
     * Visit the given URI with a POST request, expecting a JSON response.
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function post($uri, array $data = [], array $headers = [])
    {
        $this->handleAuthorizationHeader($headers);

        return parent::postJson($uri, $data, $headers);
    }

    /**
     * Visit the given URI with a PUT request, expecting a JSON response.
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function put($uri, array $data = [], array $headers = [])
    {
        $this->handleAuthorizationHeader($headers);

        return parent::putJson($uri, $data, $headers);
    }

    /**
     * Visit the given URI with a PATCH request, expecting a JSON response.
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function patch($uri, array $data = [], array $headers = [])
    {
        $this->handleAuthorizationHeader($headers);

        return parent::patchJson($uri, $data, $headers);
    }

    /**
     * Visit the given URI with a DELETE request, expecting a JSON response.
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function delete($uri, array $data = [], array $headers = [])
    {
        $this->handleAuthorizationHeader($headers);

        return parent::deleteJson($uri, $data, $headers);
    }

    /**
     * Visit the given URI with an OPTIONS request, expecting a JSON response.
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function options($uri, array $data = [], array $headers = [])
    {
        $this->handleAuthorizationHeader($headers);

        return parent::optionsJson($uri, $data, $headers);
    }

    /**
     * Call the given URI and return the Response.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $parameters
     * @param  array  $cookies
     * @param  array  $files
     * @param  array  $server
     * @param  string|null  $content
     * @return \Illuminate\Testing\TestResponse
     */
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $uri = $this->prepareUri($uri);

        return parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }

    /**
     * Prepare the given uri
     * 
     * @param  string $uri
     * @return string
     */
    protected function prepareUri(string $uri): string
    {
        $uri = $this->apiPrefix . '/' . ltrim($uri, '/');

        if (Str::contains($uri, '?')) {
            $uri .= '&';
        } else {
            $uri .= '?';
        }

        $uri .= $this->isAuthenticated ? 'Token=' . env('BEARER_TOKEN') : 'Key=' . env('API_KEY');

        return $uri;
    }

    /**
     * Assert success json response
     * 
     * @param  array|null  $structure
     * @param  array|null  $responseData
     * @return $this
     */
    public function assertSuccess(array $structure, $responseData = null)
    {
        $this->response->assertStatus(200);

        $this->response->assertJsonStructure($structure, $responseData);

        return $this;
    }

    /**
     * Generate data for the given keys and return corresponding data
     * 
     * @param array $filling
     * @return array
     */
    protected function fill(array $filling)
    {
        $data = [];

        foreach ($filling as $key => $value) {
            if (!is_numeric($key)) {
                $key = $value;
                $data[$key] = $value;
                continue;
            }

            if (Str::contains('password', $key)) {
                $length = null;
                if (Str::contains($key, ':')) {
                    [$key, $length] = explode(':', $key);
                }

                $data[$key] = $this->faker->password($length);
            } else {
                $data[$key] = $this->faker->$value;
            }
        }

        return $data;
    }

    /**
     * Create success create request
     * 
     * @param array $data
     * @param array $errorKeys
     * @param bool $ignoreOtherKeys | if set to true, it will ignore other keys
     * @return void
     */
    protected function assertInvalidData(array $data, array $errorKeys, bool $ignoreOtherKeys = false)
    {
        $response = $this->post($this->route, $data);
        $response->assertStatus(422);

        $jsonResponse = json_decode($response->decodeResponseJson()->json);

        $this->assertObjectHasAttribute('errors', $jsonResponse);

        $errors = $jsonResponse->errors;

        $this->assertIsArray($errors);

        $responseErrorKeys = [];

        foreach ($errors as $error) {
            // check for the key property to be exists
            $this->assertObjectHasAttribute('key', $error);
            $this->assertObjectHasAttribute('value', $error);
            // check if the error key is listed 
            $errorKeyMustBeNotListed = in_array($error->key, $errorKeys);

            $this->assertTrue($errorKeyMustBeNotListed, ($error->key) . ' error key asserted to be not exist, Error message for the key:' . $error->value);

            $responseErrorKeys[] = $error->key;
        }

        foreach ($errorKeys as $errorKey) {
            $this->assertTrue(in_array($errorKey, $responseErrorKeys), ($errorKey) . ' error key asserted to be exist');
        }
    }

    /**
     * Check success found record
     *
     * @param   int $id
     * @return  void
     */
    protected function successFoundRecord(string $uri = '', $mainKey = 'record.', array $headers = [], array $responseRecordShape = [])
    {
        if (empty($uri)) {
            $uri = $this->route;
        }
        // dd($uri);
        $response = $this->get($uri, $headers);
        // dd($response->decodeResponseJson()->json);
        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($responseRecordShape, $mainKey) {
            $data = Arr::dot($responseRecordShape);

            foreach ($data as $key => $type) {
                $json->whereType('data.' . $mainKey . $key, $type);
            }

            $json->etc();
        });
    }
}
