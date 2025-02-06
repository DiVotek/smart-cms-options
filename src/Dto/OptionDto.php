<?php

namespace SmartCms\Options\Dto;

use SmartCms\Core\Traits\Dto\AsDto;

class OptionDto
{
   use AsDto;
   public function __construct(public int $id, public string $name, public bool $required, public array $values = []) {}

   public function toArray(): array
   {
      return [
         'id' => $this->id,
         'name' => $this->name,
         'required' => $this->required,
         'values' => $this->values,
      ];
   }
}
