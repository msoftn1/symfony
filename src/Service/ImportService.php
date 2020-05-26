<?php

namespace App\Service;

use App\DTO\ImportResult;
use App\Entity\Category;
use App\Entity\Product;
use App\Exception\ImportException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Сервис импорта.
 */
class ImportService
{
    /** Сервис категорий. */
    private CategoryService $categoryService;

    /** Сервис товаров. */
    private ProductService $productService;

    /** Сервис валидации. */
    private ValidatorInterface $validator;

    /**
     * Конструктор.
     *
     * @param CategoryService $categoryService
     * @param ProductService $productService
     * @param ValidatorInterface $validator
     */
    public function __construct(CategoryService $categoryService, ProductService $productService, ValidatorInterface $validator)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;
        $this->validator = $validator;
    }

    /**
     * Импорт категорий из JSON файла.
     *
     * @param $pathToFile
     *
     * @return ImportResult
     * @throws ImportException
     */
    public function importCategories(string $pathToFile): ImportResult
    {
        $raw = $this->loadJson($pathToFile);
        $json = $this->parseJson($raw);

        $jsonByEid = [];
        $eIdList = [];
        foreach ($json as $item) {
            if (!\array_key_exists('eId', $item) || !\array_key_exists('title', $item)) {
                throw new ImportException('В JSON отсутствуют одно или несколько полей eId,title');
            }

            $eIdList[] = (int)$item['eId'];
            $jsonByEid[(int)$item['eId']] = $item;
        }

        unset($raw);
        unset($json);

        $updateList = [];
        $cntUpdated = 0;

        //update categories
        $categories = $this->categoryService->getCategoriesByEid($eIdList);
        foreach ($categories as $category) {
            if (\array_key_exists($category->getEid(), $jsonByEid)) {
                $category->setTitle($jsonByEid[$category->getEid()]['title']);
                $updateList[] = $category;

                $cntUpdated++;
                unset($jsonByEid[$category->getEid()]);
            }
        }

        //import new
        $eidListCheck = [];
        $cntAdded = 0;

        foreach ($jsonByEid as $item) {
            $category = new Category();
            $category->setTitle($item['title'])
                ->setEid((int)$item['eId']);

            $updateList[] = $category;

            if (\array_key_exists($item['eId'], $eidListCheck)) {
                throw new ImportException('Обнаружены дубликаты по eId');
            }

            $cntAdded++;
            $eidListCheck[$item['eId']] = 1;
        }

        //validate
        foreach ($updateList as $category) {
            $errors = $this->validator->validate($category);

            if (count($errors) > 0) {
                throw new ImportException(\sprintf("%s: %s", $errors[0]->getPropertyPath(), $errors[0]->getMessage()));
            }
        }

        $this->categoryService->saveCategories($updateList);

        return new ImportResult($cntAdded, $cntUpdated);
    }

    /**
     * Импорт товаров из JSON файла.
     *
     * @param $pathToFile
     *
     * @return ImportResult
     * @throws ImportException
     */
    public function importProducts(string $pathToFile): ImportResult
    {
        $raw = $this->loadJson($pathToFile);
        $json = $this->parseJson($raw);


        $jsonByEid = [];
        $eIdList = [];
        foreach ($json as $item) {
            if (!\array_key_exists('eId', $item)
                || !\array_key_exists('title', $item)
                || !\array_key_exists('price', $item)
                || !\array_key_exists('categoriesEId', $item)
                || !\is_array($item['categoriesEId'])
            ) {
                throw new ImportException('В JSON отсутствуют одно или несколько полей eId,title,price,categoriesEId или categoriesEId не массив');
            }

            $eIdList[] = (int)$item['eId'];
            $jsonByEid[(int)$item['eId']] = $item;
        }

        unset($raw);
        unset($json);

        $updateList = [];
        $cntUpdated = 0;

        //update products
        $products = $this->productService->getProductsByEid($eIdList);
        foreach ($products as $product) {
            if (\array_key_exists($product->getEid(), $jsonByEid)) {
                $product
                    ->setTitle($jsonByEid[$product->getEid()]['title'])
                    ->setPrice($jsonByEid[$product->getEid()]['price']);

                $product->removeCategories();

                $categories = $this->categoryService->getCategoriesByEid($jsonByEid[$product->getEid()]['categoriesEId']);

                foreach($categories as $category) {
                    $product->addCategory($category);
                }

                $updateList[] = $product;

                $cntUpdated++;
                unset($jsonByEid[$product->getEid()]);
            }
        }

        //import new
        $eidListCheck = [];
        $cntAdded = 0;

        foreach ($jsonByEid as $item) {
            $product = new Product();
            $product->setTitle($item['title'])
                ->setPrice((float)$item['price'])
                ->setEid((int)$item['eId']);

            $categories = $this->categoryService->getCategoriesByEid($item['categoriesEId']);

            foreach($categories as $category) {
                $product->addCategory($category);
            }

            $updateList[] = $product;

            if (\array_key_exists($item['eId'], $eidListCheck)) {
                throw new ImportException('Обнаружены дубликаты по eId');
            }

            $cntAdded++;
            $eidListCheck[$item['eId']] = 1;
        }

        //validate
        foreach ($updateList as $product) {
            $errors = $this->validator->validate($product);

            if (count($errors) > 0) {
                throw new ImportException(\sprintf("%s: %s", $errors[0]->getPropertyPath(), $errors[0]->getMessage()));
            }
        }

        $this->productService->saveProducts($updateList);

        return new ImportResult($cntAdded, $cntUpdated);
    }

    /**
     * Загрузка JSON файла.
     *
     * @param $pathToFile
     *
     * @return string
     * @throws ImportException
     */
    private function loadJson(string $pathToFile): string
    {
        try {
            return \file_get_contents($pathToFile);
        } catch (\Exception $e) {
            throw new ImportException(\sprintf("Ошибка загрузки JSON файла %s", $pathToFile), 0, $e);
        }
    }

    /**
     * Парсинг JSON.
     *
     * @param string $raw
     *
     * @return array
     * @throws ImportException
     */
    private function parseJson(string $raw)
    {
        try {
            return \json_decode($raw, true, 4, \JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            throw new ImportException(\sprintf("Ошибка парсинга JSON"), 0, $e);
        }
    }
}
