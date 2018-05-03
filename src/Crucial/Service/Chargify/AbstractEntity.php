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

namespace Crucial\Service\Chargify;

use GuzzleHttp\Psr7\Response;
use Crucial\Service\Chargify;

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
    protected $_errors = array();

    /**
     * Data container for providing ArrayAccess on this object
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Instance of Chargify sent in constructor
     *
     * @var Chargify
     */
    protected $_service;

    /**
     * Simply stores service instance for use in concrete classes
     *
     * @param Chargify $service
     */
    public function __construct(Chargify $service)
    {
        $this->_service = $service;
    }

    /**
     * Get Chargify instance sent in constructor
     *
     * @return Chargify
     */
    public function getService()
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
    public function setParam($param, $value)
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
    public function getParam($paramName)
    {
        return !empty($this->_params[$paramName]) ? $this->_params[$paramName] : NULL;
    }

    /**
     * Get all params.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Assmbles an object from given array
     *
     * @param array $array
     *
     * @return \stdClass
     */
    public function arrayToObject($array)
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
    public function getRawData($array)
    {
        return json_encode($array);
    }

    /**
     * Helper to determine if there are errors with the request
     *
     * @return bool
     */
    public function isError()
    {
        return !empty($this->_errors);
    }

    /**
     * Array of errors, if any, returned from Chargify. Not necessarily HTTP errors
     * like 404 or 201 status codes.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Transfoms the response body (json) into an array we can more easily
     * work with.
     *
     * @param Response|false $response
     *
     * @return array
     * @todo $this->_errors is populated with errors from Chargify. Should this also populate a separate errors array when we get HTTP 404s or 201s?
     */
    public function getResponseArray($response)
    {
        $return = array();
        if (!($response instanceof Response)) {
            $this->_errors['Crucial_Service_Chargify']['networking error'] = 'no response';

            return $return;
        }

        $body = $response->getBody();
        $body = trim((string)$body);

        /**
         * return early on bad status codes
         */
        $code       = $response->getStatusCode();
        $errorCodes = array(404, 401, 500);
        if (in_array($code, $errorCodes)) {
            $this->_errors['Crucial_Service_Chargify']['Bad status code'] = $code;

            return $return;
        }

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
        if (!empty($return['errors'])) {
            $this->_errors = $return['errors'];
        }

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
    public function offsetSet($offset, $value)
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
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     * For \ArrayAccess interface
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
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
    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : NULL;
    }

    /**
     * Implementation of \Iterator
     */

    /**
     * For \Iterator interface
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->_data);
    }

    /**
     * For \Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->_data);
    }

    /**
     * For \Iterator interface
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->_data);
    }

    /**
     * For \Iterator interface
     *
     * @return mixed
     */
    public function next()
    {
        return next($this->_data);
    }

    /**
     * For \Iterator interface
     *
     * @return bool
     */
    public function valid()
    {
        return $this->current() !== FALSE;
    }

    /**
     * Implementation of \Countable
     */

    /**
     * For \Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->_data);
    }
    
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
}
