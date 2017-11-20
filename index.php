<?php

//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);

//define constants for the credentials
define('DATABASE', 'an478');
define('USERNAME', 'an478');
define('PASSWORD', '8QD1YLZg');
define('CONNECTION', 'sql2.njit.edu');

class dbConn
{
    //variable to hold connection object.
    protected static $db;
    //private construct - class cannot be instatiated externally.
    private function __construct() 
    {
        try {
            // assign PDO object to db variable
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            }
        catch (PDOException $e) 
        {
            //Output error - would normally log this to error file rather than output to user.
            echo "Connection Error: " . $e->getMessage();
        }
    }
    // get connection function. Static method - accessible without instantiation
    public static function getConnection()
     {
        //Guarantees single instance, if no connection object exists then create one.
        if (!self::$db) 
          {
            //new connection object.
            new dbConn();
          }
        //return connection.
        return self::$db;
      }
}

class collection 
{
    static public function create()
     {
      $model = new static::$modelName;
      return $model;
     }
    static public function findAll()
     {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class); 
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
      }
    static public function findOne($id) 
     {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
      }


}

class accounts extends collection
 {
    protected static $modelName = 'account';
 }
class todos extends collection  
 {
    protected static $modelName = 'todo';
 }
 
class model 
{
    protected $tableName;
    public function save($id)
    {
        if ($id=='') 
        {
         $sql = $this->insert();
        }else
        {

         $sql = $this->update($id);
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
        echo 'One record saved: ' . $this->id;
    }

    private function insert() 
    { 
      $tableName = $this->tableName;
      //$array = get_object_vars($tableName);
      //$columnString = implode(',', $array);
      //$valueString = ":".implode(',:', $array);
      /*echo "INSERT INTO $tableName (" . $columnString . ") VALUES (" . $valueString . ")</br>";*/
      $sql = " INSERT INTO ".$tableName." (".static::$columns.") VALUES (".static::$values.")";
      return $sql;
    }

    private function update($id) 
    {   
        $tableName = $this->tableName;
        $sql = " UPDATE ".$tableName." SET lname='Smith' WHERE id=".$id;
        return $sql;
        echo 'I just updated record' . $this->id;
    }

    public function delete() 
    {
      $tableName = $this->tableName;
      $sql = "DELETE FROM ".$tableName." WHERE id = 9"; 
      $db = dbConn::getConnection();
      $statement = $db->prepare($sql);
      $statement->execute();  
      echo 'I just deleted record' . $this->id;
    }

}

class account extends model 
{
  public $id;
  public $email;
  public $fname;
  public $lname;
  public $phone;
  public $birthday;
  public $gender;
  public $password;
  public function __construct()
  {
    $this->tableName = 'accounts';
  }

  static $columns = 'id,email,fname,lname,phone,birthday,gender,password';
  static $values = "12,'ramons@gmail.com','Ramon','Smith','999-444-5566',1997-08-25,'Male','12345'";
}

class todo extends model 
{
  public $id;
  public $owneremail;
  public $ownerid;
  public $createddate;
  public $duedate;
  public $message;
  public $isdone;
  public function __construct()
  {
    $this->tableName = 'todos';  
  }
  static $columns = 'id,owneremail,ownerid,createdate,duedate,message,isdone,';
  static $values = "9,'james@gmail.com','5','Smith','2017-08-25 00:00:00','2017-11-04 00:00:00','This is it',1";
}


class display
{
  static public function printtable($result)//static method to print the table
  {
  echo "<table border=\"1\">";
   $i=0;
  foreach ($result as $row) 
  {
    echo "<tr>";
    foreach($row as $key => $value)
    {
      if($i==0)
      {
        echo "<th>".$key."</th>";
      }   
    }
    $i = $i + 1;
    echo "</tr><tr>";
    foreach ($row as $field) 
    {
      echo "<td>".$field."</td>";
    }
    echo "</tr>";
  }
  echo "</table><br><br>";
  }
}

echo "<h1>Selection of all records</h1>";
echo"<h2>Accounts Table</h2>";
$accrecords = accounts::findAll();
display::printtable($accrecords);
echo"<h2>Todos Table</h2>";
$todosrecord = todos::findALL();
display::printtable($todosrecord);
echo "<br><hr><br>";

echo "<h1>Selection of a Particular record </h1>";
echo"<h2>Accounts Table id=2</h2>";
$accrecords = accounts::findOne(2);
display::printtable($accrecords);
echo"<h2>Todos Table id=4</h2>";
$todosrecord = todos::findOne(4);
display::printtable($todosrecord);
echo "<br><hr><br>";

echo "<h1>Insert new Record </h1>";
echo"<h2>Accounts Table </h2>";
$objacc = new account();
$accrecords = $objacc->save('');
$accrecords = accounts::findAll();
display::printtable($accrecords);
echo "<br><hr><br>";

//echo "<h1>Update a Record </h1>";
//$objacc = new account();
//$accrecords = $objacc->save(12);
//$accrecords = accounts::findAll();
//display::printtable($accrecords);
//echo "<br><hr><br>";



 //echo "<h2>Delete a record</h2>";
 //$accobj = new account();
 //$accobj->delete();
?>

