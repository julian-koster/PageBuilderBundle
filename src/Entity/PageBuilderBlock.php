<?php

namespace JulianKoster\PageBuilderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PageBuilderBlock
{
    private ?int $id = null;

    private ?string $name = null;

    private ?\DateTimeImmutable $addedAt = null;

    private ?string $screenshot = null;

    private Collection $category;

    private ?string $twigTemplatePath = null;

    private ?string $phpClass = null;

    private Collection $blockInstance;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->blockInstance = new ArrayCollection();
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

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    public function getScreenshot(): ?string
    {
        return $this->screenshot;
    }

    public function setScreenshot(?string $screenshot): static
    {
        $this->screenshot = $screenshot;

        return $this;
    }

    /**
     * @return Collection<int, PageBuilderBlockCategory>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(PageBuilderBlockCategory $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(PageBuilderBlockCategory $category): static
    {
        $this->category->removeElement($category);

        return $this;
    }

    public function getTwigTemplatePath(): ?string
    {
        return $this->twigTemplatePath;
    }

    public function setTwigTemplatePath(string $twigTemplatePath): static
    {
        $this->twigTemplatePath = $twigTemplatePath;

        return $this;
    }

    public function getPhpClass(): ?string
    {
        return $this->phpClass;
    }

    public function setPhpClass(?string $phpClass): static
    {
        $this->phpClass = $phpClass;

        return $this;
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
            $blockInstance->setPageBuilderBlock($this);
        }

        return $this;
    }

    public function removeBlockInstance(PageBuilderBlockInstance $blockInstance): static
    {
        if ($this->blockInstance->removeElement($blockInstance)) {
            // set the owning side to null (unless already changed)
            if ($blockInstance->getPageBuilderBlock() === $this) {
                $blockInstance->setPageBuilderBlock(null);
            }
        }

        return $this;
    }
}
