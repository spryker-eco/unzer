<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence;

use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerCustomerQuery;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItemQuery;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerQuery;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerShipmentChargeQuery;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransactionQuery;
use Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsQuery;
use Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsStoreQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use SprykerEco\Zed\Unzer\Persistence\Propel\Mapper\UnzerMapper;

/**
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface getEntityManager()
 */
class UnzerPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerQuery<\Orm\Zed\Unzer\Persistence\SpyPaymentUnzer>
     */
    public function getPaymentUnzerQuery(): SpyPaymentUnzerQuery
    {
        return SpyPaymentUnzerQuery::create();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItemQuery<\Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItem>
     */
    public function getPaymentUnzerOrderItemQuery(): SpyPaymentUnzerOrderItemQuery
    {
        return SpyPaymentUnzerOrderItemQuery::create();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransactionQuery<\Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransaction>
     */
    public function getPaymentUnzerTransactionQuery(): SpyPaymentUnzerTransactionQuery
    {
        return SpyPaymentUnzerTransactionQuery::create();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerCustomerQuery<\Orm\Zed\Unzer\Persistence\SpyPaymentUnzerCustomer>
     */
    public function getPaymentUnzerCustomerQuery(): SpyPaymentUnzerCustomerQuery
    {
        return SpyPaymentUnzerCustomerQuery::create();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Persistence\Propel\Mapper\UnzerMapper
     */
    public function getUnzerMapper(): UnzerMapper
    {
        return new UnzerMapper();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsStoreQuery<\Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsStore>
     */
    public function getUnzerCredentialsStoreQuery(): SpyUnzerCredentialsStoreQuery
    {
        return SpyUnzerCredentialsStoreQuery::create();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsQuery<\Orm\Zed\Unzer\Persistence\SpyUnzerCredentials>
     */
    public function getUnzerCredentialsQuery(): SpyUnzerCredentialsQuery
    {
        return SpyUnzerCredentialsQuery::create();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerShipmentChargeQuery<\Orm\Zed\Unzer\Persistence\SpyPaymentUnzerShipmentCharge>
     */
    public function getPaymentUnzerShipmentChargeQuery(): SpyPaymentUnzerShipmentChargeQuery
    {
        return SpyPaymentUnzerShipmentChargeQuery::create();
    }
}
