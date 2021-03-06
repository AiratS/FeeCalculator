<?php

declare(strict_types=1);

namespace App\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\FileTransactionFeeCalculator;

class CalculateFeeCommand extends Command
{
    private const ARG_FILE_PATH = 'file-path';

    protected static $defaultName = 'app:calculate-fee';
    private FileTransactionFeeCalculator $feeCalculator;

    public function __construct(FileTransactionFeeCalculator $feeCalculator)
    {
        $this->feeCalculator = $feeCalculator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Calculates fee for given file.');
        $this->setHelp(implode(' ', [
            'An application that processes transactions in CSV',
            'format and calculates a commission based on certain rules.'
        ]));

        $this->addArgument(self::ARG_FILE_PATH, InputArgument::REQUIRED, 'Path to csv file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument(self::ARG_FILE_PATH);
        if (!file_exists($filePath)) {
            $output->writeln(sprintf('<error>The given file "%s" is not found</error>', $filePath));

            return Command::INVALID;
        }

        try {
            $fees = $this->feeCalculator->getCalculatedFees($filePath);
            foreach ($fees as $fee) {
                $output->writeln($fee);
            }
        } catch (Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
