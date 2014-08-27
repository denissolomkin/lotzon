<?php

interface IProcessor
{
    public function __construct(Model $model);

    public function create();
    public function fetch();
    public function update();
    public function delete();
}