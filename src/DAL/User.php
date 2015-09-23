<?php

/**
 * Created by PhpStorm.
 * User: sean
 * Date: 9/16/2015
 * Time: 12:58 PM.
 */
namespace Spark\Project\DAL;

/**
 * Class User.
 */
class User
{
    private $name, $role, $email, $phone, $created_at, $updated_at;

    /**
     * @param \FluentPDO $fpdo
     * @param int        $id
     */
    public function __construct(\FluentPDO $fpdo, $id)
    {
        $this->fpdo = $fpdo;
        $this->getUser($id);
    }

    /**
     * @param $id int id of User to generate
     */
    private function getUser($id)
    {
        $query = $this->fpdo->from('user', $id);

        foreach ($query as $row) {
            $this->name = $row['name'];
            $this->role = $row['role'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
        }
    }

    /**
     * @return bool
     */
    public function isManager()
    {
        if ($this->role == 'manager') {
            return true;
        }

        return false;
    }

    /**
     * @return array $info of contact information.
     */
    public function getContactInfo()
    {
        $info = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];

        return $info;
    }
}
