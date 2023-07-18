<?php

namespace glad\kit;

use IteratorIterator;
use pocketmine\item\Item;

class Kit
{
    public function __construct(
        private string $name,
        private string $whocreate,
        private array $items,
        private array $armor
    ){}

    public function toArray(): array
    {
        return [
            'whocreate' => $this->whocreate,
            'items' => $this->items,
            'armor' => $this->armor
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getArmor(): array
    {
        return Utils::milk($this->armor);
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return Utils::milk($this->items);
    }

    /**
     * @return string
     */
    public function getWhocreate(): string
    {
        return $this->whocreate;
    }

}