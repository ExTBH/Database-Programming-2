<?php

class Feature
{
    public $icon;
    public $title;
    public $description;

    public function __construct(string $icon, string $title, string $description)
    {
        $this->icon = $icon;
        $this->title = $title;
        $this->description = $description;
    }
}
