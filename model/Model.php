<?php
/**
 * Created by PhpStorm.
 * User: xpisarov
 * Date: 13.03.2019
 * Time: 9:54
 */

abstract class Model
{

    protected $db;

    function __construct(PDO $db)
    {
        $this->db = $db;
    }


}