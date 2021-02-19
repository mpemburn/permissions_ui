<?php

namespace App\Interfaces;

/**
 * Interface UiInterface
 * @package App\Interfaces
 *
 * @property int $id
 */
interface UiInterface
{
    public static function findById(int $id, string $guardName);
    public function getTable();
    public function save();
    public function update(array $attributes);
    public function delete();
}
