<?php

/**
 * Copyright 2011 Crucial Web Studio, LLC or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * https://raw.githubusercontent.com/chargely/chargify-sdk-php/master/LICENSE.md
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Crucial\Service\ChargifyV2;

use GuzzleHttp\Psr7\Response;
use Crucial\Service\ChargifyV2;
use stdClass;

abstract class AbstractEntity implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * Container for parameters to be sent in API request
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Container for errors thrown by the API
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Data container for providing ArrayAccess on this object
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Instance of ChargifyV2 sent in constructor
     *
     * @var ChargifyV2
     */
    protected $_service;

    /**
     * Simply stores service instance for use in concrete classes
     *
     * @param ChargifyV2 $service
     */
    public function __construct(ChargifyV2 $service)
    {
        $this->_service = $service;
    }

    /**
     * Get ChargifyV2 instance sent in constructor
     *
     * @return ChargifyV2
     */
    public function getService(): ChargifyV2
    {
        return $this->_service;
    }

    /**
     * Set a single parameter. Provides fluent interface.
     *
     * @param string $param
     * @param mixed  $value
     *
     * @return AbstractEntity
     */
    public function setParam(string $param, mixed $value): AbstractEntity
    {
        $this->_params[$param] = $value;

        return $this;
    }

    /**
     * Get a single parameter.
     *
     * @param string $paramName
     *
     * @return string|array
     */
    public function getParam(string $paramName): string|array
    {
        return !empty($this->_params[$paramName]) ? $this->_params[$paramName] : null;
    }

    /**
     * Get all params.
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->_params;
    }

    /**
     * Assembles an object from given array
     *
     * @param array $array
     *
     * @return \stdClass
     */
    public function arrayToObject(array $array): stdClass
    {
        $object = new \stdClass();
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                // load nested elements
                $v = $this->arrayToObject($v);
            }
            $object->{$k} = $v;
        }

        return $object;
    }

    /**
     * Assembles the raw data (json) from the given array
     *
     * @param array $array
     *
     * @return string
     */
    public function getRawData(array $array): string
    {
        return json_encode($array);
    }

    /**
     * Helper to determine if there are errors with the request
     *
     * @return bool
     */
    public function isError(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Array of errors, if any, returned from Chargify. Not necessarily HTTP errors
     * like 404 or 201 status codes.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Transfoms the response body (json) into an array we can more easily
     * work with.
     *
     * @param Response|false $response
     *
     * @return array
     */
    public function getResponseArray(Response|false $response): array
    {
        $return = [];

        if (!($response instanceof Response)) {
            $this->errors[] = [
                // this error is coming from us, not the Chargify API response
                'source'    => 'client',
                'attribute' => null,
                'kind'      => 'networking',
                'message'   => 'no response'
            ];

            return $return;
        }

        /**
         * add error for bad status codes
         */
        $code       = $response->getStatusCode();
        $errorCodes = [404, 401, 500];
        if (in_array($code, $errorCodes)) {
            $this->errors[] = [
                // this error is coming from us, not the Chargify API response
                'source'    => 'client',
                'attribute' => null,
                'kind'      => 'status_code',
                'message'   => 'Bad status code: ' . $code
            ];
        }

        $body = $response->getBody();
        $body = trim((string)$body);

        /**
         * Return early if we have an empty body, which we can't turn into an array
         * anyway. This happens in cases where the API returns a 404, and possibly
         * other status codes.
         */
        if (empty($body)) {
            return $return;
        }

        $return = json_decode($body, true);

        // set errors, if any
        if (!empty($return['result']['errors'])) {
            foreach ($return['result']['errors'] as $error) {
                $this->errors[] = $error;
            }
        }

        /**
         * NOTE: There is also, maybe, an errors array nested in $return['meta']['errors']
         *       Why is this API so inconsistent???!!!!!
         */

        return $return;
    }

    /**
     * Implementation of \ArrayAccess
     */

    /**
     * For \ArrayAccess interface
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
    }

    /**
     * For \ArrayAccess interface
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->_data[$offset]);
    }

    /**
     * For \ArrayAccess interface
     *
     * @param mixed $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->_data[$offset]);
    }

    /**
     * For \ArrayAccess interface
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetGet(mixed $offset): bool
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }

    /**
     * Implementation of \Iterator
     *
     */

    /**
     * For \Iterator interface
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->_data);
    }

    /**
     * For \Iterator interface
     *
     * @return mixed
     */
    public function current(): mixed
    {
        return current($this->_data);
    }

    /**
     * For \Iterator interface
     *
     * @return mixed
     */
    public function key(): mixed
    {
        return key($this->_data);
    }

    /**
     * For \Iterator interface
     *
     * @return void
     */
    public function next(): void
    {
        next($this->_data);
    }

    /**
     * For \Iterator interface
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->current() !== false;
    }

    /**
     * Implementation of \Countable
     */

    /**
     * For \Countable interface
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->_data);
    }
}
