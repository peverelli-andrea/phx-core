<?php

namespace AndreaPeverelli\PhxCore;

use AndreaPeverelli\PhxCore\Settings;
use AndreaPeverelli\PhxCore\palette\ColorScheme;

abstract class Component
{
	private array $settings = [];

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

	private function loadSettings(): void
	{
		if($this->settings === []) {
			$this->settings = Settings::getSettings();
		}
	}

	final protected function useFont(Typo $typo, string $component_id = "default"): void
	{
		$monospace = $typo->monospace ? "monospace" : "proportional";
		$emphasized = $typo->emphasized ? "emphasized" : "not_emphasized";

		$class_name = "phx_{$monospace}_{$emphasized}_{$typo->role->value}_{$typo->sub_role->value}";

		$this->loadSettings();
		$settings = $this->settings["typos"][$monospace][$emphasized][$typo->role->value][$typo->sub_role->value];

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

	final protected function useColor(
		ColorScheme $color,
		ColorMode $mode,
		string $component_id = "default",
	): void
	{
		$class_name = "phx_{$color->value}_{$mode->value}";

		array_push($this->classes[$component_id], $class_name);

		$this->loadSettings();
		$settings = $this->settings["colors"];

		array_push($this->css[$component_id], <<<CSS
		.{$class_name} {
			{$mode->getCssPropertyName()}: {$settings[$color->getColor(theme: "light", contrast: "default")->value]};
		}

		@media (prefers-contrast: more) {
			.{$class_name} {
				{$mode->getCssPropertyName()}: {$settings[$color->getColor(theme: "light", contrast: "high")->value]};
			}
		}

		@media (prefers-color-scheme: dark) {
			.{$class_name} {
				{$mode->getCssPropertyName()}: {$settings[$color->getColor(theme: "dark", contrast: "default")->value]};
			}
		}

		@media (prefers-color-scheme: dark) and (prefers-contrast: more) {
			.{$class_name} {
				{$mode->getCssPropertyName()}: {$settings[$color->getColor(theme: "dark", contrast: "high")->value]};
			}
		}
		CSS);
	}
}
