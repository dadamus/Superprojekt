<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 28.02.2018
 * Time: 23:15
 */
class PlateMultipartDuplicator
{
    /** @var  int */
    private $newDirectoryId;

    /**
     * PlateMultipartDuplicator constructor.
     * @param int $directoryId
     * @throws Exception
     */
    public function __construct(int $directoryId)
    {
        global $db;

        try {
            $db->beginTransaction();
            $this->newDirectoryId = $this->copyDirectory($directoryId);

            $details = $this->getDetails($directoryId);
            $oldDetails = $details;

            $this->copyMpw($details);

            $this->saveDetails($details, $this->newDirectoryId);

            $programsPart = $this->getProgramsPart($oldDetails, $details);
            $this->clonePrograms($programsPart);
            $this->cloneProgramsPart($programsPart);
        } catch (\Exception $ex) {
            $db->rollBack();
            throw $ex;
        }
    }

    /**
     * @return int
     */
    public function getNewDirectoryId(): int
    {
        return $this->newDirectoryId;
    }

    /**
     * @param array $programsPart
     */
    private function cloneProgramsPart(array $programsPart)
    {
        foreach ($programsPart as $programPart) {
            $programPartInsert = new sqlBuilder(sqlBuilder::INSERT, 'plate_multiPartProgramsPart');
            $programPartInsert
                ->bindValue('DetailId', $programPart['DetailId'], PDO::PARAM_INT)
                ->bindValue('PartNo', $programPart['PartNo'], PDO::PARAM_INT)
                ->bindValue('PartName', $programPart['PartName'], PDO::PARAM_STR)
                ->bindValue('PartCount', $programPart['PartCount'], PDO::PARAM_STR)
                ->bindValue('UnfoldXSize', $programPart['UnfoldXSize'], PDO::PARAM_STR)
                ->bindValue('UnfoldYSize', $programPart['UnfoldYSize'], PDO::PARAM_STR)
                ->bindValue('LaserMatName', $programPart['LaserMatName'], PDO::PARAM_STR)
                ->bindValue('ProgramId', $programPart['ProgramId'], PDO::PARAM_INT)
                ->bindValue('CreateDate', date("Y-m-d H:i:s"), PDO::PARAM_STR)
                ->flush();
        }
    }

