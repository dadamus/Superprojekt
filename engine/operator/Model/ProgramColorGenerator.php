<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 2018-12-05
 * Time: 20:22
 */

/**
 * Class ProgramColorGenerator
 */
class ProgramColorGenerator
{
    /**
     * @param array $program
     * @return string
     */
    public static function generate(array $program): string
    {
        if ($program['correction_quantity'] > 0) {
            return '#8caeff';
        }

        if ($program['all_programs_quantity'] === $program['canceled_programs_quantity']) {
            return '#d05454';
        }

        if ($program['done_programs_quantity'] > 0 && $program['canceled_programs_quantity']) {
            return '#F4D03F';
        }

        if ($program['details_to_cut'] <= 0) {
            return '#1BBC9B';
        }



        return '';
    }
}