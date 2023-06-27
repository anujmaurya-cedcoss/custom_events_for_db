<?php
namespace App\Handlers;

use Phalcon\Escaper;

class Listener
{
    public function escape()
    {
        $escaper = new Escaper();

        $db = \Phalcon\Di::getDefault()->get('db');
        $query = 'SELECT * FROM users';
        $sth = $db->prepare("$query");
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            if ($value['name'] == '0') {
                $uquery = "UPDATE `users` SET `name`='10' WHERE `id` = '$key'";
                $sth = $db->prepare("$uquery");
                $sth->execute();
            }
        }
        return [
            $_POST['name'] = ($_POST['name'] == '' ? 'default' : $escaper->escapeHtml($_POST['name'])),
            $_POST['email'] = ($_POST['email'] == '' ? 'default@mail.com' : $escaper->escapeHtml($_POST['email']))
        ];
    }
}
