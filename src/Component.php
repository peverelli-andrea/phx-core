<?php

namespace AndreaPeverelli\PhxCore;

use AndreaPeverelli\PhxCore\Settings;

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

	final protected function useFont(Typo $typo, string $component_id = "default"): void
	{
		$monospace = $typo->monospace ? "monospace" : "proportional";
		$emphasized = $typo->emphasized ? "emphasized" : "not_emphasized";

		$class_name = "phx_{$monospace}_{$emphasized}_{$typo->role->value}_{$typo->sub_role->value}";

		$settings = Settings::getSettings()["typos"][$monospace][$emphasized][$typo->role->value][$typo->sub_role->value];

		array_push($this->classes[$component_id], $class_name);

		// generate src string for the various fonts fallbacks
		$src = [];
		foreach($settings["src"] as $source) {
			array_push($src, "url({$source["url"]}) format({$source["format"]})");
		}
		$src = implode(",", $src);

		array_push($this->css[$component_id], <<<CSS
		@font-face {
			font-family: {$settings["font-family"]};
			src: $src;
		}
		CSS);

		array_push($this->css[$component_id], <<<CSS
		.{$class_name} {
			font-family: {$settings["font-family"]};
			font-weight: {$settings["font-weight"]};
			line-height: {$settings["line-height"]};
			font-size: {$settings["font-size"]};
			letter-spacing: {$settings["letter-spacing"]};
		}
		CSS);
	}
}
