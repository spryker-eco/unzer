<?php

use SprykerEco\Shared\Unzer\UnzerConstants;

$config[UnzerConstants::UNZER_AUTHORIZE_RETURN_URL] = 'https://spryker.com/checkout/success';
$config[UnzerConstants::UNZER_CHARGE_RETURN_URL] = 'https://spryker.com/checkout/success';
$config[UnzerConstants::WEBHOOK_RETRIEVE_URL] = 'https://spryker.com';
$config[UnzerConstants::VAULT_DATA_TYPE] = 'UNZER_CREDENTIALS_PRIVATE_KEY';
$config[UnzerConstants::EXPENSES_REFUND_STRATEGY_KEY] = UnzerConstants::NO_EXPENSES_REFUND_STRATEGY;
