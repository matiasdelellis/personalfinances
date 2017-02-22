<?php
namespace OCA\PersonalFinances\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\PersonalFinances\Service\ReportService;

class ReportController extends Controller {

    private $service;
    private $userId;

    use Errors;

    public function __construct($AppName, IRequest $request,
                                ReportService $service, $UserId) {
        parent::__construct($AppName, $request);
        $this->service = $service;
        $this->userId = $UserId;
    }

    /**
     * @NoAdminRequired
     */
    public function index() {
        return new DataResponse($this->service->reportAll($this->userId));
    }

    public function reportSince($timestamp) {
        return new DataResponse($this->service->reportSince($this->userId, $timestamp));
    }

}
