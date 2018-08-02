<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 29.04.2017
 * Time: 17:39
 */
class TollsEngineController
{
    /**
     * @var array $tool_list
     */
    protected $tool_list;

    /**
     * @param string $type
     * @param string $toolName
     */
    public function addTool(string $type, string $toolName= null)
    {
        if ($toolName == null) {
            $path = implode("/", [
                dirname(__DIR__),
                $type
            ]);

            $files = scandir($path, 1);
            foreach ($files as $fileName)
            {
                $file = explode(".", $fileName);
                if (count($file) <= 1) {
                    continue;
                }

                if ($file[count($file) - 1] != "php") {
                    continue;
                }

                $this->tool_list[$type][] = str_replace(".php", "", $fileName);
            }
        } else {
            $this->tool_list[$type][] = $toolName;
        }
    }

    public function initialize()
    {
        foreach ($this->tool_list as $dir => $dirTools)
        {
            foreach ($dirTools as $tool)
            {
                $path = implode("/", [
                    dirname(__DIR__),
                    $dir,
                    $tool . ".php"
                ]);
                require $path;
            }
        }
    }
}