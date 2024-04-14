<?php
class DB
{
    public $db;
    public $error;
    private $lastQuery;
    const HOST = '127.0.0.1';
    const DBNAME = 'birthdaygreeter';
    const USER = 'root';
    const PASS = '@149GTVNBskn';

    public function __construct()
    {
        $this->error = [];

        try
        {
            $this->db =new PDO("mysql:host=".self::HOST.";dbname=".self::DBNAME,self::USER,self::PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        catch(Exception | PDOException $e)
        {
            $this->error[] = "Connection failed: " . $e->getMessage();
            echo $e->getMessage();die;
        }
    }

    public function insert($query,$arg=[])
    {
        try
        {
            $this->lastQuery = $query;
            $qury = $this->db->prepare($query);
            ($qury->execute($arg)) ? $this->db->lastInsertId() : false;
        }
        catch(PDOException $e)
        {print_r($e);
            return false;
        }
    }

    public function select($query)
    {
        try
        {
            $data = $this->db->prepare($query);

            if($data->execute())
            {
                return ($data->rowCount() > 0) ? $data->fetchAll(PDO::FETCH_ASSOC) : false;
            }
            else return false;
        }
        catch(PDOException $e)
        {
            return false;
        }
    }    

    public function rawQuery($query)
    {
        try
        {
            $data = $this->db->prepare($query);

            if($data->execute())
            {
                return ($data->rowCount() > 0) ? $data->fetchAll(PDO::FETCH_ASSOC) : false;
            }
            else return false;
        }
        catch(PDOException $e)
        {
            print_r($e);
            return false;
        }
    }

    public function getLastQuery()
    {
        return $this->lastQuery;
    }
}