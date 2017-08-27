<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 25.08.2017
 * Time: 11:09
 */

require_once dirname(__DIR__) . "/../plateMultiPart.php";

class detailCardModel
{
    /** @var  PlateMultiPart */
    private $plateMultiPart;

    /**
     * detailCardModel constructor.
     * @param PlateMultiPart $plateMultiPart
     */
    public function __construct(PlateMultiPart $plateMultiPart)
    {
        $this->plateMultiPart = $plateMultiPart;
    }

    /**
     * @return PlateMultiPart
     */
    public function getPlateMultiPart(): PlateMultiPart
    {
        return $this->plateMultiPart;
    }

    public function make()
    {

    }
}