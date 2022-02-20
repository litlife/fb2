<?php

namespace Litlife\Fb2;

use Imagick;
use ImagickException;

class Binary
{
    private string $blob;
    private string $id;
    private mixed $content_type;
    private Imagick $imagick;
    private Fb2 $fb2;

    public function __construct(Fb2 $fb2, string $id, string $content_type = null)
    {
        $this->fb2 = $fb2;
        $this->id = urldecode($id);
        $this->content_type = $content_type;
        $this->fb2->binaries[$id] = $this;
    }

    public function setContentAsBase64(string $base64)
    {
        $this->blob = base64_decode($base64);
    }

    public function getContentAsBase64(): string
    {
        return base64_encode($this->blob);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * @throws \ImagickException
     */
    public function open($blob)
    {
        $this->blob = $blob;

        $this->content_type = $this->getImagick()->getImageMimeType();
    }

    /**
     * @throws \ImagickException
     */
    public function getImagick(): Imagick
    {
        if (empty($this->imagick)) {
            $this->imagick = new Imagick();
            $this->imagick->readImageBlob($this->getContent());
        }

        return $this->imagick;
    }

    public function getContent(): string
    {
        return $this->blob;
    }

    public function isValidImage(): bool
    {
        try {
            return $this->getImagick()->valid();
        } catch (ImagickException) {
            return false;
        }
    }
}