    /**
     * @param array $programsPart
     */
    private function clonePrograms(array &$programsPart)
    {
        global $db;

        $clonedMaterial = [];

        foreach ($programsPart as $key => $programPart) {
            $programDataQuery = $db->prepare("
              SELECT 
              *
              FROM
              plate_multiPartPrograms
              WHERE
              id = :programId
               ");
            $programDataQuery->bindValue(':programId', $programPart['ProgramId'], PDO::PARAM_INT);
            $programDataQuery->execute();

            $programData = $programDataQuery->fetch();

            if (!isset($clonedMaterial[$programData["materialId"]])) {
                $clonedMaterial[$programData["materialId"]] = $this->cloneMaterial($programData["materialId"]);
            }

            $programInsert = new sqlBuilder(sqlBuilder::INSERT, 'plate_multiPartPrograms');
            $programInsert
                ->bindValue('SheetName', $programData['SheetName'], PDO::PARAM_STR)
                ->bindValue('PreTime', $programData['PreTime'], PDO::PARAM_STR)
                ->bindValue('materialId', $clonedMaterial[$programData["materialId"]], PDO::PARAM_INT)
                ->bindValue('UsedSheetNum', $programData["UsedSheetNum"], PDO::PARAM_INT)
                ->bindValue('CreateDate', date("Y-m-d H:i:s"), PDO::PARAM_STR)
                ->bindValue('SheetCount', $programData["SheetCount"], PDO::PARAM_INT)
                ->flush();
            $newProgramId = $db->lastInsertId();
            $oldProgramId = $programPart['ProgramId'];

            $this->cloneFrame($oldProgramId, $newProgramId);

            $programsPart[$key]['ProgramId'] = $newProgramId;
        }
    }

    /**
     * @param int $oldProgramId
     * @param int $newProgramId
     */
    private function cloneFrame(int $oldProgramId, int $newProgramId)
    {
        global $db;

        $frameDataQuery = $db->prepare("
            SELECT 
            *
            FROM
            plate_costingFrame
            WHERE
            programId = :programId
            AND type = 'multiPartCosting'
        ");
        $frameDataQuery->bindValue(':programId', $oldProgramId, PDO::PARAM_INT);
        $frameDataQuery->execute();

        $frameData = $frameDataQuery->fetch();

        $newFrame = new sqlBuilder(sqlBuilder::INSERT, 'plate_costingFrame');
        $newFrame
            ->bindValue('imgId', $frameData['imgId'], PDO::PARAM_INT)
            ->bindValue('type', $frameData['type'], PDO::PARAM_STR)
            ->bindValue('points', $frameData['points'], PDO::PARAM_STR)
            ->bindValue('value', $frameData['value'], PDO::PARAM_STR)
            ->bindValue('programId', $newProgramId, PDO::PARAM_INT)
            ->flush();
    }

    /**
     * @param int $materialId
     * @return int
     */
    private function cloneMaterial(int $materialId): int
    {
        global $db;

        $materialDataQuery = $db->prepare("
            SELECT
            *
            FROM
            plate_multiPartCostingMaterial
            WHERE
            id = :materialId
        ");
        $materialDataQuery->bindValue(':materialId', $materialId, PDO::PARAM_INT);
        $materialDataQuery->execute();

        $materialData = $materialDataQuery->fetch();

        $materialInsert = new sqlBuilder(sqlBuilder::INSERT, 'plate_multiPartCostingMaterial');
        $materialInsert
            ->bindValue('SheetCode', $materialData['SheetCode'], PDO::PARAM_STR)
            ->bindValue('UsedSheetNum', $materialData['UsedSheetNum'], PDO::PARAM_INT)
            ->bindValue('MatName', $materialData['MatName'], PDO::PARAM_STR)
            ->bindValue('thickness', $materialData['thickness'], PDO::PARAM_STR)
            ->bindValue('SheetSize', $materialData['SheetSize'], PDO::PARAM_STR)
            ->bindValue('density', $materialData['density'], PDO::PARAM_STR)
            ->bindValue('price', $materialData['price'], PDO::PARAM_STR)
            ->bindValue('prgSheetPrice', $materialData['prgSheetPrice'], PDO::PARAM_STR)
            ->flush();
        return $db->lastInsertId();
    }

    /**
     * @param array $oldDetails
     * @return array
     */
    private function getProgramsPart(array $oldDetails, array $details): array
    {
        global $db;

        $programParts = [];
        $newDetail = reset($details);

        foreach ($oldDetails as $detail) {
            $programPartDataQuery = $db->prepare("
              SELECT
              pp.*
              FROM
              plate_multiPartProgramsPart pp
              WHERE
              pp.PartName = :partName
            ");
            $programPartDataQuery->bindValue(':partName', $detail['name'], PDO::PARAM_STR);
            $programPartDataQuery->execute();

            $programPart = $programPartDataQuery->fetch();
            $programPart['PartName'] = $newDetail['name'];
            $programParts[] = $programPart;

            $newDetail = next($details);
        }

        return $programParts;
    }

    /**
     * @param array $details
     */
    private function saveDetails(array $details, int $newDirectoryId)
    {
        foreach ($details as $detail) {
            $detailInsert = new sqlBuilder(sqlBuilder::INSERT, 'plate_multiPartDetails');
            $detailInsert
                ->bindValue('name', $detail['name'], PDO::PARAM_STR)
                ->bindValue('dirId', $newDirectoryId, PDO::PARAM_INT)
                ->bindValue('mpw', $detail['mpw'], PDO::PARAM_INT)
                ->bindValue('did', $detail['did'], PDO::PARAM_INT)
                ->bindValue('src', $detail['src'], PDO::PARAM_STR)
                ->flush();
        }
    }

    /**
     * @param array $details
     */
    private function copyMpw(array &$details)
    {
        global $db;

        foreach ($details as $key => $detail) {
            $detailsMpwId = $detail["mpw"];

            if (isset($detailsMpwId)) {
                continue;
            }

            $mpwDataQuery = $db->prepare("
                SELECT
                *
                FROM
                mpw
                WHERE
                id = :mpwId
            ");
            $mpwDataQuery->bindValue(":mpwId", $detail["mpw"], PDO::PARAM_INT);
            $mpwDataQuery->execute();

            $mpwData = $mpwDataQuery->fetch();

            $mpwInsert = new sqlBuilder(sqlBuilder::INSERT, "mpw");
            $mpwInsert
                ->bindValue('pid', $mpwData['pid'], PDO::PARAM_INT)
                ->bindValue('src', $mpwData['src'], PDO::PARAM_STR)
                ->bindValue('code', $mpwData['code'], PDO::PARAM_STR)
                ->bindValue('version', $mpwData['version'], PDO::PARAM_INT)
                ->bindValue('material', $mpwData['material'], PDO::PARAM_INT)
                ->bindValue('thickness', $mpwData['thickness'], PDO::PARAM_STR)
                ->bindValue('pieces', $mpwData['pieces'], PDO::PARAM_INT)
                ->bindValue('atribute', $mpwData['atribute'], PDO::PARAM_STR)
                ->bindValue('date', date("Y-m-d H:i:s"), PDO::PARAM_STR)
                ->bindValue('type', OT::AUTO_WYCENA_BLACH_MULTI_KROK_2, PDO::PARAM_INT)
                ->flush();
            $newMpwId = $db->lastInsertId();

            $oldDetailName = explode('-', $detail['name']);
            $oldDetailName[5] = $newMpwId;
            $newDetailName = implode('-', $oldDetailName);

            $oldDetailSrcWExt = explode('.', $detail['src']);
            $oldDetailSrc = explode('-', reset($oldDetailSrcWExt));
            $oldDetailSrc[5] = $newMpwId;
            $newDetailSrc = implode('-', $oldDetailSrc) . '.' . end($oldDetailSrcWExt);

            $details[$key]["mpw"] = $newMpwId;
            $details[$key]["name"] = $newDetailName;
            $details[$key]["src"] = $newDetailSrc;
        }
    }

    /**
     * @param int $directoryId
     * @return array
     */
    private function getDetails(int $directoryId): array
    {
        global $db;

        $directoryDetailsQuery = $db->prepare("
            SELECT
            *
            FROM
            plate_multiPartDetails
            WHERE
            dirId = :directoryId
        ");
        $directoryDetailsQuery->bindValue(":directoryId", $directoryId, PDO::PARAM_INT);
        $directoryDetailsQuery->execute();

        return $directoryDetailsQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $directoryId
     * @return int
     */
    private function copyDirectory(int $directoryId): int
    {
        global $db;

        $directoryQuery = $db->prepare("
            SELECT 
            dir_name,
            created_at
            FROM
            plate_multiPartDirectories
            WHERE
            id = :id
        ");
        $directoryQuery->bindValue(':id', $directoryId, PDO::PARAM_INT);
        $directoryQuery->execute();
        $directoryData = $directoryQuery->fetch();

        $directoryDate = new \DateTime("now");
        $newDirFolderId = $directoryId + 1;
        $newDirFolderName = $newDirFolderId . '/' . $directoryDate->format("m/Y");

        $directoryInsert = new sqlBuilder(sqlBuilder::INSERT, 'plate_multiPartDirectories');
        $directoryInsert
            ->bindValue('dir_name', $newDirFolderName, PDO::PARAM_STR)
            ->bindValue('created_at', date("Y-m-d H:i:s"), PDO::PARAM_STR)
            ->bindValue('parent_dir_id', $directoryId, PDO::PARAM_STR)
            ->flush();

        echo $newDirFolderName . "\n";
        echo $db->lastInsertId();

        die;
        return $db->lastInsertId();
    }
}