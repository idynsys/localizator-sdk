<?php

namespace Ids\Localizator\Collections;

use Iterator;

/**
 * Класс для создания коллекций объектов
 */
abstract class Collection implements Iterator
{
    // Массив элементов коллекции
    private array $items = [];

    // Указатель на текущий элемент итератора
    private int $position = 0;

    // Метод интерфейса Iterator
    public function rewind()
    {
        $this->position = 0;
    }

    // Метод интерфейса Iterator
    public function current()
    {
        return $this->items[$this->position];
    }

    // Метод интерфейса Iterator
    public function key()
    {
        return $this->position;
    }

    // Метод интерфейса Iterator
    public function next()
    {
        $this->position++;
    }

    // Метод интерфейса Iterator
    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    /**
     * Получить все элементы коллекции
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Добавить объект в коллекцию
     *
     * @param object $item
     * @return void
     */
    public function addItem(object $item): void
    {
        $this->items[] = $item;
    }
}