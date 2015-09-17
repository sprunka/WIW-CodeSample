<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 9/16/2015
 * Time: 12:58 PM
 */

namespace Spark\Project\Domain;


class User
{
    private $id=1, $name='foo', $role='manager', $email='email@domain.com', $phone='555.1212', $create_at='DATE', $updated_at='DATE';

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
        echo "<pre>\n".print_r($query,true)."</pre>\n";
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