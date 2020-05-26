<?php
namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Сервис категорий.
 */
class CategoryService
{
    /** Менеджер сущностей. */
    private EntityManagerInterface $entityManager;

    /**
     * Конструктор.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Получить категории по списку идентификаторов в системе импорта.
     *
     * @param array $eidList
     *
     * @return Category[]
     */
    public function getCategoriesByEid(array $eidList): array
    {
        if(!\count($eidList)) {
            return [];
        }

        /** @var EntityRepository $repository */
        $repository = $this->entityManager
            ->getRepository(Category::class);

        return $repository->createQueryBuilder("c")
            ->where("c.eid IN (:eIdList)")
            ->setParameter("eIdList", $eidList)
            ->getQuery()
            ->getResult();
    }

    /**
     * Сохранить категории.
     *
     * @param Category[] $categories
     */
    public function saveCategories(array $categories)
    {
        foreach($categories as $category) {
            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();
    }
}
