<?php

class Feature
{
    /** @var string */
    public $icon;

    /** @var string */
    public $title;

    /** @var string */
    public $description;

    /**
     * @param string $icon
     * @param string $title
     * @param string $description
     */
    public function __construct($icon, $title, $description)
    {
        $this->icon = $icon;
        $this->title = $title;
        $this->description = $description;
    }
}
