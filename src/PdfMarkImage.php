<?php

namespace Lacuna\RestPki\ClientLegacy;

class PdfMarkImage
{
    public $resource;
    public $opacity;

    public function __construct()
    {
        $args = func_get_args();
        if (sizeof($args) == 0) { // Case ()
            $this->opacity = 100;
            $this->resource = new ResourceContentOrReference();
        } elseif (sizeof($args) == 2) { // Case (imageContent, mimeType)
            $this->resource = new ResourceContentOrReference();
            $this->resource->content = base64_encode($args[0]);
            $this->resource->mimeType = $args[1];
        } else {
            throw new \InvalidArgumentException("Invalid parameters passed to the PdfMarkImage's Constructor.");
        }
    }
}