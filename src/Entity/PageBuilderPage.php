<?php

namespace JulianKoster\PageBuilderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PageBuilderPage
{
    private ?int $id = null;

    private ?string $name = null;

    private ?string $title = null;

    private ?string $locale = null;

    private Collection $blockInstance;

    private ?PageBuilderMeta $meta = null;

    public function __construct()
    {
        $this->blockInstance = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, PageBuilderBlockInstance>
     */
    public function getBlockInstance(): Collection
    {
        return $this->blockInstance;
    }

    public function addBlockInstance(PageBuilderBlockInstance $blockInstance): static
    {
        if (!$this->blockInstance->contains($blockInstance)) {
            $this->blockInstance->add($blockInstance);
            $blockInstance->setPageBuilderPage($this);
        }

        return $this;
    }

    public function removeBlockInstance(PageBuilderBlockInstance $blockInstance): static
    {
        if ($this->blockInstance->removeElement($blockInstance)) {
            // set the owning side to null (unless already changed)
            if ($blockInstance->getPageBuilderPage() === $this) {
                $blockInstance->setPageBuilderPage(null);
            }
        }

        return $this;
    }

    public function getMeta(): ?PageBuilderMeta
    {
        return $this->meta;
    }

    public function setMeta(?PageBuilderMeta $meta): static
    {
        $this->meta = $meta;

        return $this;
    }
}
