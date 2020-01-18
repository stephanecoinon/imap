<?php

namespace StephaneCoinon\Imap;

abstract class Command
{
    /**
     * Build the command.
     *
     * @return string
     */
    abstract public function build(): string;
}