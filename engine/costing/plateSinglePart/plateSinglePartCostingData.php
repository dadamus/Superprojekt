<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 05.07.2017
 * Time: 20:49
 */

class plateSinglePartCostingData
{
    private $details_all_unf;
    private $details_all_unf_per;
    private $details_ext_unf;
    private $details_ext_unf_per;
    private $details_int_unf;
    private $details_int_unf_per;
    private $details_real_unf;
    private $details_real_unf_per;
    private $remnant_unf_per;
    private $remnant_unf;
    private $remnant_unf_value;
    private $details_mat_price;
    private $cut_time;
    private $clean_cut;
    private $cut_komp_n;
    private $cut_detal_n;
    private $price_kom_n;
    private $price_kom_b;
    private $price_det_n;
    private $price_det_b;

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = floatval($value);
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getPriceDetB()
    {
        return $this->price_det_b;
    }

    /**
     * @param $price_det_n
     */
    public function setPriceDetB($price_det_n)
    {
        if ($this->getPriceDetB() == 0) {
            $this->price_det_b = $price_det_n * 1.23;
        }
    }

    /**
     * @return mixed
     */
    public function getPriceDetN()
    {
        return $this->price_det_n;
    }

    /**
     * @param $price_kom_n
     * @param $partCount
     */
    public function setPriceDetN($price_kom_n, $partCount)
    {
        if ($this->getPriceDetN() == 0) {
            $this->price_det_n = $price_kom_n / intval($partCount);
        }
    }

    /**
     * @return mixed
     */
    public function getPriceKomB()
    {
        return $this->price_kom_b;
    }

    /**
     * @param $price_kom_n
     */
    public function setPriceKomB($price_kom_n)
    {
        if ($this->getPriceKomB() == 0) {
            $this->price_kom_b = $price_kom_n * 1.23;
        }
    }

    /**
     * @return mixed
     */
    public function getPriceKomN()
    {
        return $this->price_kom_n;
    }

    /**
     * @param $details_mat_price
     * @param $cut_komp_n
     * @param $checkbox
     */
    public function setPriceKomN($details_mat_price, $cut_komp_n, $checkbox)
    {
        if ($this->getPriceKomN() == 0) {
            $this->price_kom_n = $details_mat_price + $cut_komp_n + $checkbox;
        }
    }

    /**
     * @return mixed
     */
    public function getCutDetalN()
    {
        return $this->cut_detal_n;
    }

    /**
     * @param $cut_komp_n
     * @param $qty
     */
    public function setCutDetalN($cut_komp_n, $qty)
    {
        if ($this->getCutDetalN() == 0) {
            $this->cut_detal_n = $cut_komp_n / intval($qty);
        }
    }

    /**
     * @return mixed
     */
    public function getCutKompN()
    {
        return $this->cut_komp_n;
    }

    /**
     * @param $clean_cut
     * @param $p_factor
     * @param $czas_przelad
     * @param $cena_przeladunku
     */
    public function setCutKompN($clean_cut, $p_factor, $czas_przelad, $cena_przeladunku)
    {
        if ($this->getCutKompN() == 0) {
            $this->cut_komp_n = $clean_cut * $p_factor + ($czas_przelad * $cena_przeladunku);
        }
    }

    /**
     * @return mixed
     */
    public function getCleanCut()
    {
        return $this->clean_cut;
    }

    /**
     * @param $cut_time
     * @param $cutPrice
     */
    public function setCleanCut($cut_time, $cutPrice)
    {
        if ($this->getCleanCut() == 0) {
            $this->clean_cut = $cut_time * $cutPrice;
        }
    }

    /**
     * @return mixed
     */
    public function getCutTime()
    {
        return $this->cut_time;
    }

