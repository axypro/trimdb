<?php
/**
 * Trimmed a layer of abstraction over the data
 *
 * @package axy\trimdb
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @license https://raw.github.com/axypro/trimdb/master/LICENSE MIT
 * @link https://github.com/axypro/trimdb repository
 * @uses PHP5.4+
 */

namespace axy\trimdb;

if (!\is_file(__DIR__.'/vendor/autoload.php')) {
    throw new \LogicException('Please: ./composer.phar install --dev');
}

require_once(__DIR__.'/vendor/autoload.php');
