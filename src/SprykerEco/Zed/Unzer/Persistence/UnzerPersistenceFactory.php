<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Persistence;

use Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipantQuery;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItemQuery;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerQuery;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransactionQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use SprykerEco\Zed\Unzer\Persistence\Mapper\UnzerPersistenceMapper;

/**
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface getEntityManager()
 */
class UnzerPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyMerchantUnzerParticipantQuery
     */
    public function createMerchantUnzerParticipantQuery(): SpyMerchantUnzerParticipantQuery
    {
        return SpyMerchantUnzerParticipantQuery::create();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerQuery
     */
    public function createPaymentUnzerQuery(): SpyPaymentUnzerQuery
    {
        return SpyPaymentUnzerQuery::create();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItemQuery
     */
    public function createPaymentUnzerOrderItemQuery(): SpyPaymentUnzerOrderItemQuery
    {
        return SpyPaymentUnzerOrderItemQuery::create();
    }

    /**
     * @return \Orm\Zed\Unzer\Persistence\SpyPaymentUnzerTransactionQuery
     */
    public function createPaymentUnzerTransactionQuery(): SpyPaymentUnzerTransactionQuery
    {
        return SpyPaymentUnzerTransactionQuery::create();
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Persistence\Mapper\UnzerPersistenceMapper
     */
    public function createUnzerPersistenceMapper(): UnzerPersistenceMapper
    {
        return new UnzerPersistenceMapper();
    }
}
