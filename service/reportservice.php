<?php
namespace OCA\PersonalFinances\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;


class ReportService {

    /**
     * @var IDBConnection
     */
    private $connection;

    public function __construct(IDBConnection $connection) {
        $this->connection = $connection;
    }

    public function reportAll($userId) {
        $builder = $this->connection->getQueryBuilder();
        $query = $builder->select(['T.id', 'T.date', 'T.info', 'T.amount', 'CP.name', 'C.id', 'C.name'])
                         ->selectAlias('CP.name', 'cat_parent_name')
                         ->selectAlias('C.name', 'cat_name')
                         ->from('personalfinances_transactions', 'T')
                         ->leftJoin('T', 'personalfinances_categories', 'C', $builder->expr()->eq('T.category', 'C.id'))
                         ->leftJoin('C', 'personalfinances_categories', 'CP', $builder->expr()->eq('C.parent', 'CP.id'))
                         ->where($builder->expr()->eq('T.dst_account', $builder->createNamedParameter('0')))
                         ->andWhere($builder->expr()->eq('T.user_id', $builder->createNamedParameter($userId)))
                         ->orderBy('T.category', 'ASC')
                         ->addOrderBy('T.amount', 'ASC');
        $result = $query->execute();

        $data = $result->fetchAll();
        $result->closeCursor();

        return $data;
    }

    public function reportSince($userId, $timestamp) {
        $builder = $this->connection->getQueryBuilder();
        $query = $builder->select(['T.id', 'T.date', 'T.info', 'T.amount', 'CP.name', 'C.id', 'C.name'])
                         ->selectAlias('CP.name', 'cat_parent_name')
                         ->selectAlias('C.name', 'cat_name')
                         ->from('personalfinances_transactions', 'T')
                         ->leftJoin('T', 'personalfinances_categories', 'C', $builder->expr()->eq('T.category', 'C.id'))
                         ->leftJoin('C', 'personalfinances_categories', 'CP', $builder->expr()->eq('C.parent', 'CP.id'))
                         ->where($builder->expr()->eq('T.dst_account', $builder->createNamedParameter('0')))
                         ->andWhere($builder->expr()->eq('T.user_id', $builder->createNamedParameter($userId)))
                         ->andWhere($builder->expr()->gt('T.date', $builder->createNamedParameter($timestamp)))
                         ->orderBy('T.category', 'ASC')
                         ->addOrderBy('T.amount', 'ASC');
        $result = $query->execute();

        $data = $result->fetchAll();
        $result->closeCursor();

        return $data;
    }

}
