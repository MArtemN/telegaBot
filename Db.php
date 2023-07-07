<?php

class Db
{
    protected $host = 'mysqldb';
    protected $userName = 'root';
    protected $password = 'root';
    protected $dbName = 'foodPlanBd';

    protected function getConnect()
    {
        try {
            // подключаемся к серверу
            $conn = new PDO('mysql:host=' . $this->host . ';charset=utf8;dbname=' . $this->dbName, $this->userName, $this->password);
            echo "Database connection established";
            return $conn;
        }
        catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return 'Ошибка подключения ' . $e->getMessage();
        }
    }

    public function addFood($foodName, $proteinCount, $userName, $messageId)
    {
        $conn = $this->getConnect();

        if (!is_numeric($proteinCount)) {
            return 'После запятой должно быть число';
        }

        $currentDate = date('Y-m-d H:i:s');
        $sql = "INSERT INTO `food` (`user_name`, `food_name`, `protein`, `date`, `message_id`) VALUES ('$userName', '$foodName', '$proteinCount', '$currentDate', '$messageId')";
        $affectedRowsNumber = $conn->exec($sql);

        if($affectedRowsNumber > 0 ){
            return 'Данные успешно записаны';
        } else {
            return 'Данные не записались';
        }
    }

    public function getReport($period)
    {
        $conn = $this->getConnect();
        $sql = '';
        $resultArr = [];
        $response = '';

        if ($period == 1) {
            $sql = "SELECT `food_name`, `protein`, `date` FROM `food` WHERE date = CURDATE()";
        } else if ($period == 7) {
            $sql = "SELECT `food_name`, `protein`, `date` FROM food WHERE date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        } else if ($period == 30) {
            $sql = "SELECT `food_name`, `protein`, `date` FROM food WHERE MONTH(date) = MONTH(CURDATE())";
        }

        $result = $conn->query($sql);

        if (!$result) {
            return 'Ошибка получения попробуйте еще';
        }
        while($row = $result->fetch()){
            $resultArr[$row['date']][] = $row;
        }

        foreach ($resultArr as $date => $itemArr) {
            $response .= $date;
            $proteinCount = 0;
            foreach ($itemArr as $item) {
                if ($date == $item['date']) {
                    $proteinCount += $item['protein'];
                }
                $response .= "\n<b>Блюдо:</b> " .$item['food_name']. "\n<b>Количество белка:</b> " .$item['protein']. "\n";
            }
            $response .= "\n<b>Общее количество белка за день: " .$proteinCount. "</b>\n\n";
        }

        return $response;
    }
}
