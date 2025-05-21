<?php

namespace JulianKoster\PageBuilderBundle\Entity;

class PageBuilderBlockOverrides
{

    private ?int $id = null;
    private ?string $instanceId = null;

    private ?string $fieldKey = null;

    private ?string $fieldValue = null;

    private ?string $type = null;

    private ?PageBuilderBlockInstance $pageBuilderBlockInstance = null;

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

    public function getFieldKey(): ?string
    {
        return $this->fieldKey;
    }

    public function setFieldKey(string $fieldKey): static
    {
        $this->fieldKey = $fieldKey;

        return $this;
    }

    public function getFieldValue(): ?string
    {
        return $this->fieldValue;
    }

    public function setFieldValue(?string $fieldValue): static
    {
        $this->fieldValue = $fieldValue;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPageBuilderBlockInstance(): ?PageBuilderBlockInstance
    {
        return $this->pageBuilderBlockInstance;
    }

    public function setPageBuilderBlockInstance(?PageBuilderBlockInstance $pageBuilderBlockInstance): static
    {
        $this->pageBuilderBlockInstance = $pageBuilderBlockInstance;

        return $this;
    }
}
