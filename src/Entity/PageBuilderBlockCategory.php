<?php

namespace JulianKoster\PageBuilderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PageBuilderBlockCategory
{
    private ?int $id = null;

    private ?string $name = null;

    private Collection $pageBuilderBlocks;

    public function __construct()
    {
        $this->pageBuilderBlocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, PageBuilderBlock>
     */
    public function getPageBuilderBlocks(): Collection
    {
        return $this->pageBuilderBlocks;
    }

    public function addPageBuilderBlock(PageBuilderBlock $pageBuilderBlock): static
    {
        if (!$this->pageBuilderBlocks->contains($pageBuilderBlock)) {
            $this->pageBuilderBlocks->add($pageBuilderBlock);
            $pageBuilderBlock->addCategory($this);
        }

        return $this;
    }

    public function removePageBuilderBlock(PageBuilderBlock $pageBuilderBlock): static
    {
        if ($this->pageBuilderBlocks->removeElement($pageBuilderBlock)) {
            $pageBuilderBlock->removeCategory($this);
        }

        return $this;
    }
}
