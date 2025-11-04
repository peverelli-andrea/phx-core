<?php

namespace Phx\Core;

final class Bundler
{
	/** @param Render[] $component_renders */
	final public static function make(array $component_renders): Bundle
	{
		$classes_bundle = [];
		$typos_bundle = [];
		$colors_bundle = [];
		$scripts_before_bundle = [];
		$scripts_after_bundle = [];

		foreach($component_renders as $component_render) {
			$classes = $component_render->classes;
			$typos = $component_render->typos;
			$colors = $component_render->colors;
			$scripts_before = $component_render->scripts_before;
			$scripts_after = $component_render->scripts_after;

			foreach($classes as $class_name => $class_css) {
				$classes_bundle[$class_name] = $class_css;
			}

			foreach($typos as $typo_name => $typo) {
				$typos_bundle[$typo_name] = $typo;
			}

			foreach($colors as $color_name => $color) {
				$colors_bundle[$color_name] = $color;
			}

			foreach($scripts_before as $script_name => $script) {
				$scripts_before_bundle[$script_name] = $script;
			}

			foreach($scripts_after as $script_name => $script) {
				$scripts_after_bundle[$script_name] = $script;
			}
		}

		$classes_bundle = array_values($classes_bundle);
		$typos_bundle = array_values($typos_bundle);
		$colors_bundle = array_values($colors_bundle);
		$sctipts_before_bundle = array_values($scripts_before_bundle);
		$sctipts_after_bundle = array_values($scripts_after_bundle);

		$bundle = new Bundle(
			classes: $classes_bundle,
			typos: $typos_bundle,
			colors: $colors_bundle,
			scripts_before: $scripts_before_bundle,
			scripts_after: $scripts_after_bundle,
		);

		return $bundle;
	}

	final public static function getCss(Bundle $bundle): string
	{
		$typos_css = self::getTyposCss(bundle: $bundle);
		$colors_css = self::getColorsCss(bundle: $bundle);
		$classes_css = self::getClassesCss(bundle: $bundle);

		$css = [];

		array_push(
			$css,
			$colors_css,
			...$typos_css,
			...$classes_css,
		);
		$css = implode("\n\n", $css);

		return $css;
	}

	/** @return string[] */
	private static function getTyposCss(Bundle $bundle): array
	{
		$typos = $bundle->typos;

	  	$typos_css = [];
		foreach($typos as $key => $css) {
			$typos_css[$key] = $css;
		}

		return $typos_css;
	}

	private static function getColorsCss(Bundle $bundle): string
	{
		$colors = $bundle->colors;

		$palette_colors = [];
		foreach($colors as $color_name => $color) {
			if(gettype($color) !== "string") {
				$palette_colors[$color_name] = $color;
			}
		}

		$colors_css = "";
		if(count($palette_colors)) {
			$palette_config = self::readPaletteConfig();

			$light_normal_colors = [];
			$light_medium_colors = [];
			$light_high_colors = [];
			$dark_normal_colors = [];
			$dark_medium_colors = [];
			$dark_high_colors = [];

			foreach($palette_colors as $_ => $color) {
				$css_color_name = $color->getCssName();

				$palette_set = self::getPaletteSet(
					color: $color,
					palette_config: $palette_config,
				);

				$light_normal_color = $palette_set->light_normal;
				$light_medium_color = $palette_set->light_medium;
				$light_high_color = $palette_set->light_high;
				$dark_normal_color = $palette_set->dark_normal;
				$dark_medium_color = $palette_set->dark_medium;
				$dark_high_color = $palette_set->dark_high;

				array_push($light_normal_colors, "--color-$css_color_name: $light_normal_color;");
				array_push($light_medium_colors, "--color-$css_color_name: $light_medium_color;");
				array_push($light_high_colors, "--color-$css_color_name: $light_high_color;");
				array_push($dark_normal_colors, "--color-$css_color_name: $dark_normal_color;");
				array_push($dark_medium_colors, "--colro-$css_color_name: $dark_medium_color;");
				array_push($dark_high_colors, "--color-$css_color_name: $dark_high_color;");
			}

			$light_normal_colors = implode("\n", $light_normal_colors);
			$light_medium_colors = implode("\n", $light_medium_colors);
			$light_high_colors = implode("\n", $light_high_colors);
			$dark_normal_colors = implode("\n", $dark_normal_colors);
			$dark_medium_colors = implode("\n", $dark_medium_colors);
			$dark_high_colors = implode("\n", $dark_high_colors);

			$colors_css = <<<CSS
			:root {
				$light_normal_colors
			}

			@media (prefers-contrast: less) {
				:root {
					$light_medium_colors
				}
			}

			@media (prefers-contrast: more) {
				:root {
					$light_high_colors
				}
			}

			@media (prefers-color-scheme: dark) {
				:root {
					$dark_normal_colors
				}

				@media (prefers-contrast: less) {
					:root {
						$dark_medium_colors
					}
				}

				@media (prefers-contrast: more) {
					:root {
						$dark_high_colors
					}
				}
			}
			CSS;
		}

		return $colors_css;
	}

