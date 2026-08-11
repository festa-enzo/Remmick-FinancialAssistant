<?php
declare(strict_types=1);

class Paginator
{
    public static function make(array $items, int $total, int $page, int $perPage): array
    {
        $lastPage = max(1, (int)ceil($total / $perPage));
        $from     = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
        $to       = min($page * $perPage, $total);

        return [
            'data'       => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => $lastPage,
                'from'         => $from,
                'to'           => $to,
                'has_next'     => $page < $lastPage,
                'has_prev'     => $page > 1,
            ],
        ];
    }
}
