<?php

namespace App\Command;

use App\Exception\ImportException;
use App\Service\ImportService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Команда импорта товаров.
 */
class ImportProductsCommand extends Command
{
    /** @var string Адрес команды. */
    protected static $defaultName = 'app:import-products';

    /** Сервис импорта. */
    private ImportService $importService;

    /**
     * Конструктор.
     *
     * @param ImportService $importService
     */
    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Импорт товаров')
            ->addArgument('path', InputArgument::REQUIRED, 'Путь к JSON файлу с товарами');
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path  = $input->getArgument('path');

        try {
            $res = $this->importService->importProducts($path);

            $io->success(\sprintf('Операция импорта выполнена успешно! Добавлено: %s, обновлено: %s', $res->getCntAdded(), $res->getCntUpdated()));
        }
        catch (ImportException $e) {
            $io->error($e->getMessage());
        }

        return 0;
    }
}
