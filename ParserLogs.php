<?php


namespace Logs;

class ParserLogs
{
    /**
     * @type array|string
     */
    private $fileNames;
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
     * @type array|string
     */
    private $urls = [];

    public function __construct(array $filesNames)
    {
        try {
            $this->fileNames($filesNames);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * @param array|string $fileNames
     * @throws \Exception
     */
    public function fileNames($fileNames)
    {
        foreach ($fileNames as $fileName) {
            if (!file_exists($fileName)) {
                throw new \Exception("Wrong name of file: $fileName\n");
            }
        }
        $this->fileNames = $fileNames;
    }

    /**
     * @param string $fileName
     * @throws \Exception
     * @return string
     */
    public function getJson($fileName)
    {
        $fd = fopen($fileName, 'r');
        if ($fd) {
            while (($logLine = fgets($fd)) !== false) {
                try {
                    $this->parseLogLine($logLine);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    die;
                }

                $this->result["views"] += 1;
            }
            $this->result["urls"] = count($this->urls);
            var_dump(json_encode($this->result));
            fclose($fd);
        } else {
            throw new \Exception("Impossible to read file: $fileName\n");
        }
    }


    /**
     * @param string $logLine
     * @throws \Exception
     */
    private function parseLogLine($logLine)
    {
        $pattern = "!.*\"(.+)\" (\d{3}) (\d+) \"(.+)\" \"(.+)\"!";
        if (preg_match_all($pattern, $logLine, $allLogParams, PREG_SET_ORDER)) {
            if (count($allLogParams[0]) != 6) {
                throw new \Exception("Invalid number of log parameters in line {$this->result["views"]}\n");
            }
        }
        $logParams = array_slice($allLogParams[0], 1);

        $query = $logParams[0];
        $queryParams = explode(" ", $query);

        if (count($queryParams) != 3) {
            throw new \Exception("Invalid number of query parameters in line {$this->result["views"]}\n");
        }

        $queryType = $queryParams[0];
        if (!in_array($queryType, ["POST", "GET"])) {
            throw new \Exception("Invalid query type in line {$this->result["views"]}\n");
        }

        $url = $queryParams[1];
        if (!in_array($url, $this->urls))
            $this->urls[] = $url;

        $statusCode = $logParams[1];
        if (!is_numeric($statusCode) && strlen($statusCode) === 3) {
            throw new \Exception("Server's status code is incorrect in line {$this->result["views"]}\n");
        }

        if (key_exists($statusCode, $this->result["statusCodes"])) {
            $this->result["statusCodes"][$statusCode] += 1;
        } else {
            $this->result["statusCodes"][$statusCode] = 1;
        }

        $traffic = $logParams[2];
        if (!is_numeric($traffic)) {
            throw new \Exception("Traffic value is not a number in line {$this->result["views"]}\n");
        }

        if ($queryType === "POST")
            $this->result["traffic"] += $traffic;

        $browserInfo = $logParams[4];
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