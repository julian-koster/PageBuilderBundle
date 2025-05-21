<?php

namespace JulianKoster\PageBuilderBundle\Entity;

class PageBuilderMeta
{
    private ?int $id = null;

    private ?string $title = null;

    private ?string $metaDescription = null;

    private ?string $image = null;

    private ?array $metaKeywords = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): static
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getMetaKeywords(): ?array
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(?array $metaKeywords): static
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }
}
