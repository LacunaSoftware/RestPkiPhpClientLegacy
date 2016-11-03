<?php

namespace Lacuna\RestPki\ClientLegacy;

abstract class SignatureFinisher
{
    /** @var RestPkiClient */
    protected $restPkiClient;
    public $token;
    public $signature;
    protected $done;
    protected $callbackArgument;
    protected $certificateInfo;

    public function __construct($restPkiClient)
    {
        $this->restPkiClient = $restPkiClient;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    public abstract function finish();

    public function getCallbackArgument()
    {
        if (!$this->done) {
            throw new \InvalidArgumentException('The method getCallbackArgument() can only be called after calling the finish() method');
        }

        return $this->callbackArgument;
    }

    public function getCertificateInfo()
    {
        if (!$this->done) {
            throw new \InvalidArgumentException('The method getCertificateInfo() can only be called after calling the finish() method');
        }

        return $this->certificateInfo;
    }
}