<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 07.09.2017
 * Time: 22:16
 */

/**
 * Class PlateSyncController
 */
class PlateSyncController
{
    /**
     * @param array $programs
     */
    public function syncAction(array $programs)
    {
        $detailsNameString = "";

        return print_r($programs);

        foreach ($programs as $program) {
            $sheetName = $program["SheetName"];
            $details = $program["Details"];

            /*
             * Detal
             * public string PartName { get; set; }
        public int Quantity { get; set; }
             *
             */
        }
    }
}