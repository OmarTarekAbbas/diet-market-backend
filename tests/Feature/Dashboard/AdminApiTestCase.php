<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;

abstract class AdminApiTestCase extends ApiTestCase
{
    /**
     * If marked as true, a bearer token will be passed with Bearer in the Authorization Header
     *
     * @var bool
     */
    protected $isAuthenticated = true;

    /**
     * Add Prefix to all routes
     *
     * @var string
     */
    protected $apiPrefix = '/api/admin';

    /**
     * Module route
     *
     * @var string
     */
    protected $route;

    /**
     * Module unitTesting
     *
     * @var string
     */
    protected $unitTesting = '?unitTesting=true';

    /**
     * Response Object
     *
     * @var \Illuminate\Testing\TestResponse
     */
    protected $response;

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
    protected function successCreate(array $data, array $responseRecordShape)
    {
        $response = $this->post($this->route . $this->unitTesting, $data);
        $response->assertJson(function (AssertableJson $json) use ($responseRecordShape) {
            $data = Arr::dot($responseRecordShape);

            foreach ($data as $key => $type) {
                $json->whereType('data.record.' . $key, $type);
            }

            $json->etc();
        });
        $response->assertStatus(200);
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
        $response = $this->put($this->route . '/' . $id . $this->unitTesting, $data);
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
    protected function assertFailCreate(array $data, array $errorKeys, bool $ignoreOtherKeys = false)
    {

        $response = $this->post($this->route . $this->unitTesting, $data);
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
        $response = $this->delete($this->route . '/' . $id . $this->unitTesting);

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
        $response = $this->get($this->route . '/' . $id . $this->unitTesting);

        $response->assertStatus(404);
    }

    /**
     * Check success found record
     *
     * @param   int $id
     * @return  void
     */
    protected function successFoundRecord($id = null)
    {
        if ($id) {
            $route = $this->route . '/' . $id;
        } else {
            $route = $this->route;
        }

        $response = $this->get($route);

        $response->assertStatus(200);
    }

    /**
     * Check success found list
     *
     * @param   int $id
     * @return  void
     */
    protected function successFoundList(string $route)
    {
        $response = $this->get($route);

        $response->assertStatus(200);
    }
}
