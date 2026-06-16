<?php

namespace AndreaPeverelli\PhxCore;

abstract class Component
{
	protected array $props = [];
	protected array $attributes = [];
	protected array $classes = [];
	protected array $css = [];

	final protected function registerComponent(mixed $props, string $component_id = "default")
	{
		$this->props[$component_id] = $props;
		$this->attributes[$component_id] = "";
		$this->classes[$component_id] = $props->common_props->classes;
		$this->fonts[$component_id] = [];
		$this->colors[$component_id] = [];
		$this->css[$component_id] = $props->common_props->css;
	}

	final protected function makeAttributes(string $component_id = "default"): void
	{
		// build common props attributes
		$attributes = [
			"id" => $this->props[$component_id]->common_props->id ?? uniqid(),
			"class" => implode(" ", $this->classes[$component_id]),
		];

		$attributes["id"] = "id=\"{$attributes["id"]}\"";
		if($attributes["class"]) {
			$attributes["class"] = "class=\"{$attributes["class"]}\"";
		} else {
			unset($attributes["class"]);
		}

		// merge other custom attributes
		foreach($this->props[$component_id]->common_props->attributes as $attribute_name => $attribute_value){
			$attributes[$attribute_name] = "$attribute_name=\"$attribute_value\"";
		}

		$attributes = implode(" ", $attributes);

		if($attributes) {
			$this->attributes[$component_id] = " $attributes";
		}
	}
}
