<?php


namespace Logs;

class ParserLogs
{
    /**
     * @type string
     */
    private $fileName;

    /**
     * @type array
     */
    private $result = [
        "views" => 0,
        "urls" => 0,
        "traffic" => 0,
        "crawlers" => [
            "Google" => 0,
            "Bing" => 0,
            "Baidu" => 0,
            "Yandex" => 0
        ],
        "statusCodes" => [],
    ];

    /**
     * @type array[string]
     */
    private $urls = [];

    /**
     * @type string
     */
    private $queryType;

    /**
     * @type int
     */
    private $currentLine;

    public function __construct($filesName)
    {
        try {
            $this->setFileName($filesName);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * @param array[string] $fileNames
     * @throws \Exception
     */
    public function setFileName($fileName)
    {
        if (!file_exists($fileName)) {
            throw new \Exception("Wrong name of file: $fileName\n");
        }
        $this->fileName = $fileName;
    }

    /**
     * @throws \Exception
     * @return string
     */
    public function getJson()
    {
        $fd = fopen($this->fileName, 'r');
        if ($fd) {
            // don't know how to read file in another way
            while (($logLine = fgets($fd)) !== false) {
                try {
                    $this->parseLogLine($logLine);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    continue;
                }

                $this->result["views"] += 1;
            }
            fclose($fd);
        } else {
            throw new \Exception("Impossible to read file: $this->fileName\n");
        }

        $this->result["urls"] = count($this->urls);
        $jsonResult = json_encode($this->result, JSON_PRETTY_PRINT);

        return $jsonResult;
    }


    /**
     * @param string $logLine
     * @throws \Exception
     */
    private function parseLogLine($logLine)
    {
        $pattern = "!.*\"(.+)\" (\d+) (\d+) \"(.+)\" \"(.+)\"!";

        $this->currentLine = $this->result["views"] + 1;
        if (!preg_match_all($pattern, $logLine, $allLogParams, PREG_SET_ORDER)) {
            if (count($allLogParams[0]) != 6) {
                throw new \Exception("Invalid number of log parameters in line {$this->currentLine}\n");
            }
        }
        $logParams = array_slice($allLogParams[0], 1);

        try {
            $this->saveUrl($logParams, 0);
            $this->saveStatusCode($logParams, 1);
            $this->saveTraffic($logParams, 2);
            $this->saveCrawler($logParams, 4);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param array[string] $logParams
     * @param int $indx
     * @throws \Exception
     */
    private function saveUrl($logParams, $indx)
    {
        $query = $logParams[$indx];
        $queryParams = explode(" ", $query);

        if (count($queryParams) != 3) {
            throw new \Exception("Invalid number of query parameters in line {$this->currentLine}\n");
        }

        $this->queryType = $queryParams[0];
        if (!in_array($this->queryType, ["POST", "GET"])) {
            throw new \Exception("Invalid query type in line {$this->currentLine}\n");
        }

        $url = $queryParams[1];
        if (!in_array($url, $this->urls))
            $this->urls[] = $url;
    }

    /**
     * @param array[string] $logParams
     * @param int $indx
     * @throws \Exception
     */
    private function saveStatusCode($logParams, $indx)
    {
        $statusCode = $logParams[$indx];
        if (!is_numeric($statusCode) || strlen($statusCode) !== 3) {
            throw new \Exception("Server's status code is incorrect in line {$this->currentLine}\n");
        }

        if (key_exists($statusCode, $this->result["statusCodes"])) {
            $this->result["statusCodes"][$statusCode] += 1;
        } else {
            $this->result["statusCodes"][$statusCode] = 1;
        }
    }

    /**
     * @param array[string] $logParams
     * @param int $indx
     * @throws \Exception
     */
    private function saveTraffic($logParams, $indx)
    {
        $traffic = $logParams[$indx];
        if (!is_numeric($traffic)) {
            throw new \Exception("Traffic value is not a number in line {$this->currentLine}\n");
        }

        if ($this->queryType === "POST")
            $this->result["traffic"] += $traffic;
    }

    /**
     * @param array[string] $logParams
     * @param int $indx
     * @throws \Exception
     */
    private function saveCrawler($logParams, $indx)
    {
        $browserInfo = $logParams[$indx];
        $browserParams = explode(" ", $browserInfo);

        $crawler = strtolower($browserParams[5]);
        if (strpos($crawler, 'google') === 0) {
            $this->result["crawlers"]["Google"] += 1;
        }
        else if (strpos($crawler, 'bing') === 0) {
            $this->result["crawlers"]['Bing'] += 1;
        }
        else if (strpos($crawler, 'baidu') === 0) {
            $this->result["crawlers"]['Baidu'] += 1;
        }
        else if (strpos($crawler, 'yandex') === 0) {
            $this->result["crawlers"]['Yandex'] += 1;
        }
    }
}