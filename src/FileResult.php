<?php

namespace Lacuna\RestPki\Legacy;

/**
 * Class FileResult
 * @package Lacuna\RestPki\Legacy
 */
class FileResult
{
    private $client;
    private $model;

    public function __construct($client, $model)
    {
        $this->client = $client;
        $this->model = $model;
    }

    /**
     * @internal
     * @return mixed
     */
    public function _getModel()
    {
        return $this->model;
    }

    public function getContentRaw() {
        $base64Content = $this->getContentBase64();
        return base64_decode($base64Content);
    }

    public function getContentBase64()
    {
        if (isset($this->model->content)) {
            return $this->model->content;
        } else {
            return $this->client->_downloadContent($this->model->url);
        }
    }
}