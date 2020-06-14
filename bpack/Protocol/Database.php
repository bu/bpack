<?php declare(strict_types=1);
namespace bPack\Protocol;

use \PDO;

interface Database {
    public function getConnection():PDO;
}