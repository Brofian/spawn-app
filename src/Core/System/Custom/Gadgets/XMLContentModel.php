<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Gadgets;

use SimpleXMLElement;
use SpawnCore\System\Custom\Collection\Collection;

class XMLContentModel
{

    protected array $attributes = array();
    protected string $type = "";
    protected ?string $value = null;
    protected array $children = array();

    public function __construct(string $type)
    {
        $this->type = $type;
    }


    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $key, $default = null): ?string
    {
        return $this->attributes[$key] ?? $default;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getChildrenByType(string $key): Collection
    {
        $childrenWithTag = new Collection();
        foreach ($this->getChildren() as $child) {
            if ($child->type === $key) {
                $childrenWithTag->add($child);
            }
        }

        return $childrenWithTag;
    }

    /**
     * @return XMLContentModel[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function loadFromSimpleXMLElement(SimpleXMLElement $simpleXMLElement, string $filePath): void
    {

        foreach ($simpleXMLElement->attributes() as $attribute) {
            $this->addAttribute(
                $attribute->getName(),
                (string)$attribute[0]
            );
        }

        foreach ($simpleXMLElement->children() as $key => $child) {

            if ($key === 'import' && isset($child->attributes()["file"])) {

                $relPath = $child->attributes()["file"];

                $combinedPath = URIHelper::joinPaths(dirname($filePath), (string)$relPath);

                $childXML = XMLReader::readFile($combinedPath);

                foreach ($childXML->getChildren() as $cKey => $cChild) {
                    $this->addChild($cChild);
                }

            } else {
                $childXML = new XMLContentModel($key);

                $childXML->loadFromSimpleXMLElement($child, $filePath);

                if (count($childXML->getChildren()) < 1) {
                    $childXML->setValue((string)$child[0]);
                }

                $this->addChild($childXML);
            }


        }

    }

    public function addAttribute(string $key, string $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function addChild(XMLContentModel $child): self
    {
        $this->children[] = $child;
        return $this;
    }

}