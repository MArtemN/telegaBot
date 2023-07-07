<?php

class Db
{
    protected $host = 'mysqldb';
    protected $userName = 'root';
    protected $password = 'root';
    protected $dbName = 'foodPlanBd';
    protected $maxProtein = 55;

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
            $maxProteinCount = $this->getProteinForDay();
            if ($maxProteinCount >= $this->maxProtein) {
                return "Данные успешно записаны\n\n<b>ВНИМАНИЕ! КОЛИЧЕСТВО БЕЛКА ПРЕВОСХОДИТ МАКСИМАЛЬНО ДОПУСТИМОЕ!\nБелка за день = ".$maxProteinCount."</b>";
            }

            return "Данные успешно записаны\nБелка за день = ".$maxProteinCount;
        } else {
            return 'Данные не записались';
        }
    }

    protected function getProteinForDay()
    {
        $response = 0;

        $conn = $this->getConnect();
        $sql = "SELECT `protein` FROM `food` WHERE date = CURDATE()";
        $request = $conn->query($sql);

        while($row = $request->fetch()){
            $response += $row['protein'];
        }

        return $response;
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

        $request = $conn->query($sql);

        if (!$request) {
            return 'Ошибка получения попробуйте еще';
        }
        while($row = $request->fetch()){
            $resultArr[$row['date']][] = $row;
        }

        foreach ($resultArr as $date => $itemArr) {
            $formattedDate = date("d.m.Y", strtotime($date));
            $response .= $formattedDate;
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
