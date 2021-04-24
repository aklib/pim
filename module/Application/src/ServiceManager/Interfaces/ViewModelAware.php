<?php

    /**
     * ViewModelAware.php
     *
     * @since 29.05.2016
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\ServiceManager\Interfaces;

    interface ViewModelAware
    {
        public function getList(array $config = [], array $options = []);
        public function getDetails(int $id);
    }