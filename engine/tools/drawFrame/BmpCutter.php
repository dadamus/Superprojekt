<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 31.05.2017
 * Time: 23:19
 */
class bmpCutter
{
    private $image;
    private $position;
    private $scale = 1;

    private $maxWidth;
    private $realWidth;

    /**
     * bmpCutter constructor.
     * @param $filename
     * @param int $maxWidth
     */
    public function __construct($filename, $maxWidth = 800)
    {
        $this->imagecreatefrombmp($filename);
        $this->getCutData();
        $this->crop();
        $this->setMaxWidth($maxWidth);
        $this->scaleImg();
    }

    /**
     * @return mixed
     */
    public function getRealWidth()
    {
        return $this->realWidth;
    }

    /**
     * @param mixed $realWidth
     */
    public function setRealWidth($realWidth)
    {
        $this->realWidth = $realWidth;
    }

    /**
     * @return mixed
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * @param mixed $maxWidth
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = $maxWidth;
    }


    /**
     * @param $filename
     * @return bool|resource
     */
    public function imagecreatefrombmp($filename)
    {
        // version 1.1
        if (!($fh = fopen($filename, 'rb'))) {
            trigger_error('imagecreatefrombmp: Can not open ' . $filename, E_USER_WARNING);
            return false;
        }

        // read file header
        $meta = unpack('vtype/Vfilesize/Vreserved/Voffset', fread($fh, 14));

        // check for bitmap
        if ($meta['type'] != 19778) {
            trigger_error('imagecreatefrombmp: ' . $filename . ' is not a bitmap!', E_USER_WARNING);
            return false;
        }

        // read image header
        $meta += unpack('Vheadersize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vcolors/Vimportant', fread($fh, 40));
        $bytes_read = 40;

        // read additional bitfield header
        if ($meta['compression'] == 3) {
            $meta += unpack('VrMask/VgMask/VbMask', fread($fh, 12));
            $bytes_read += 12;
        }

        // set bytes and padding
        $meta['bytes'] = $meta['bits'] / 8;
        $meta['decal'] = 4 - (4 * (($meta['width'] * $meta['bytes'] / 4) - floor($meta['width'] * $meta['bytes'] / 4)));
        if ($meta['decal'] == 4) {
            $meta['decal'] = 0;
        }

        // obtain imagesize
        if ($meta['imagesize'] < 1) {
            $meta['imagesize'] = $meta['filesize'] - $meta['offset'];
            // in rare cases filesize is equal to offset so we need to read physical size
            if ($meta['imagesize'] < 1) {
                $meta['imagesize'] = @filesize($filename) - $meta['offset'];
                if ($meta['imagesize'] < 1) {
                    trigger_error('imagecreatefrombmp: Can not obtain filesize of ' . $filename . '!', E_USER_WARNING);
                    return false;
                }
            }
        }

        // calculate colors
        $meta['colors'] = !$meta['colors'] ? pow(2, $meta['bits']) : $meta['colors'];

        // read color palette
        $palette = array();
        if ($meta['bits'] < 16) {
            $palette = unpack('l' . $meta['colors'], fread($fh, $meta['colors'] * 4));
            // in rare cases the color value is signed
            if ($palette[1] < 0) {
                foreach ($palette as $i => $color) {
                    $palette[$i] = $color + 16777216;
                }
            }
        }

        // ignore extra bitmap headers
        if ($meta['headersize'] > $bytes_read) {
            fread($fh, $meta['headersize'] - $bytes_read);
        }

        // create gd image
        $im = imagecreatetruecolor($meta['width'], $meta['height']);
        $data = fread($fh, $meta['imagesize']);

        // uncompress data
        switch ($meta['compression']) {
            case 1:
                $data = rle8_decode($data, $meta['width']);
                break;
            case 2:
                $data = rle4_decode($data, $meta['width']);
                break;
        }

        $p = 0;
        $vide = chr(0);
        $y = $meta['height'] - 1;
        $error = 'imagecreatefrombmp: ' . $filename . ' has not enough data!';

        // loop through the image data beginning with the lower left corner
        while ($y >= 0) {
            $x = 0;
            while ($x < $meta['width']) {
                switch ($meta['bits']) {
                    case 32:
                    case 24:
                        if (!($part = substr($data, $p, 3 /* $meta['bytes'] */))) {
                            trigger_error($error, E_USER_WARNING);
                            return $im;
                        }
                        $color = unpack('V', $part . $vide);
                        break;
                    case 16:
                        if (!($part = substr($data, $p, 2 /* $meta['bytes'] */))) {
                            trigger_error($error, E_USER_WARNING);
                            return $im;
                        }
                        $color = unpack('v', $part);

                        if (empty($meta['rMask']) || $meta['rMask'] != 0xf800) {
                            $color[1] = (($color[1] & 0x7c00) >> 7) * 65536 + (($color[1] & 0x03e0) >> 2) * 256 + (($color[1] & 0x001f) << 3); // 555
                        } else {
                            $color[1] = (($color[1] & 0xf800) >> 8) * 65536 + (($color[1] & 0x07e0) >> 3) * 256 + (($color[1] & 0x001f) << 3); // 565
                        }
                        break;
                    case 8:
                        $color = unpack('n', $vide . substr($data, $p, 1));
                        $color[1] = $palette[$color[1] + 1];
                        break;
                    case 4:
                        $color = unpack('n', $vide . substr($data, floor($p), 1));
                        $color[1] = ($p * 2) % 2 == 0 ? $color[1] >> 4 : $color[1] & 0x0F;
                        $color[1] = $palette[$color[1] + 1];
                        break;
                    case 1:
                        $color = unpack('n', $vide . substr($data, floor($p), 1));
                        switch (($p * 8) % 8) {
                            case 0:
                                $color[1] = $color[1] >> 7;
                                break;
                            case 1:
                                $color[1] = ($color[1] & 0x40) >> 6;
                                break;
                            case 2:
                                $color[1] = ($color[1] & 0x20) >> 5;
                                break;
                            case 3:
                                $color[1] = ($color[1] & 0x10) >> 4;
                                break;
                            case 4:
                                $color[1] = ($color[1] & 0x8) >> 3;
                                break;
                            case 5:
                                $color[1] = ($color[1] & 0x4) >> 2;
                                break;
                            case 6:
                                $color[1] = ($color[1] & 0x2) >> 1;
                                break;
                            case 7:
                                $color[1] = ($color[1] & 0x1);
                                break;
                        }
                        $color[1] = $palette[$color[1] + 1];
                        break;
                    default:
                        trigger_error('imagecreatefrombmp: ' . $filename . ' has ' . $meta['bits'] . ' bits and this is not supported!', E_USER_WARNING);
                        return false;
                }
                imagesetpixel($im, $x, $y, $color[1]);
                $x++;
                $p += $meta['bytes'];
            }
            $y--;
            $p += $meta['decal'];
        }
        fclose($fh);
        $this->image = $im;
    }

