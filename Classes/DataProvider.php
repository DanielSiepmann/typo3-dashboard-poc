<?php

namespace DanielSiepmann\DashboardPoc;

/*
 * Copyright (C) 2020 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class DataProvider
{
    /**
     * @var QueryBuilder
     */
    protected $ttContentQueryBuilder;

    public function __construct(QueryBuilder $ttContentQueryBuilder)
    {
        $this->ttContentQueryBuilder = $ttContentQueryBuilder;
    }

    public function contentTypes(): array
    {
        list($labels, $number) = $this->getCtypeInfo();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    // The defaults should made publicly available?
                    'backgroundColor' => ['red', 'blue', 'green', 'yello'],
                    'data' => $number,
                ]
            ],
        ];
    }

    protected function getCtypeInfo(): array
    {
        $labels = [];
        $number = [];

        $ctypes = $this->ttContentQueryBuilder
            ->selectLiteral('count(tt_content.ctype) as total')
            ->addSelect('tt_content.ctype')
            ->from('tt_content')
            ->groupBy('ctype')
            ->orderBy('total', 'desc')
            ->execute()
            ->fetchAll();

        foreach ($ctypes as $ctype) {
            $labels[] = $ctype['CType'];
            $number[] = (int) $ctype['total'];
        }

        return [
            $labels,
            $number,
        ];
    }
}
