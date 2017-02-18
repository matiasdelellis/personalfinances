<?php
namespace OCA\PersonalFinances\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\PersonalFinances\Service\TransactionService;

class TransactionController extends Controller {

    private $transactionService;
    private $userId;

    use Errors;

    public function __construct($AppName, IRequest $request, TransactionService $transactionService, $UserId) {
        parent::__construct($AppName, $request);
        $this->transactionService = $transactionService;
        $this->userId = $UserId;
    }

    /**
     * @NoAdminRequired
     */
    public function index() {
        return new DataResponse($this->transactionService->findAll($this->userId));
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function show($id) {
        return $this->handleNotFound(function () use ($id) {
            return $this->transactionService->find($id, $this->userId);
        });
    }

    /**
     * @NoAdminRequired
     *
     * @param int $account
     */
    public function findAllAccount($account) {
        return $this->transactionService->findAllAccount($account, $this->userId);
    }

    /**
     * @NoAdminRequired
     *
     * @param string $name
     * @param integer $type
     * @param float $initial
     */
    public function create($date, $amount, $account, $dst_account, $paymode, $flags, $category, $info) {
        return $this->transactionService->create($date, $amount, $account, $dst_account, $paymode, $flags, $category, $info, $this->userId);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $name
     * @param integer $type
     * @param float $initial
     */
    public function update($id, $date, $amount, $account, $dst_account, $paymode, $flags, $category, $info) {
        return $this->handleNotFound(function () use ($id, $date, $amount, $account, $dst_account, $paymode, $flags, $category, $info) {
            return $this->transactionService->update($id, $date, $amount, $account, $dst_account, $paymode, $flags, $category, $info, $this->userId);
        });
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function destroy($id) {
        return $this->handleNotFound(function () use ($id) {
            return $this->transactionService->delete($id, $this->userId);
        });
    }

}
