<?php

namespace App\Domain\User\Repository;

use PDO;

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
        return $res;
    }

    public function unban($email): int
    {
        $sql = "UPDATE apitest_users SET banned=? WHERE username=?";
        $stmt= $this->connection->prepare($sql);
        $stmt->execute([0, $email]);
        return $stmt->rowCount();
    }

    public function changePassword($email, $password): int
    {
        $sql = "UPDATE apitest_users SET password=? WHERE username=?";
        $stmt= $this->connection->prepare($sql);
        $stmt->execute([hash("sha256", $password), $email]);
        return $stmt->rowCount();
    }

    public function changePermission($email, $role): int
    {
        $sql = "UPDATE apitest_users SET role=? WHERE username=?";
        $stmt= $this->connection->prepare($sql);
        $stmt->execute([$role, $email]);
        return $stmt->rowCount();
    }

    private function invalidateTokenGlobal()
    {
        $sql = "UPDATE apitest_users SET banned=?";
        $stmt= $this->connection->prepare($sql);
        $stmt->execute([1]);
        return $stmt->rowCount();
    }

    public function invalidateToken($email): int
    {
        if ($email == null) {
            return $this->invalidateTokenGlobal();
        }
        $sql = "UPDATE apitest_users SET banned=? WHERE username=?";
        $stmt= $this->connection->prepare($sql);
        $stmt->execute([1, $email]);
        return $stmt->rowCount();
    }


    public function refreshJwt($username)
    {
        $sql = "SELECT * FROM  apitest_users where username=".$this->connection->quote($username);
        $res = $this->connection->query($sql)->fetchAll();
        return $res;
    }


}