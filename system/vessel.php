<?php

namespace Hasarius\system;

class Vessel
{
    // For Parser
    private $command = "";
    private $paramaters = [];
    private $modifiers = [];
    private $text = "";
    private $comment = "";
    // For Genarater
    private $lineNumber = 0;
    // For Command
    private $id = "";
    private $tagOpen = "";
    private $tagClose = "";
    private $script = "";
    private $css = "";

    // Setter & Getter
    public function setCommand(string $command): void
    {
        $this->cocommand = $command;
    }
    public function getCommand(): string
    {
        return $this->command;
    }

    public function setParamaters(array $paramaters): void
    {
        $this->paramaters = $paramaters;
    }
    public function getParamaters(): array
    {
        return $this->paramaters;
    }
    public function addParamaters(string $key, string $value): void
    {
        $this->paramaters[$key] = $value;
    }

    public function setModifiers(array $modifiers):void
    {
        $this->modifiers = $modifiers;
    }
    public function getModifiers(): array
    {
        return $this->modifiers;
    }
    public function addModify(string $modify): void
    {
        $this->modifiers[] = $modify;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }
    public function getText(): string
    {
        return $this->text;
    }

    public function setComment(string $comment):void
    {
        $this->comment = $comment;
    }
    public function getComment(): string
    {
        return $this->comment;
    }

    public function setLineNumber(int $lineNumber): void
    {
        $this->lineNumber = $lineNumber;
    }
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    public function setId(string $id):void
    {
        $this->id = $id;
    }
    public function getid(): string
    {
        return $this->id;
    }

    public function setTagOpen(string $tagOpen): void
    {
        $this->tagOpen = $tagOpen;
    }
    public function getTagOpen(): string
    {
        return $this->tagOpen;
    }

    public function setTagClose(string $tagClose): void
    {
        $this->tagClose = $tagClose;
    }
    public function getTagClose(): string
    {
        return $this->tagClose;
    }

    public function setScript(string $script): void
    {
        $this->script = $script;
    }
    public function getScript(): string
    {
        return $this->script;
    }

    public function setCss(string $css): void
    {
        $this->css = $css;
    }
    public function getCss(): string
    {
        return $this->css;
    }
}
