<?php

/** 
 * Ядро системы управления контентом 
 *
 * Класс адаптера Mysql
 * 
 * @author Alexsandr Kondrashov 
 * 
 * 
 */
class DB_MysqlAdapter extends DB_AdapterAbstact {

    private $db = null;
    private $count = 0;
    private $dump_query = "\n\n#########################\n";
    private $num_rows;

    /**
     * Адаптер работы с БД mysql
     */
    function __construct() {
        //parent::__construct();
        $this->dump_query.=date('Y-m-d') . "\n";
        $this->db = @new mysqli(Config::DB_SERVER, Config::DB_USER, Config::DB_PASSWORD, Config::DB_NAME);
        // var_dump($this->db);
        if ($this->db->connect_errno) {

            throw new QDException($this->db->connect_error, $this->db->connect_errno);
        }
        try {
            $this->db->query("SET NAMES utf8");
            $this->db->query("SET CHARACTER SET utf8");
            $this->db->query("SET CHARACTER_SET_CONNECTION=utf8");
            $this->count+=3;
        } catch (QDException $ex) {
            $ex->_getMessage();
        }
    }

    /**
     * Запрос на выборку. Возвращает результат в виде массива
     * @param string $query запрос
     * @param DB_AdapterAbstact $type тип возвращаемого результата ASSOC_ARRAY||NUM_ARRAY
     * @return array() результат запроса
     */
    public function getResult($query, $type) {
        $this->count++;
        $this->dump_query.=date('H:i:s') . " " . $query . "\n\r";
        $result = $this->db->query($query);
        if ($this->db->error) {
            throw new QDException($this->db->error, $this->db->errno);
        }
        $this->num_rows = $result->num_rows;
        $data = array();

        if ($type == DB_AdapterAbstact::ASSOC_ARRAY) {
            while ($result_ = $result->fetch_assoc()) {
                $data[] = $result_;
            }
        }

        if ($type == DB_AdapterAbstact::NUM_ARRAY) {
            while ($result_ = $result->fetch_row()) {
                $data[] = $result_;
            }
        }
        $query = new stdClass();



        if ($type == DB_AdapterAbstact::OBJECT) {


            while ($result_ = $result->fetch_object()) {
                $data[] = $result_;
            }
        }
        
        $query->row = isset($data[0]) ? $data[0] : array();
        
        $query->rows = $data;
        $query->num_rows = $result->num_rows;

        $this->num_rows=$result->num_rows;
        

        

        $result->free();

        return $query;
    }

    public function getNumRows() {
        return $this->num_rows;
    }

    public function getAffectedRows(){
        return $this->db->affected_rows;
    }
    
    /**
     * добавление данных 
     * @param string $query запрос на добавление
     * @param bool $return_id вернуть id добавленной записи
     * @return bool id/bool
     */
    public function insert($query, $return_id = true) {
        $this->count++;
        $this->dump_query.=date('H:i:s') . " " . $query . "\n\r";
        $result = $this->db->query($query);
        if ($this->db->error) {
            throw new QDException($this->db->error, $this->db->errno);
        }

        if ($return_id) {
            return $this->db->insert_id;
        } else {
            return $result;
        }
    }

    /** переписать!!
     * Вставка записей в таблицу.
     * Можно вставлять как одну, так и несколько строк
     * @param array $field поля
     * @param array $data массив данных array('a','b')- для вставки одной строки, array(array('a','b'),array(...)...)- для вставки n строк
     * @param string $table таблица
     * @param bool $return_id вернуть ли id последней добавленной записи
     * @return int/bool id/bool
     */
    public function insertArray($field, $data, $table, $return_id = true) {
        $this->count++;

        $fields = array();
        $datas = array();

        $sql = "";
        if (count($field) == count($data[0]) && is_array($data[0])) {
            for ($i = 0; $i < count($field); $i++) {
                $fields[] = "`" . $table . "`.`" . $field[$i] . "`";
            }
            $d = array();
            foreach ($data as $key => $value) {
                if (count($field) != count($value)) {
                    throw new QDException("Количество полей не совпадает с добавляемыми данными", "100000");
                }
                $val = array();
                for ($i = 0; $i < count($value); $i++) {
                    $val[] = mysql_escape_string($value[$i]);
                }
                $datas[] = "('" . implode("','", $val) . "')";
            }
            $sql = "INSERT INTO `" . Config::DB_PREFIX . $table . "` (" . implode(",", $fields) . ") values " . implode(",", $datas) . "";
        } elseif (count($field) != count($data) && !is_array($data[0])) {
            throw new QDException("Количество полей не совпадает с добавляемыми данными", "100000");
        } else if (is_array($data[0]) && count($field) != count($data[0])) {
            throw new QDException("Количество полей не совпадает с добавляемыми данными", "100000");
        } else {
            for ($i = 0; $i < count($field); $i++) {
                $fields[] = "`" . $table . "`.`" . $field[$i] . "`";
                $datas[] = mysql_escape_string($data[$i]);
            }
            $sql = "INSERT INTO `" . Config::DB_PREFIX . $table . "` (" . implode(",", $fields) . ") values ('" . implode("','", $datas) . "')";
        }
        $this->dump_query.=date('H:i:s') . " " . $sql . "\n\r";
        $result = $this->db->query($sql);
        if ($this->db->error) {
            throw new QDException($this->db->error, $this->db->errno);
        }
        if ($return_id) {
            return $this->db->insert_id;
        } else {
            return $result;
        }
    }

    /**
     * Изменение данных в таблице
     * @param string $query запрос
     * @return bool 
     */
    public function update($query) {
        $this->count++;
        $this->dump_query.=date('H:i:s') . " " . $query . "\n\r";

        $result = $this->db->query($query);

        if ($this->db->error) {
            throw new QDException($this->db->error, $this->db->errno);
        }

        return $result;
    }

    /**
     * удвление данных в таблице
     * @param string $query запрос
     * @return bool 
     */
    public function delete($query) {
        $this->count++;
        $this->dump_query.=date('H:i:s') . " " . $query . "\n\r";
        $result = $this->db->query($query);
        if ($this->db->error) {
            throw new QDException($this->db->error, $this->db->errno);
        }
        return $result;
    }

    /**
     * Запуск транзакции
     */
    public function startTransaction() {
        //отключаем автокоммит. Если не получилось отключить вернется false и сгенерируется исключение
        if (!$this->db->autocommit(false)) {
            throw new QDException($this->db->error, $this->db->errno);
        }
    }

    /**
     * Коммит транзакции
     */
    public function commit() {
        $this->db->commit();
        $this->db->autocommit(true);
        //после коммита не забываем включить автокоммит
    }

    /**
     * Откат транзакции
     */
    public function rollback() {
        $this->db->rollback();
        $this->db->autocommit(true);
        //после отката тоже не забываем включить автокоммит
    }

    /**
     * экранирование
     * @param string неэкранированная строка
     * @return type экранированная строка
     */
    public function escape($string) {


        return mysql_escape_string($string);
    }

    function __destruct() {
        $this->db->close();

        if (Config::$WRITE_DB_QUERYES) {
            $fp = null;
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/system/var/log/DBlog.txt')) {
                $fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/system/var/log/DBlog.txt', "w");
            } else {
                $fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/system/var/log/DBlog.txt', "a+");
            }
            if ($fp) {
                $this->dump_query.="\n" . "queries=" . $this->count;
                fwrite($fp, $this->dump_query);
                fclose($fp);
            } else {
                throw new Exception('error open DBlog file');
            }
        }
    }

}

//fetch_array
?>
