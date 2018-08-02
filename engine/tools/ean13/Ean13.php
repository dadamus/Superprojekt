<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 20.04.2017
 * Time: 21:07
 */
class Ean13
{
    private $kol;
    private $code;
    private $value;

    private $i;
    private $w;
    private $b;

    private function gen_binary($kod, $strona, $sys)
    {
        $kod = str_split($kod);
        $ret = '';
        if ($strona == 0) {
            foreach ($kod as $key => $val) {
                $ret .= $this->code['lewa'][$this->kol[$sys][$key]][$val];
            }
        } else {
            foreach ($kod as $val) {
                $ret .= $this->code['prawa'][$val];
            }
        }
        return $ret;
    }

    private function print_code($kod, $img)
    {
        $now = 0;
        $kod = str_split($kod);
        foreach ($kod as $val) {
            if ($val == 1) {
                imageline($img, $now, 0, $now, 80, $this->b);
                imageline($img, $now+1, 0, $now+1, 80, $this->b);
                $now+=2;
            } elseif ($val == 0) {
                $now+=2;
            }
        }
    }

    public function __construct($code)
    {
        $len = strlen($code);
        if(trim($code, '0123456789')!='' OR ($len!=12 AND $len!=13)) {
            throw new Exception('Znaki inne niż cyfry lub błędna długość ('.$len.')');
        }

        $kod = str_split(substr($code, 0, 12));
        $now = 1;
        $sum = 0;
        foreach($kod as $val) {
            if($now==1) {
                $sum += $val;
                $now = 3;
            }
            else
            {
                $sum += $val*3;
                $now = 1;
            }
        }
        $sum = 10-($sum%10);
        if($sum==10) $sum = 0;

        if($len==12) {
            $code .= $sum;
        }
        elseif(substr($code, -1)!=$sum) {
            throw new Exception('Błędna suma kontrolna '.$sum);
        }
        $this->kol = array(
            '0' => array('A', 'A', 'A', 'A', 'A', 'A'),
            '1' => array('A', 'A', 'B', 'A', 'B', 'B'),
            '2' => array('A', 'A', 'B', 'B', 'A', 'B'),
            '3' => array('A', 'A', 'B', 'B', 'B', 'A'),
            '4' => array('A', 'B', 'A', 'A', 'B', 'B'),
            '5' => array('A', 'B', 'B', 'A', 'A', 'B'),
            '6' => array('A', 'B', 'B', 'B', 'A', 'A'),
            '7' => array('A', 'B', 'A', 'B', 'A', 'B'),
            '8' => array('A', 'B', 'A', 'B', 'B', 'A'),
            '9' => array('A', 'B', 'B', 'A', 'B', 'A')
        );
        $this->code = array(
            'start' => '101',
            'lewa' => array(
                'A' => array(
                    '0' => '0001101',
                    '1' => '0011001',
                    '2' => '0010011',
                    '3' => '0111101',
                    '4' => '0100011',
                    '5' => '0110001',
                    '6' => '0101111',
                    '7' => '0111011',
                    '8' => '0110111',
                    '9' => '0001011'
                ),
                'B' => array(
                    '0' => '0100111',
                    '1' => '0110011',
                    '2' => '0011011',
                    '3' => '0100001',
                    '4' => '0011101',
                    '5' => '0111001',
                    '6' => '0000101',
                    '7' => '0010001',
                    '8' => '0001001',
                    '9' => '0010111'
                )
            ),
            'srodek' => '01010',
            'prawa' => array(
                '0' => '1110010',
                '1' => '1100110',
                '2' => '1101100',
                '3' => '1000010',
                '4' => '1011100',
                '5' => '1001110',
                '6' => '1010000',
                '7' => '1000100',
                '8' => '1001000',
                '9' => '1110100'
            ),
            'stop' => '101'
        );

        $this->value = $code;
        $this->i = imagecreate(190, 80);
        $this->w = imagecolorallocate($this->i, 255, 255, 255);
        $this->b = imagecolorallocate($this->i, 0, 0, 0);
    }

    public function drawCode()
    {
        $sys = substr($this->value, 0, 1);
        $lewa = substr($this->value, 1, 6);
        $prawa = substr($this->value, 7);

        $this->print_code($this->code['start'] . $this->gen_binary($lewa, 0, $sys) . $this->code['srodek'] . $this->gen_binary($prawa, 1, $sys) . $this->code['stop'], $this->i);

        header('Content-type: image/gif');
        imagegif($this->i);
    }
}