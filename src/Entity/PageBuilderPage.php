<?php

namespace JulianKoster\PageBuilderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JulianKoster\PageBuilderBundle\Enum\PageBuilderPageStatus;

class PageBuilderPage
{
    private ?int $id = null;

    private ?string $name = null;

    private ?string $title = null;

    private ?string $locale = null;

    private Collection $blockInstance;

    private ?PageBuilderMeta $meta = null;

    # A child page is a child of a parent page. E.g. the About Us page is originally written in English (the parent).
    # Each child page should reference the parent rather than having a child of a child of a child. Just to keep things a bit more organized.
    private ?bool $childPage = null;

    /**
     * @var Collection<int, PageBuilderPage>
     */
    private Collection $translations;

    private ?string $status = null;

    private function getAllowedStatusTypes(): array
    {
        return PageBuilderPageStatus::cases();
    }

    public function __construct()
    {
        $this->blockInstance = new ArrayCollection();
        $this->translations = new ArrayCollection();
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

    public function setChildPage(?bool $childPage): void
    {
        $this->childPage = $childPage;
    }

    public function getChildPage(): ?bool
    {
        return $this->childPage;
    }

    /**
     * @return Collection<int,PageBuilderPage>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(PageBuilderPage $page): static
    {
        if (!$this->translations->contains($page)) {
            $this->translations->add($page);
            $page->addTranslation($this);
        }

        return $this;
    }

    public function removeTranslation(PageBuilderPage $page): static
    {
        if ($this->translations->removeElement($page)) {
            $page->removeTranslation($this);
        }

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $allowedStatuses = $this->getAllowedStatusTypes();

        foreach($allowedStatuses as $allowedStatus)
        {
            if($status == $allowedStatus->value)
            {
                $this->status = $status;
                return $this;
            }
        }

        return $this;
    }
}