	/** @return object[] */
	final public static function readPaletteConfig(): array
	{
		$settings_path = realpath($_SERVER["DOCUMENT_ROOT"] . "/../settings/palette/");

		$light_normal_path = realpath($settings_path . "/light-normal.json");
		$light_medium_path = realpath($settings_path . "/light-medium.json");
		$light_high_path = realpath($settings_path . "/light-high.json");
		$dark_normal_path = realpath($settings_path . "/dark-normal.json");
		$dark_medium_path = realpath($settings_path . "/dark-medium.json");
		$dark_high_path = realpath($settings_path . "/dark-high.json");


		$light_normal_settings = json_decode(file_get_contents($light_normal_path));
		$light_medium_settings = json_decode(file_get_contents($light_medium_path));
		$light_high_settings = json_decode(file_get_contents($light_high_path));
		$dark_normal_settings = json_decode(file_get_contents($dark_normal_path));
		$dark_medium_settings = json_decode(file_get_contents($dark_medium_path));
		$dark_high_settings = json_decode(file_get_contents($dark_high_path));

		$palette_config = [
			"light_normal" => $light_normal_settings,
			"light_medium" => $light_medium_settings,
			"light_high" => $light_high_settings,
			"dark_normal" => $dark_normal_settings,
			"dark_medium" => $dark_medium_settings,
			"dark_high" => $dark_high_settings,
		];

		return $palette_config;
	}

	/** @param object[] $palette_config */
	final public static function getPaletteSet(
		ForegroundColor|BackgroundColor $color,
		array $palette_config,
	): PaletteSet
	{
		$color_name = $color->value;

		$light_normal = $palette_config["light_normal"]->$color_name;
		$light_medium = $palette_config["light_medium"]->$color_name;
		$light_high = $palette_config["light_high"]->$color_name;
		$dark_normal = $palette_config["dark_normal"]->$color_name;
		$dark_medium = $palette_config["dark_medium"]->$color_name;
		$dark_high = $palette_config["dark_high"]->$color_name;

		$palette_set = new PaletteSet(
			light_normal: $light_normal,
			light_medium: $light_medium,
			light_high: $light_high,
			dark_normal: $dark_normal,
			dark_medium: $dark_medium,
			dark_high: $dark_high,
		);

		return $palette_set;
	}


	/** @return string[] */
	private static function getClassesCss(Bundle $bundle): array
	{
		$classes = $bundle->classes;

		$classes_css = [];

		foreach($classes as $key => $class) {
			$classes_css[$key] = $class;
		}

		return $classes_css;
	}

	final public static function getScriptsBefore(Bundle $bundle): string
	{
		$scripts_before_bundle = $bundle->scripts_before;

		$html_scripts = [];
		
		foreach($scripts_before_bundle as $key => $script) {
			$html_script = <<<HTML
			<script>
				{$script}
			</script>
			HTML;

			$html_scripts[$key] = $html_script;
		}

		$html_scripts = implode("\n\n", $html_scripts);

		return $html_scripts;
	}

	final public static function getScriptsAfter(Bundle $bundle): string
	{
		$scripts_after_bundle = $bundle->scripts_after;

		$html_scripts = [];
		
		foreach($scripts_after_bundle as $key => $script) {
			$html_script = <<<HTML
			<script>
				{$script}
			</script>
			HTML;

			$html_scripts[$key] = $html_script;
		}

		$html_scripts = implode("\n\n", $html_scripts);

		return $html_scripts;
	}

	final public static function getHtml(Render $render): string
	{
		return $render->html;
	}
}
