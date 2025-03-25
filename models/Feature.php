<?php

class Feature
{
    public string $icon;
    public string $title;
    public string $description;

    public function __construct(string $icon, string $title, string $description)
    {
        $this->icon = $icon;
        $this->title = $title;
        $this->description = $description;
    }
}
