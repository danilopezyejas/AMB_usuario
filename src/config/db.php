<?php
  class db{
    private $dbHost = '127.0.0.1';
    private $dbUser = 'root';
    private $dbpass = '';
    private $dbName = 'amb_usuarios';

    public function conexionDB()
    {
      $mysqlConnect = "mysql:host=$this->dbHost;dbname=$this->dbName";
      $dbConexion = new PDO($mysqlConnect, $this->dbUser, $this->dbpass);
      $dbConexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $dbConexion;
    }
  }
 ?>
