<?php

namespace App\Domain\User\Repository;

use PDO;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\String_;

/**
 * Repository.
 */
class UserRepository
{
    /**
     * @var PDO The database connection
     */
    private $connection;

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Insert user row.
     *
     * @param array $user The user
     *
     * @return int The new ID
     */
    public function login($user, $password): Array
    {
        $sql = "SELECT * FROM apitest_users where username=".$this->connection->quote($user)
            . " AND password=".$this->connection->quote(hash("sha256", $password));

        $res = $this->connection->query($sql)->fetchAll();
      //  var_dump($res);die();
        return $res;
    }


    public function changePassword($user, $password): int
    {
        $sql = "UPDATE apitest_users SET password=? WHERE username=?";
        $stmt= $this->connection->prepare($sql);
        $stmt->execute([hash("sha256", $password), $user]);
       return $stmt->rowCount();
    }

    /**
     * Insert user row.
     *
     * @param array $user The user
     *
     * @return int The new ID
     */
    public function insertUser(array $user): int
    {
        $row = [
            'username' => $user['username'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
        ];

        $sql = "INSERT INTO users SET 
                username=:username, 
                first_name=:first_name, 
                last_name=:last_name, 
                email=:email;";

        $this->connection->prepare($sql)->execute($row);

        return (int)$this->connection->lastInsertId();
    }
}