<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 14/11/2018
 * Time: 19:44
 */

class ListRepository
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * ListRepository constructor.
     */
    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    /**
     * @param array $availableState
     * @param string $orderBy
     * @return array
     */
    public function getPrograms(array $availableState = [], string $orderBy = 'p.position ASC'): array
    {
        $stateWhere = null;

        if (count($availableState) > 0) {
            $stateWhere = 'WHERE p.status IN( ' . implode(',', $availableState) . ' )';
        }

        $programs = $this->db->query("
          SELECT 
          p.`name`,
          p.`id`,
          p.`mpw`,
          p.`cut`,
          p.`position`,
          cq.sheet_count as quantity,
          cq.parent_synced,
          p.new_cutting_queue_id,
          cq.modified_at,
          (
            SELECT
            COUNT(*)
            FROM
            cutting_queue_list cql
            WHERE
            cql.cutting_queue_id = cq.id
            AND state = 3
          ) as done_programs_quantity,
          (
            SELECT
            COUNT(*)
            FROM
            cutting_queue_list cql
            WHERE
            cql.cutting_queue_id = cq.id
          ) as all_programs_quantity
          FROM `programs` p
          LEFT JOIN cutting_queue cq ON cq.id = p.new_cutting_queue_id
          $stateWhere
          ORDER BY $orderBy
          LIMIT 300
        ");

        return $programs->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $availableState
     * @param string $orderBy
     * @return array
     */
    public function getProgramsWithStatusColor(array $availableState = [], string $orderBy = 'p.position ASC'): array
    {
        if (count($availableState) > 0) {
            $stateWhere = 'WHERE p.status IN( ' . implode(',', $availableState) . ' )';
        }

        $programs = $this->db->query("
          SELECT 
          p.`name`,
          p.`id`,
          p.`mpw`,
          p.`cut`,
          p.`position`,
          cq.sheet_count as quantity,
          cq.parent_synced,
          p.new_cutting_queue_id,
          cq.modified_at,
          (
            SELECT
            SUM(quantity-cutting)
            FROM
            cutting_queue_details cqd
            WHERE
            cqd.cutting_queue_list_id = cq.id
          ) as details_to_cut,
          (
            SELECT
            COUNT(*)
            FROM
            cutting_queue_list cql
            WHERE
            cql.cutting_queue_id = cq.id
            AND state = 3
          ) as done_programs_quantity,
          (
            SELECT
            COUNT(*)
            FROM
            cutting_queue_list cql
            WHERE
            cql.cutting_queue_id = cq.id
            AND state = 5
          ) as canceled_programs_quantity,
          (
            SELECT
            COUNT(*)
            FROM
            cutting_queue_list cql
            WHERE
            cql.cutting_queue_id = cq.id
          ) as all_programs_quantity,
          (
            SELECT
            COUNT(*)
            FROM
            cutting_queue_list cql
            WHERE
            cql.cutting_queue_id = cq.id
            AND state = 7
          ) as correction_quantity
          FROM `programs` p
          LEFT JOIN cutting_queue cq ON cq.id = p.new_cutting_queue_id
          $stateWhere
          ORDER BY $orderBy
          LIMIT 300
        ");

        return $programs->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $program
     */
    public function updatePlateWaste(array $program)
    {
        if ((int)$program['parent_synced'] === 0) {
            //Najpierw detale bo to do nich jest blacha przypisana
            $detailWasteQuery = $this->db->prepare("SELECT id, plate_warehouse_id FROM cutting_queue_details WHERE cutting_queue_list_id = :listId");
            $detailWasteQuery->bindValue(':listId', $program['new_cutting_queue_id'], PDO::PARAM_INT);
            $detailWasteQuery->execute();

            $detailsCount = 0;
            $syncedCount = 0;
            while ($row = $detailWasteQuery->fetch()) {
                $detailsCount++;

            }

            if ($detailsCount === $syncedCount) {
                $queueUpdateQuery = $this->db->prepare("UPDATE cutting_queue SET parent_synced = 1 WHERE id = :queueId");
                $queueUpdateQuery->bindValue(":queueId", $program['new_cutting_queue_id'], PDO::PARAM_INT);
                $queueUpdateQuery->execute();
            }
        }
    }

    /**
     * @param int $programId
     * @return array
     */
    public function getProgramData(int $programId): array
    {
        $query = $this->db->prepare("
          SELECT 
          p.new_cutting_queue_id,
          p.name,
          i.src as image_src
          FROM `programs` p
          LEFT JOIN sheet_image i ON i.program_id = p.id
          WHERE 
          p.id = :programId
        ");
        $query->bindValue(':programId', $programId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch();
    }

    /**
     * @param int $newCuttingQueueId
     * @return array
     */
    public function getMPWData(int $newCuttingQueueId): array
    {
        $mpwQuery = $this->db->prepare('
          SELECT
          cq.id,
          pw.SheetCode,
          pw.MaterialName,
          tm.Thickness,
          tm.MaterialTypeName,
          cq.sheet_name,
          cq.sheet_count,
          qd.LaserMatName,
          pw2.SheetCode as ChildSheetCode,
          i.src as imageSrc,
          i2.src as altImageSrc
          FROM
          cutting_queue_details qd
          LEFT JOIN cutting_queue_list l ON l.id = qd.cutting_queue_list_id
          LEFT JOIN cutting_queue cq ON cq.id = l.cutting_queue_id
          LEFT JOIN plate_warehouse pw ON pw.id = qd.plate_warehouse_id
          LEFT JOIN T_material tm ON tm.MaterialName = pw.MaterialName
          LEFT JOIN plate_warehouse pw2 ON pw2.parentId = pw.id AND pw2.SheetCode LIKE CONCAT("%", cq.sheet_name, "%")
          LEFT JOIN sheet_image i ON i.plate_warehouse_id = pw2.id
          LEFT JOIN sheet_image i2 ON i2.plate_warehouse_id = pw.id
          WHERE
          cq.id = :cuttingQueueId
          LIMIT 1
        ');
        $mpwQuery->bindValue(':cuttingQueueId', $newCuttingQueueId, PDO::PARAM_INT);
        $mpwQuery->execute();

        return $mpwQuery->fetch();
    }

    /**
     * @param int $newCuttingQueueId
     * @return array
     */
    public function getQueueList(int $newCuttingQueueId): array
    {
        $listQuery = $this->db->prepare('
          SELECT
          l.*
          FROM
          cutting_queue_list l
          WHERE
          l.cutting_queue_id = :qid
        ');
        $listQuery->bindValue(':qid', $newCuttingQueueId, PDO::PARAM_INT);
        $listQuery->execute();

        return $listQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $cuttingQueueListId
     * @return array
     */
    public function getDetailsData(int $cuttingQueueListId): array
    {
        $query = $this->db->prepare('
            SELECT
            *,
            d.src as detail_name
            FROM
            cutting_queue_details cd
            LEFT JOIN oitems oi ON oi.id = cd.oitem_id
            LEFT JOIN details d ON d.id = oi.did
            LEFT JOIN details_cutted_report r ON r.cutting_queue_detail_id = cd.id
            WHERE
            cd.cutting_queue_list_id = :id
            GROUP BY cd.id
        ');
        $query->bindParam(':id', $cuttingQueueListId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}