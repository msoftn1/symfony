<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Сущность категории.
 *
 * @ORM\Table(name="category", uniqueConstraints={@ORM\UniqueConstraint(name="category_eId_uindex", columns={"eId"})})
 * @ORM\Entity
 */
class Category
{
    /**
     * Идентификатор.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Название.
     *
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=12)
     *
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=12, nullable=false)
     */
    private $title;

    /**
     * Идентификатор в системе импорта.
     *
     * @var int|null
     *
     * @ORM\Column(name="eId", type="integer", nullable=true)
     */
    private $eid;

    /**
     * Товары категории.
     *
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="categories")
     */
    private $products;

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * Получить идентификатор.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Получить название.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Установить название.
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Получить идентификатор в системе импорта.
     */
    public function getEid(): ?int
    {
        return $this->eid;
    }

    /**
     * Установить идентификатор в системе импорта.
     *
     * @param int|null $eid
     *
     * @return $this
     */
    public function setEid(?int $eid): self
    {
        $this->eid = $eid;

        return $this;
    }

    /**
     * Получить товары.
     *
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * Добавить товар в категорию.
     *
     * @param Product $product
     *
     * @return $this
     */
    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addCategory($this);
        }

        return $this;
    }

    /**
     * Удалить товар из категории.
     *
     * @param Product $product
     *
     * @return $this
     */
    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removeCategory($this);
        }

        return $this;
    }

    /**
     * Строковое представление.
     *
     * @return string|null
     */
    public function __toString()
    {
        return $this->getTitle();
    }
}
