<?php

declare(strict_types=1);

namespace App\Command;

use ParagonIE\Halite\Alerts\HaliteAlert;
use ParagonIE\Halite\KeyFactory;
use SodiumException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-key')]
class CreateHaliteCommand extends Command
{
    /**
     * @var string
     */
    private string $encryptionKeyDir = './';

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Creates a Halite key and stores it in a file');
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws SodiumException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $keyFileName = 'Halite.key';
            $encKey = KeyFactory::generateEncryptionKey();
            $success = KeyFactory::save($encKey, $this->encryptionKeyDir.$keyFileName);
            if (false === $success) {
                $output->write('<error>Error occured during key writing on disk.</error>');

                return 1;
            }
        } catch (HaliteAlert $exception) {
            $output->write('<error>'.$exception->getMessage().'</error>');

            return 1;
        }

        return 0;
    }
}
