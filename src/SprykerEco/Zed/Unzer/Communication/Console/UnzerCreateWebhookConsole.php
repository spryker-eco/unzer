<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Console;

use Generated\Shared\Transfer\UnzerNotificationConfigTransfer;
use Spryker\Zed\Kernel\Communication\Console\Console;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \SprykerEco\Zed\Unzer\Business\UnzerFacade getFacade()
 * @method \SprykerEco\Zed\Unzer\Communication\UnzerCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface getRepository()
 */
class UnzerCreateWebhookConsole extends Console
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'unzer:register-webhook';

    /**
     * @var string
     */
    public const DESCRIPTION = 'Register new webhook on unzer server. Use the -e ';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::DESCRIPTION)
            ->addOption('event', 'e', InputOption::VALUE_OPTIONAL, 'Parameter event, default `all`.', 'all');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $event */
        $event = $input->getOption('event');
        $url = $this->getFactory()->getConfig()->getWebhookRetrieveUrl();

        $unzerNotificationConfigTransfer = (new UnzerNotificationConfigTransfer())
            ->setEvent($event)
            ->setUrl($url);

        try {
            $this->getFacade()->setUnzerNotificationUrl($unzerNotificationConfigTransfer);
        } catch (UnzerException $unzerApiException) {
            $output->writeln('Failed to add webhook:');
            $output->writeln(sprintf(' - Message: %s', $unzerApiException->getMessage()));

            return static::CODE_ERROR;
        }

        $output->writeln('Successfully added webhook:');
        $output->writeln(sprintf(' - Url: %s', $url));
        $output->writeln(sprintf(' - Event: %s', $event));

        return static::CODE_SUCCESS;
    }
}
