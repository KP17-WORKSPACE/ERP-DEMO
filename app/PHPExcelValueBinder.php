<?php

namespace App\Support;

class PHPExcelValueBinder extends \PHPExcel_Cell_DefaultValueBinder
{
    public function bindValue(\PHPExcel_Cell $cell, $value = null)
    {
        if (is_int($value) || is_float($value)) {
            $cell->setValueExplicit($value, \PHPExcel_Cell_DataType::TYPE_NUMERIC);

            return true;
        }

        if (is_bool($value)) {
            $cell->setValueExplicit($value, \PHPExcel_Cell_DataType::TYPE_BOOL);

            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
