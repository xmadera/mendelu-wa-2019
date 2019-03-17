<?php
/**
 * Created by PhpStorm.
 * User: xpisarov
 * Date: 13.03.2019
 * Time: 9:54
 */

class Rooms extends Model
{
    function add($title, $idOwner, $lock = false) {
        $stmt = $this->db->prepare('INSERT INTO rooms 
(created, title, id_users_owner, lock) VALUES (NOW(), :t, :id, :l)');
        $stmt->bindValue(':t', $title);
        $stmt->bindValue(':id', $idOwner);
        $stmt->bindValue(':l', $lock);
        return $stmt->execute();
    }

    function all() {
        $stmt = $this->db->query('SELECT * FROM rooms 
ORDER BY created');
        return $stmt->fetchAll();
    }

    function find() {

    }

    function delete() {

    }
}