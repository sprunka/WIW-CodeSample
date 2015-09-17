<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 9/16/2015
 * Time: 12:58 PM
 */

namespace Spark\Project\DAL;


class User
{
    private $id, $name, $role, $email, $phone, $created_at, $updated_at;

    public function __construct(\FluentPDO $fpdo, $id=null, $userData=null)
    {
        $this->fpdo = $fpdo;

        if ( $id == null )
            if ( $userData !== null && is_array( $userData ) ) {
                $this->createUser($userData);
            } else {
                throw new \Exception('No User Data supplied or User Data in bad format.');
        } else {
            $this->getUser($id);
        }
    }

    private function createUser(array $userData)
    {

    }

    private function getUser($id)
    {
        $query = $this->fpdo->from('user', $id);

        foreach ($query as $row) {
            $this->name  = $row['name'];
            $this->role  = $row['role'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
        }
    }

    public function isManager()
    {
        if ( $this->role == 'manager' ) {
            return true;
        }
        return false;
    }

    public function getContactInfo()
    {
        $info = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone
        ];
        return $info;
    }

}