    public function getCutData()
    {

        $xpos = round(imagesx($this->image) / 2);
        $ypos = round(imagesy($this->image) / 2);

        $firstcolor = imagecolorat($this->image, 0, $ypos);
        $tempcolor = null;
        $tempcolory = null;


        $tempposy = null;
        $position = [];

        for ($x = 1; $x < imagesx($this->image); $x++) {
            $color = imagecolorat($this->image, $x, $ypos);
            if ($firstcolor != $color) {
                if ($tempcolor == $color || $tempcolor == null) {

                    for ($y = 1; $y < imagesy($this->image); $y++) {
                        $colory = imagecolorat($this->image, $xpos, $y);
                        if ($firstcolor != $colory) {
                            if (($tempcolory == $colory || $tempcolory == null) && $tempposy != $y) {
                                $position[] = [
                                    "x" => $x,
                                    "y" => $y
                                ];
                                $tempposy = $y;
                                $tempcolory = $colory;

                                if (count($position) == 1) {
                                    break;
                                }
                            }
                        }
                    }
                    $tempcolor = $color;

                    if (count($position) == 2) {
                        break;
                    }
                }
            }
        }

        if (count($position) === 1) {
            $position[1] = [
                "x" => imagesx($this->image),
                "y" => imagesy($this->image)
            ];
        }

        $this->position = [
            "x" => $position[0]["x"],
            "y" => $position[0]["y"],
            "width" => $position[1]["x"] - $position[0]["x"] + 1,
            "height" => $position[1]["y"] - $position[0]["y"] + 1
        ];
    }

    public function scaleImg()
    {
        $position = $this->getPosition();

        $width = $position["width"];
        $height = $position["height"];

        $this->setRealWidth($width);

        $maxWidth = 800;
        if ($this->getMaxWidth() > 0) {
            $maxWidth = $this->getMaxWidth();
        }

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $aspect = $width / $newWidth;

            $newHeight = $height / $aspect;

            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled(
                $newImage,
                $this->image,
                0,
                0,
                0,
                0,
                $newWidth,
                $newHeight,
                $width,
                $height
            );
            $this->image = $newImage;
            $this->scale = $newWidth / $width;

            $this->position = [
                "x" => $position["x"],
                "y" => $position["y"],
                "width" => $newWidth,
                "height" => $newHeight
            ];
        }
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function crop()
    {

        $newimg = imagecrop($this->image, $this->position);
        $this->image = $newimg;
    }

    public function getScale()
    {
        return ( $this->scale != 0 ? $this->scale : 1);
    }

    /**
     * @return string
     */
    public function getBase64()
    {
        ob_start();
        imagepng($this->image);

        $b64 = base64_encode(ob_get_contents());
        ob_end_clean();
        return $b64;
    }
}