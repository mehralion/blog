<?php

/**
 * Class RatingItemImage
 *
 * @package application.rating.models
 */
class RadioContainer
{
    public $name;
    public $listeners;
    public $description;
    public $title;
    public $url;
    public $start;
    public $genre;

    public function __construct(array $options)
    {
        if(is_array($options)) {
            foreach($options as $name => $value)
                $this->{$name} = $value;
        }
    }
}