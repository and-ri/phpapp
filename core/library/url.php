<?php

class Url {
    public function link($route, $args = array()) {
        $url = URL_WEBSITE . $route;

        if ($args) {
            $url .= '?' . http_build_query($args);
        }

        return $url;
    }
}