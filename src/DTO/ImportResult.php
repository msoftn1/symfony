<?php
namespace App\DTO;

/**
 * DTO с результатами импорт.
 */
class ImportResult
{
    /** Количество добавленных элементов. */
    private int $cntAdded;

    /** Количество обновленных элементов. */
    private int $cntUpdated;

    /**
     * Конструктор.
     *
     * @param int $cntAdded
     * @param int $cntUpdated
     */
    public function __construct(int $cntAdded, int $cntUpdated)
    {
        $this->cntAdded = $cntAdded;
        $this->cntUpdated = $cntUpdated;
    }

    /**
     * Получить количество добавленных элементов.
     *
     * @return int
     */
    public function getCntAdded(): int
    {
        return $this->cntAdded;
    }

    /**
     * Получить количество обновленных элементов.
     *
     * @return int
     */
    public function getCntUpdated(): int
    {
        return $this->cntUpdated;
    }
}
