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

		$css = "";

		array_push($css, $typos_css, $classes_css);
		$css = implode("\n\n", $css);

		return $css;
	}

	/** @return string[] */
	private static function getTyposCss(Bundle $bundle): array
	{
	  	$typos_css = [];
		foreach($bundle->typos as $typo) {
			$font_family = $typo->font_family;
			$src = [];
			foreach($typo->src as $font_src) {
				$font_src_url = $font_src->url;
				$font_src_format = $font_src->format;

				if($font_src_format === "local") {
					array_push(
						$src,
						<<<CSS
						local("{$font_src_url}")
						CSS,
					);
				} else {
					array_push(
						$src,
						<<<CSS
						url("{$font_src_url}") format("{$font_src_format}")
						CSS,
					);
				}
			}

			array_push(
				$typos_css,
				<<<CSS
				@font-face {
					font-family: {$typo->font_family};
					src: {implode($typo->src};
				}
				CSS,
			);

			return $typos_css;
		}
	}

	/** @return string[] */
	private static function getClassesCss(Bundle $bundle): array
	{
		$classes_css = [];

		foreach($bundle->classes as $class) {
			array_push(
				$classes_css,
				<<<CSS
				{$class}
				CSS,
			);
		}

		return $classes_css;
	}

	final public static function getScriptsBefore(Bundle $bundle): string
	{
		$scripts_before_bundle = $bundle->scripts_before;

		$html_scripts = [];
		
		foreach($scripts_before_bundle as $script) {
			array_push(
				$html_scripts,
				<<<HTML
				<script>
					{$script}
				</script>
				HTML,
			);
		}

		$html_scripts = implode("\n\n", $html_scripts);

		return $html_scripts;
	}

	final public static function getScriptsAfter(Bundle $bundle): string
	{
		$scripts_after_bundle = $bundle->scripts_after;

		$html_scripts = [];
		
		foreach($scripts_after_bundle as $script) {
			array_push(
				$html_scripts,
				<<<HTML
				<script>
					{$script}
				</script>
				HTML,
			);
		}

		$html_scripts = implode("\n\n", $html_scripts);

		return $html_scripts;
	}

	final public static function getHtml(Render $render): string
	{
		return $render->html;
	}
}
