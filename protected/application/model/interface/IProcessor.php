<?php

interface IProcessor
{
    public function __construct(Model $model);

    public function create(Entity $instanse);
    public function fetch(Entity $instanse);
    public function update(Entity $instanse);
    public function delete(Entity $instanse);
}