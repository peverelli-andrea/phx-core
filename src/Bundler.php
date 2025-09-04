<?php

namespace Phx\Core;

final class Bundler
{
	/** @param Render[] $component_renders */
	public final static function make(array $component_renders): Bundle
	{
		$classes_bundle = [];
		$typos_bundle = [];
		$scripts_bundle = [];

		foreach($component_renders as $component_render) {
			$classes = $component_render->classes;
			$typos = $component_render->typos;
			$scripts = $component_render->scripts;

			foreach($classes as $class_name => $class_css) {
				$classes_bundle[$class_name] = $class_css;
			}

			foreach($typos as $typo_name => $typo) {
				$typos_bundle[$typo_name] = $typo;
			}

			foreach($scripts as $script_name => $script) {
				$scripts_bundle[$script_name] = $script;
			}
		}

		$classes_bundle = array_values($classes_bundle);
		$typos_bundle = array_values($typos_bundle);
		$sctipts_bundle = array_values($scripts_bundle);

		$bundle = new Bundle(
			classes: $classes_bundle,
			typos: $typos_bundle,
			scripts: $scripts_bundle,
		);

		return $bundle;
	}

	public final static function getCss(Bundle $bundle): string
	{
		$typos_css = self::getTyposCss(bundle: $bundle);
		$classes_css = self::getClassesCss(bundle: $bundle);

		$css = "";

		array_push($css, $typos_css, $classes_css);
		$css = implode("\n\n", $css);

		return $css;
	}

	/** @return string[] */
	private final static function getTyposCss(Bundle $bundle): array
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
	private final static function getClassesCss(Bundle $bundle): array
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

	public final static function getScriptsBefore(Bundle $bundle): string
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

	public final static function getScriptsAfter(Bundle $bundle): string
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

	public final static function getHtml(Render $render): string
	{
		return $render->html;
	}
}
