<?php


namespace App\Service;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Сервис товаров.
 */
class ProductService
{
    /** Менеджер сущностей. */
    private EntityManagerInterface $entityManager;

    /**
     * Конструктор.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Сохранить товар.
     *
     * @param Product $product
     */
    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /**
     * Удалить товар.
     *
     * @param Product $product
     */
    public function delete(Product $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    /**
     * Получить все товары.
     *
     * @return array
     */
    public function getAllProducts(): array
    {
        return $this->entityManager
            ->getRepository(Product::class)
            ->findAll();
    }

    /**
     * Получить продукты по списку идентификаторов в системе импорта.
     *
     * @param array $eidList
     *
     * @return Product[]
     */
    public function getProductsByEid(array $eidList): array
    {
        if(!\count($eidList)) {
            return [];
        }

        /** @var EntityRepository $repository */
        $repository = $this->entityManager
            ->getRepository(Product::class);

        return $repository->createQueryBuilder("p")
            ->where("p.eid IN (:eIdList)")
            ->setParameter("eIdList", $eidList)
            ->getQuery()
            ->getResult();
    }

    /**
     * Сохранить товары.
     *
     * @param Product[] $products
     */
    public function saveProducts(array $products)
    {
        foreach($products as $product) {
            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
    }
}
