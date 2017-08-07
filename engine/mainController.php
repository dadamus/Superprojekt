<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 10.07.2017
 * Time: 17:44
 */
class mainController
{
    /**
    * @var string
    */
    private $viewPath;

    /**
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    /**
     * @param string $viewPath
     */
    public function setViewPath(string $viewPath)
    {
        $lastChar = substr($viewPath, -1);
        $path = $viewPath;
        if ($lastChar !== "/") {
            $path .= "/";
        }

        $this->viewPath = $path;
    }

    /**
     * @param $path
     * @return string
     * @throws Exception
     */
    public function render($path, $data = [])
    {
        try {
            ob_start();
            include $this->viewPath . $path;
            return ob_get_clean();
        } catch(\Exception $ex) {
            throw new \Exception("Brak pliku $path !");
        }
    }
}