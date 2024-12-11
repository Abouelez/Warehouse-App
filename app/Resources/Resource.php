<?php

namespace App\Resources;

class Resource
{



    static function resource($data)
    {
        if (is_array($data))
            return [];
    }

    static function collection_resource($collection)
    {
        $resource = [];

        foreach ($collection as $data)
            $resource[] = static::resource($data);

        return $resource;
    }

    static function unset_null(&$data)
    {
        foreach ($data as $key => $val) {
            if (!$val)
                unset($data[$key]);
        }
    }
}
