<?php

namespace SmartCms\Options\Dto;

use SmartCms\Core\Traits\Dto\AsDto;

class OptionValueDto
{
   use AsDto;

   public function __construct(public int $id, public string $name, public ?string $image, public float $price, public string $sign, public bool $selected = false) {}

   public function toArray(): array
   {
      return [
         'id' => $this->id,
         'name' => $this->name,
         'image' => $this->validateImage($this->image ?? no_image()),
         'price' => $this->price,
         'sign' => $this->sign,
         'selected' => $this->selected,
      ];
   }
}
