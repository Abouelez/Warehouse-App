<?php

namespace Core;

class Pagination
{


    static function paginate($data, $limit)
    {
        $data = array_chunk($data, $limit);
        $response = [];
        for ($i = 0; $i < count($data); $i++) {
            $curr = $i + 1;
            $prev = ($curr - 1) == 0 ? null : $curr - 1;
            $next = ($curr + 1) > count($data) ? null : $curr + 1;
            $response[$i]['current_page'] = "?page=" . $curr;
            ($prev) ? $response[$i]['prev_page'] = "?page=" . $prev : $response[$i]['prev_page'] = null;
            ($next) ? $response[$i]['next_page'] = "?page=" . $next : $response[$i]['next_page'] = null;
            $response[$i]['data'] = $data[$i];
        }
        if (isset($_GET['page']))
            $page = $_GET['page'] - 1;
        else
            $page = 0;
        return $response[$page];
    }
}
