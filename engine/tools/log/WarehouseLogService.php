<?php

class WarehouseLogService
{
    const SYSTEM = 0;

    const BADGE_ABL = 1;
    const BADGE_ABE = 2;
    const BADGE_EDIT = 3;

    const LOG_TABLE = 'plate_warehouse_log';

    CONST NEW_ROW_TYPE = 1;
    CONST QUANTITY_CHANGED_TYPE = 2;
    CONST EXTERNAL_DISPATCH_TYPE = 3;
    CONST INTERNAL_DISPATCH_TYPE = 4;
    CONST POSITIVE_CORRECTION_TYPE = 5;
    CONST NEGATIVE_CORRECTION_TYPE = 6;
    CONST LOSS_TYPE = 7;
    CONST SCRAPPING_TYPE = 8;
    CONST WEIGHT_TYPE = 9;
    CONST TRASH_TYPE = 10;
    CONST FROM_TRASH_TYPE = 10;

    public static function newRow(string $sheetCode, int $quantity, int $user = self::SYSTEM): void
    {
        $text = 'Dodanie nowego wpisu -> ' . $quantity . ' szt';

        self::insertLog(
            $sheetCode,
            self::NEW_ROW_TYPE,
            $text,
            self::BADGE_ABL,
            $user
        );
    }

    public static function quantityChanged(string $sheetCode, int $oldQuantity, int $newQuantity): void
    {
        $text = 'Zmieniono stan: ' . $oldQuantity . ' szt -> ' . $newQuantity . ' szt';

        self::insertLog(
            $sheetCode,
            self::QUANTITY_CHANGED_TYPE,
            $text,
            self::BADGE_ABE,
            self::SYSTEM
        );
    }

    public static function externalDispatch(string $sheetCode, int $dispatchQuantity, int $plateQuantity, int $userId): void
    {
        $text = "Wydanie zewnętrzne $dispatchQuantity szt. Aktualnie $plateQuantity szt";

        self::insertLog(
            $sheetCode,
            self::EXTERNAL_DISPATCH_TYPE,
            $text,
            self::BADGE_EDIT,
            $userId
        );
    }

    public static function internalDispatch(string $sheetCode, int $dispatchQuantity, int $plateQuantity, int $userId): void
    {
        $text = "Wydanie wewnętrzne $dispatchQuantity szt. Aktualnie $plateQuantity szt";

        self::insertLog(
            $sheetCode,
            self::INTERNAL_DISPATCH_TYPE,
            $text,
            self::BADGE_EDIT,
            $userId
        );
    }

    public static function positiveCorrection(string $sheetCode, int $correctionValue, int $plateQuantity, int $userId): void
    {
        $text = "Korekta dodająca $correctionValue szt. Aktualnie $plateQuantity szt";

        self::insertLog(
            $sheetCode,
            self::POSITIVE_CORRECTION_TYPE,
            $text,
            self::BADGE_EDIT,
            $userId
        );
    }

    public static function negativeCorrection(string $sheetCode, int $correctionValue, int $plateQuantity, int $userId): void
    {
        $text = "Korekta odejmująca $correctionValue szt. Aktualnie $plateQuantity szt";

        self::insertLog(
            $sheetCode,
            self::NEGATIVE_CORRECTION_TYPE,
            $text,
            self::BADGE_EDIT,
            $userId
        );
    }

    public static function loss(string $sheetCode, int $lossValue, int $plateQuantity, int $userId): void
    {
        $text = "Zagubienie $lossValue szt. Aktualnie $plateQuantity szt";

        self::insertLog(
            $sheetCode,
            self::LOSS_TYPE,
            $text,
            self::BADGE_EDIT,
            $userId
        );
    }

    public static function scrapping(string $sheetCode, int $lossValue, int $plateQuantity, int $userId): void
    {
        $text = "Zezłomowanie $lossValue szt. Aktualnie $plateQuantity szt";

        self::insertLog(
            $sheetCode,
            self::SCRAPPING_TYPE,
            $text,
            self::BADGE_EDIT,
            $userId
        );
    }

    public static function changeWeight(string $sheetCode, string $programName, string $programId, float $weight): void
    {
        $text = "Edycja wagi -> $weight kg. Program $programName";

        self::insertLog(
            $sheetCode,
            self::WEIGHT_TYPE,
            $text,
            self::BADGE_ABL,
            self::SYSTEM
        );
    }

    public static function trash(string $sheetCode): void
    {
        $text = "Przenesiono do kosza";

        self::insertLog(
            $sheetCode,
            self::TRASH_TYPE,
            $text,
            self::BADGE_ABL,
            self::SYSTEM
        );
    }

    public static function fromTrash(string $sheetCode): void
    {
        $text = "Przenesiono z kosza";

        self::insertLog(
            $sheetCode,
            self::FROM_TRASH_TYPE,
            $text,
            self::BADGE_ABL,
            self::SYSTEM
        );
    }

    private static function insertLog(string $sheetCode, int $type, string $text, int $badge, int $user): void
    {
        $insert = sqlBuilder::createInsert(self::LOG_TABLE);
        $insert
            ->bindValue('sheetcode', $sheetCode, PDO::PARAM_STR)
            ->bindValue('type', $type, PDO::PARAM_INT)
            ->bindValue('text', $text, PDO::PARAM_STR)
            ->bindValue('date', date('Y-m-d H:i:s'), PDO::PARAM_STR)
            ->bindValue('user', $user, PDO::PARAM_INT)
            ->bindValue('system', $badge, PDO::PARAM_INT)
            ->flush();
    }
}