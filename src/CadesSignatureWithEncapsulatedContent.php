<?php

namespace Lacuna\RestPki\Legacy;

/**
 * Class CadesSignatureWithEncapsulatedContent
 * @package Lacuna\RestPki\Legacy
 *
 * @property-read mixed $signature
 * @property-read FileResult $encapsulatedContent
 */
class CadesSignatureWithEncapsulatedContent
{
    private $_signature;
    private $_encapsulatedContent;

    public function __construct($signature, $encapsulatedContent)
    {
        $this->_signature = $signature;
        $this->_encapsulatedContent = $encapsulatedContent;
    }

    public function getSignature()
    {
        return $this->_signature;
    }

    public function getEncapsulatedContent()
    {
        return $this->_encapsulatedContent;
    }

    public function __get($name)
    {
        switch ($name) {
            case "signature":
                return $this->getSignature();
            case "encapsulatedContent":
                return $this->getEncapsulatedContent();
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $name);
                return null;
        }
    }

    public function __isset($name)
    {
        switch ($name) {
            case "signature":
                return isset($this->_signature);
            case "encapsulatedContent":
                return isset($this->_encapsulatedContent);
            default:
                trigger_error('Undefined property: ' . __CLASS__ . '::$' . $name);
                return null;
        }
    }

}