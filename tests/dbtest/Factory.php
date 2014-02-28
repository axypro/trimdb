<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\tests\dbtest;

use go\DB\DB;

class Factory
{
    /**
     * @param string $dumpfile [optional]
     * @param string $dump [optional]
     * @param boolean $newcon [optional]
     * @return \go\DB\DB
     */
    public static function getDB($dumpfile = null, $dump = null, $newcon = false)
    {
        if ($newcon || (!self::$db)) {
            $params = __DIR__.'/params.php';
            if (!\is_file($params)) {
                return null;
            }
            $params = include($params);
            self::$db = DB::create($params);
        }
        if ($dumpfile) {
            $dump = \file_get_contents($dumpfile);
        }
        if ($dump) {
            foreach (\explode(';', $dump) as $sql) {
                $sql = \trim($sql);
                if ($sql !== '') {
                    self::$db->query($sql);
                }
            }
        }
        return self::$db;
    }

    /**
     * @var \go\DB\DB
     */
    private static $db;
}
