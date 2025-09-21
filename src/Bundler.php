<?php

namespace Phx\Core;

final class Bundler
{
	/** @param Render[] $component_renders */
	final public static function make(array $component_renders): Bundle
	{
		$classes_bundle = [];
		$typos_bundle = [];
		$scripts_before_bundle = [];
		$scripts_after_bundle = [];

		foreach($component_renders as $component_render) {
			$classes = $component_render->classes;
			$typos = $component_render->typos;
			$scripts_before = $component_render->scripts_before;
			$scripts_after = $component_render->scripts_after;

			foreach($classes as $class_name => $class_css) {
				$classes_bundle[$class_name] = $class_css;
			}

			foreach($typos as $typo_name => $typo) {
				$typos_bundle[$typo_name] = $typo;
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
		$sctipts_before_bundle = array_values($scripts_before_bundle);
		$sctipts_after_bundle = array_values($scripts_after_bundle);

		$bundle = new Bundle(
			classes: $classes_bundle,
			typos: $typos_bundle,
			scripts_before: $scripts_before_bundle,
			scripts_after: $scripts_after_bundle,
		);

		return $bundle;
	}

	final public static function getCss(Bundle $bundle): string
	{
		$typos_css = self::getTyposCss(bundle: $bundle);
		$classes_css = self::getClassesCss(bundle: $bundle);

		$css = [];

		array_push($css, ...$typos_css, ...$classes_css);
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