    /**
     * @param $CutPathTime
     * @param $MoveTime
     * @param $SHCutTime
     * @param $PierceTime
     */
    public function setCutTime($CutPathTime, $MoveTime, $SHCutTime, $PierceTime)
    {
        if ($this->getCutTime() == 0) {
            $this->cut_time = globalTools::calculate_second($CutPathTime) + globalTools::calculate_second($MoveTime) + globalTools::calculate_second($SHCutTime) + globalTools::calculate_second($PierceTime);
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsMatPrice()
    {
        return $this->details_mat_price;
    }

    /**
     * @param $sheet_price_all
     * @param $remnant_unf_value_sum
     */
    public function setDetailsMatPrice($sheet_price_all, $remnant_unf_value)
    {
        if ($this->getDetailsMatPrice() == 0) {
            $this->details_mat_price = (floatval($sheet_price_all) - $remnant_unf_value);
        }
    }

    /**
     * @return mixed
     */
    public function getRemnantUnfValue()
    {
        return $this->remnant_unf_value;
    }

    /**
     * @param $remnant_unf
     * @param $scrapPrice
     * @param $scrapFactor
     */
    public function setRemnantUnfValue($remnant_unf, $scrapPrice, $scrapFactor)
    {
        if ($this->getRemnantUnfValue() == 0) {
            $this->remnant_unf_value = $remnant_unf * floatval($scrapPrice) * floatval($scrapFactor);
        }
    }

    /**
     * @return mixed
     */
    public function getRemnantUnf()
    {
        return $this->remnant_unf;
    }

    /**
     * @param $remnant_unf_per
     * @param $sheet_weight
     */
    public function setRemnantUnf($remnant_unf_per, $sheet_weight)
    {
        if ($this->getRemnantUnf() == 0) {
            $this->remnant_unf = $remnant_unf_per * floatval($sheet_weight) / 100;
        }
    }

    /**
     * @return mixed
     */
    public function getRemnantUnfPer()
    {
        return $this->remnant_unf_per;
    }

    /**
     * @param $details_real_unf_per
     * @param $ramka_per
     */
    public function setRemnantUnfPer($details_real_unf_per, $ramka_per)
    {
        if ($this->getRemnantUnfPer() == 0) {
            $this->remnant_unf_per = 100 - $details_real_unf_per + floatval($ramka_per);
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsRealUnfPer()
    {
        return $this->details_real_unf_per;
    }

    /**
     * @param $details_real_unf
     * @param $sheet_unfold
     */
    public function setDetailsRealUnfPer($details_real_unf, $sheet_unfold)
    {
        if ($this->getDetailsRealUnfPer() == 0) {
            $this->details_real_unf_per = $details_real_unf / floatval($sheet_unfold) * 100;
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsRealUnf()
    {
        return $this->details_real_unf;
    }

    /**
     * @param $AreaWithOutHoles
     * @param $partCount
     */
    public function setDetailsRealUnf($AreaWithOutHoles, $partCount)
    {
        if ($this->getDetailsRealUnf() == 0) {
            $this->details_real_unf = floatval($AreaWithOutHoles) * intval($partCount);
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsIntUnfPer()
    {
        return $this->details_int_unf_per;
    }

    /**
     * @param $details_int_unf
     * @param $sheet_unfold
     */
    public function setDetailsIntUnfPer($details_int_unf, $sheet_unfold)
    {
        if ($this->getDetailsIntUnfPer() == 0) {
            $this->details_int_unf_per = $details_int_unf / floatval($sheet_unfold) * 100;
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsIntUnf()
    {
        return $this->details_int_unf;
    }

    /**
     * @param $AreaWithHoles
     * @param $AreaWithOutHoles
     * @param $PartCount
     */
    public function setDetailsIntUnf($AreaWithHoles, $AreaWithOutHoles, $PartCount)
    {
        if ($this->getDetailsIntUnf() == 0) {
            $this->details_int_unf = (floatval($AreaWithOutHoles) - floatval($AreaWithHoles)) * intval($PartCount);
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsExtUnfPer()
    {
        return $this->details_ext_unf_per;
    }

    /**
     * @param $details_ext_unf
     * @param $sheet_unfold
     */
    public function setDetailsExtUnfPer($details_ext_unf, $sheet_unfold)
    {
        if ($this->getDetailsExtUnfPer() == 0) {
            $this->details_ext_unf_per = $details_ext_unf / floatval($sheet_unfold) * 100;
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsExtUnf()
    {
        return $this->details_ext_unf;
    }

    /**
     * @param $dimensionSizeX
     * @param $dimensionSizeY
     * @param $areaWithHoles
     * @param $partCount
     */
    public function setDetailsExtUnf($dimensionSizeX, $dimensionSizeY, $areaWithHoles, $partCount)
    {
        if ($this->getDetailsExtUnf() == 0) {
            $this->details_ext_unf = ((floatval($dimensionSizeX) * floatval($dimensionSizeY)) - floatval($areaWithHoles)) * intval($partCount);
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsAllUnfPer()
    {
        return $this->details_all_unf_per;
    }

    /**
     * @param $allUnf
     * @param $sheetUnfold
     */
    public function setDetailsAllUnfPer($allUnf, $sheetUnfold)
    {
        if ($this->getDetailsAllUnfPer() == 0) {
            $this->details_all_unf_per = floatval($allUnf) / floatval($sheetUnfold) * 100;
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsAllUnf()
    {
        return $this->details_all_unf;
    }

    /**
     * @param $partCount
     * @param $dimensionSizeX
     * @param $dimensionSizeY
     */
    public function setDetailsAllUnf($partCount, $dimensionSizeX, $dimensionSizeY)
    {
        if ($this->getDetailsAllUnf() == 0) {
            $this->details_all_unf = intval($partCount) * floatval($dimensionSizeX) * floatval($dimensionSizeY);
        }
    }
}