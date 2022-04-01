<?php

declare(strict_types=1);

namespace App\Command;

use ParagonIE\Halite\Alerts\HaliteAlert;
use ParagonIE\Halite\KeyFactory;
use SodiumException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A console command that creates a Halite key and stores it in a file.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console app:create-key
 *
 * To output detailed information, increase the command verbosity:
 *
 *     $ php bin/console app:create-key -vv
 *
 * See https://symfony.com/doc/current/console.html
 *
 * We use the default services.yaml configuration, so command classes are registered as services.
 * See https://symfony.com/doc/current/console/commands_as_services.html
 */
class CreateHaliteCommand extends Command
{
	/**
     * to make your command lazily loaded, configure the $defaultName static property,
     * so it will be instantiated only when the command is actually called.
     *
	 * @var string
	 */
	protected static $defaultName = 'app:create-key';

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
