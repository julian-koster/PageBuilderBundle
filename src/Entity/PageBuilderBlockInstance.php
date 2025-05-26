<?php

namespace JulianKoster\PageBuilderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PageBuilderBlockInstance
{
    private ?int $id = null;

    private ?string $instanceId = null;

    private Collection $overrides;

    private ?int $position = null;

    private ?PageBuilderPage $pageBuilderPage = null;

    private ?PageBuilderBlock $pageBuilderBlock = null;

    private ?array $layoutConfig = null;

    public function __construct()
    {
        $this->overrides = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstanceId(): ?string
    {
        return $this->instanceId;
    }

    public function setInstanceId(string $instanceId): static
    {
        $this->instanceId = $instanceId;

        return $this;
    }

    /**
     * @return Collection<int, PageBuilderBlockOverrides>
     */
    public function getOverrides(): Collection
    {
        return $this->overrides;
    }

    public function addOverride(PageBuilderBlockOverrides $override): static
    {
        if (!$this->overrides->contains($override)) {
            $this->overrides->add($override);
            $override->setPageBuilderBlockInstance($this);
        }

        return $this;
    }

    public function removeOverride(PageBuilderBlockOverrides $override): static
    {
        if ($this->overrides->removeElement($override)) {
            if ($override->getPageBuilderBlockInstance() === $this) {
                $override->setPageBuilderBlockInstance(null);
            }
        }

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getPageBuilderPage(): ?PageBuilderPage
    {
        return $this->pageBuilderPage;
    }

    public function setPageBuilderPage(?PageBuilderPage $pageBuilderPage): static
    {
        $this->pageBuilderPage = $pageBuilderPage;

        return $this;
    }

    public function getPageBuilderBlock(): ?PageBuilderBlock
    {
        return $this->pageBuilderBlock;
    }

    public function setPageBuilderBlock(?PageBuilderBlock $pageBuilderBlock): static
    {
        $this->pageBuilderBlock = $pageBuilderBlock;

        return $this;
    }

    public function getLayoutConfig(): ?array
    {
        return $this->layoutConfig;
    }

    public function setLayoutConfig(?array $layoutConfig): static
    {
        $this->layoutConfig = $layoutConfig;

        return $this;
    }
}
