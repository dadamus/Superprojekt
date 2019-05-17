<?php

class WarehouseLogService
{
    public const SYSTEM = 0;

    public const BADGE_ABL = 1;
    public const BADGE_ABE = 2;
    public const BADGE_EDIT = 3;

    private const LOG_TABLE = 'plate_warehouse_log';

    public CONST NEW_ROW_TYPE = 1;
    public CONST QUANTITY_CHANGED_TYPE = 2;
    public CONST EXTERNAL_DISPATCH_TYPE = 3;
    public CONST INTERNAL_DISPATCH_TYPE = 4;
    public CONST POSITIVE_CORRECTION_TYPE = 5;
    public CONST NEGATIVE_CORRECTION_TYPE = 6;
    public CONST LOSS_TYPE = 7;
    public CONST SCRAPPING_TYPE = 8;

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