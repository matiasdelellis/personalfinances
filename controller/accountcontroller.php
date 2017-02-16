<?php
namespace OCA\PersonalFinances\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\PersonalFinances\Service\AccountService;

class AccountController extends Controller {

    private $service;
    private $userId;

    use Errors;

    public function __construct($AppName, IRequest $request,
                                AccountService $service, $UserId) {
        parent::__construct($AppName, $request);
        $this->service = $service;
        $this->userId = $UserId;
    }

    /**
     * @NoAdminRequired
     */
    public function index() {
        return new DataResponse($this->service->findAll($this->userId));
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function show($id) {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->find($id, $this->userId);
        });
    }

    /**
     * @NoAdminRequired
     *
     * @param string $name
     * @param integer $type
     * @param float $initial
     */
    public function create($name, $type, $initial) {
        return $this->service->create($name, $type, $initial, $this->userId);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $name
     * @param integer $type
     * @param float $initial
     */
    public function update($id, $name, $type, $initial) {
        return $this->handleNotFound(function () use ($id, $name, $type, $initial) {
            return $this->service->update($id, $name, $type, $initial, $this->userId);
        });
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function destroy($id) {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->delete($id, $this->userId);
        });
    }

}
