<?php

class Pagination {
    public function get($url, $total, $limit, $page) {
        $pages = ceil($total / $limit);

        return array(
            'total_pages' => $pages,
            'first' => $this->modifyUrl($url, 1),
            'last' => $this->modifyUrl($url, $pages),
            'prev' => $page > 1 ? $this->modifyUrl($url, $page - 1) : '',
            'next' => $page < $pages ? $this->modifyUrl($url, $page + 1) : '',
            'results' => $page * $limit,
            'total' => $total,
            'pages' => $this->createPages($url, $pages, $page)
        );
    }

    protected function modifyUrl($url, $page) {
        if (strpos($url, '?') === false) {
            return $url . '?page=' . $page;
        } else {
            return $url . '&page=' . $page;
        }
    }

    protected function createPages($url, $pages, $page) {
        $limit = 5;

        $start = $page - floor($limit / 2);

        if ($start < 1) {
            $start = 1;
        }

        $end = $start + $limit - 1;

        if ($end > $pages) {
            $end = $pages;
        }

        $pages = array();

        for ($i = $start; $i <= $end; $i++) {
            $pages[] = array(
                'page' => $i,
                'url' => $this->modifyUrl($url, $i),
                'current' => $i == $page
            );
        }

        return $pages;
    }
}