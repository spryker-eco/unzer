<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence;

use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerCustomerQuery;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItemQuery;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerQuery;
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
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItemQuery
     */
    public function getPaymentUnzerOrderItemQuery(): SpyPaymentUnzerOrderItemQuery
    {
        return SpyPaymentUnzerOrderItemQuery::create();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransactionQuery
     */
    public function getPaymentUnzerTransactionQuery(): SpyPaymentUnzerTransactionQuery
    {
        return SpyPaymentUnzerTransactionQuery::create();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerCustomerQuery
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
     * @return \Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsStoreQuery
     */
    public function getUnzerCredentialsStoreQuery(): SpyUnzerCredentialsStoreQuery
    {
        return SpyUnzerCredentialsStoreQuery::create();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyUnzerCredentialsQuery
     */
    public function getUnzerCredentialsQuery(): SpyUnzerCredentialsQuery
    {
        return SpyUnzerCredentialsQuery::create();
    }
}
