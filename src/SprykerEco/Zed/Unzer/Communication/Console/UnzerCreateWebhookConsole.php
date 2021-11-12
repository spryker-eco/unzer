<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication\Console;

use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiSetWebhookRequestTransfer;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function Functional\first;

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

        $webHookRequest = (new UnzerApiSetWebhookRequestTransfer())
            ->setEvent($event)
            ->setRetrieveUrl($url);
        $unzerApiRequestTransfer = (new UnzerApiRequestTransfer())->setSetWebhookRequest($webHookRequest);

        $result = $this->getFacade()->performSetNotificationUrlApiCall($unzerApiRequestTransfer);

        if ($result->getIsSuccess()) {
            $output->writeln('Successfully added webhook:');
            $output->writeln(sprintf(' - Url: %s', $url));
            $output->writeln(sprintf(' - Event: %s', $event));

            return static::CODE_SUCCESS;
        }

        $errorResponse = first($result->getErrorResponse()->getErrors());
        $output->writeln('Failed to add webhook:');
        $output->writeln(sprintf(' - Code: %s', $errorResponse->getCode()));
        $output->writeln(sprintf(' - Message: %s', $errorResponse->getMerchantMessage()));

        return static::CODE_ERROR;
    }
}
