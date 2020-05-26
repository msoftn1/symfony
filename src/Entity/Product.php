<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Сущность товара.
 *
 * @ORM\Table(name="product", uniqueConstraints={@ORM\UniqueConstraint(name="product_eId_uindex", columns={"eId"})})
 * @ORM\Entity
 */
class Product
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
     * Цена.
     *
     * @Assert\NotNull
     * @Assert\Range(min=0, max=200)
     *
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=3, scale=2, nullable=false)
     */
    private $price;

    /**
     * Идентификатор в системе импорта.
     *
     * @var int|null
     *
     * @ORM\Column(name="eId", type="integer", nullable=true)
     */
    private $eid;

    /**
     * Категории товара.
     *
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="products", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="category_product",
     *     joinColumns={
     *          @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *     }
     * )
     */
    private $categories;

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
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
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Получить цену.
     *
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Установить цену.
     *
     * @param float $price
     *
     * @return $this
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Получить идентификатор в системе импорта.
     *
     * @return int|null
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
     * Получить категории.
     *
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * Добавить категорию в товар.
     *
     * @param Category $category
     *
     * @return $this
     */
    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    /**
     * Удалить категорию у товара.
     *
     * @param Category $category
     *
     * @return $this
     */
    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * Удалить все категории у товара.
     *
     * @return $this
     */
    public function removeCategories(): self
    {
        $this->categories->clear();

        return $this;
    }
}
