<?php

namespace Utils;

class ParserArgs
{
    /**
     * @type string
     */
    private $fileNameToSave = '';

    /**
     * @type bool
     */
    private $isPrint = true;

    /**
     * @type string|null
     */
    private $fileNameLogs = '';

    /**
     * @type string
     */
    private $helpMessage = '';

    /**
     * @type bool
     */
    private $isHelp = true;

    /**
     * @type array[string]
     */
    private $options;

    /**
     * ParserArgs constructor.
     * @type array[string] $args
     * @throws \Exception
     */
    public function __construct()
    {
        $options = $this->createOptions();
        $this->options = getopt($options);

        $this->setFileNameToSave();
        $this->setIsPrint();
        $this->setHelpMessageAndIsHelp();
        try {
            $this->setFileNameLogs();
        } catch (\Exception $e) {
            if (!$this->isHelp) {
                die($this->helpMessage);
            }
            throw $e;
        }
    }

    private function createOptions()
    {
        $options = "f:";
        $options .= "h::";
        $options .= "s:";
        $options .= "p::";
        return $options;
    }

    /**
     * @throws \Exception
     */
    private function setFileNameLogs()
    {

        if (!key_exists("f", $this->options)) {
            throw new \Exception("Flag -f is not provided\n");
        }
        if (!file_exists($this->options["f"])) {
            throw new \Exception("Invalid path of file in flag -f\n");
        }

        $this->fileNameLogs = $this->options["f"];
    }

    private function setFileNameToSave()
    {
        if (key_exists("s", $this->options)) {
            $this->fileNameToSave = $this->options["s"];
        }
    }

    private function setIsPrint()
    {
        if (key_exists("p", $this->options)) {
            $this->isPrint = $this->options["p"];
        }
    }

    private function setHelpMessageAndIsHelp()
    {
        if (key_exists("h", $this->options)) {
            $this->isHelp = $this->options["h"];
            $this->helpMessage =
"Usage: php parser -f FILE_OF_LOGS [-s FILE_TO_SAVE -p]
   
Required flags:

-f FILE_OF_LOGS - log's path file to parse

Optional flags:

-s FILE_TO_SAVE - path of file to save
-p - print result to terminal\n";
        }
    }

    /**
     * @return string
     */
    public function fileNameToSave()
    {
        return $this->fileNameToSave;
    }

    /**
     * @return bool
     */
    public function isPrint()
    {
        return $this->isPrint;
    }

    /**
     * @return string
     */
    public function fileNameLogs()
    {
        return $this->fileNameLogs;
    }

    /**
     * @return string
     */
    public function helpMessage()
    {
        return $this->helpMessage;
    }

    /**
     * @return bool
     */
    public function isHelp()
    {
        return $this->isHelp;
    }
}
