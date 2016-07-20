<?php

namespace Crucial\Service\Chargify;

class Metadata extends AbstractEntity
{

    /**
     * For subscriptions you have the ability to add / populate metadata
     * (custom fields) into pre-defined metafields.
     *
     * Pass data to custom fields using the AbstractEntity::setParam() method
     * @param int $subscriptionId
     *
     * @return Metadata
     * @see AbstractEntity::setParam()
     */
    public function create($subscriptionId)
    {
        $service       = $this->getService();

        $params = [];
        foreach ($this->getParams() as $key => $value){
            $params[] = ['name' => $key, 'value' => $value];
        }
        $rawData       = $this->getRawData(array('metadata' => $params));
        $response      = $service->request('subscriptions/' . (int)$subscriptionId . '/metadata', 'POST', $rawData);
        $responseArray = $this->getResponseArray($response);

        if (!$this->isError()) {
            $this->_data = $responseArray;
        } else {
            $this->_data = array();
        }

        return $this;
    }
}