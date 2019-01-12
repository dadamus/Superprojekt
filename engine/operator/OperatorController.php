<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 14/11/2018
 * Time: 19:38
 */

require_once __DIR__ . '/../mainController.php';
require_once __DIR__ . '/Repository/ListRepository.php';
require_once __DIR__ . '/Model/ProgramColorGenerator.php';

/**
 * Class OperatorController
 */
class OperatorController extends mainController
{
    const STATUS_LIST = [
        0 => 'Oczekuje',
        1 => 'w trakcje',
        2 => 'Do potwierdzenia',
        3 => 'Wycięto',
        4 => 'Wstrzymany',
        5 => 'Anulowany',
        6 => 'Nie rozpoznany',
        7 => 'Poprawka'
    ];

    /**
     * @var ListRepository
     */
    private $listRepository;

    /**
     * OperatorController constructor.
     */
    public function __construct()
    {
        $this->listRepository = new ListRepository();
        $this->setViewPath(__DIR__ . '/view/');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function cutListAction(): string
    {
        $programs = $this->listRepository->getPrograms([0]);
        $this->updatePlateWaste($programs);
        return $this->render('cutList.php', [
            'programs' => $programs
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function historyListAction(): string
    {
        $programs = $this->listRepository->getProgramsWithStatusColor([1], 'cq.modified_at DESC');
        $this->updatePlateWaste($programs);
        return $this->render('historyList.php', [
            'programs' => $programs
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function bufferListAction(): string
    {
        $programs = $this->listRepository->getPrograms([2]);
        //$this->updatePlateWaste($programs);
        return $this->render('cutList.php', [
            'programs' => $programs
        ]);

    }

    /**
     * @return string
     * @throws Exception
     */
    public function correctionListAction(): string
    {
        $programs = $this->listRepository->getPrograms();

        return $this->render('correctionList.php', [
            'programs' => $programs
        ]);
    }

    /**
     * @param int $programId
     * @param bool $extended
     * @return string
     * @throws Exception
     */
    public function programDetailsAction(int $programId, bool $extended = false): string
    {
        $programData = $this->listRepository->getProgramData($programId);
        $data = [
            'program' => $programData,
            'mpwData' => $this->listRepository->getMPWData((int)$programData['new_cutting_queue_id']),
            'list' => $this->listRepository->getQueueList($programData['new_cutting_queue_id']),
            'image' => str_replace('/var/www/html', '', $programData['image_src']),
            'statusList' => self::STATUS_LIST
        ];

        if ($extended) {
            $data['details'] = $this->listRepository->getDetailsData($programData['new_cutting_queue_id']);
        }

        return $this->render('details.php', $data);
    }

    /**
     * @param array $programs
     */
    private function updatePlateWaste(array $programs)
    {
        foreach ($programs as $program) {
            $this->listRepository->updatePlateWaste($program);
        }
    }
}