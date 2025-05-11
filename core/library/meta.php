<?php

use Melbahja\Seo\MetaTags;

class Meta {
    protected $title;
    protected $description;
    protected $meta;
    protected $image;
    protected $canonical;
    protected $robots;

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setMeta($property, $value) {
        $this->meta[$property] = $value;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function setCanonical($canonical) {
        $this->canonical = $canonical;
    }

    public function setRobots($robots) {
        $this->robots = $robots;
    }

    public function getMetaTags() {
        $metaTags = new MetaTags();

        if ($this->title) {
            $metaTags->title($this->title);
        }

        if ($this->description) {
            $metaTags->description($this->description);
        }

        if ($this->meta) {
            foreach ($this->meta as $property => $value) {
                $metaTags->meta($property, $value);
            }
        }

        if ($this->image) {
            $metaTags->image($this->image);
        }

        if ($this->canonical) {
            $metaTags->canonical($this->canonical);
        }

        if ($this->robots) {
            $metaTags->robots($this->robots);
        }

        return $metaTags;
    }
}