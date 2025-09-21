<?php

namespace Phx\Core;

abstract class Component
{
	final public static function getTypographyCss(
		TypographyRole $role = TypographyRole::LABEL,
		TypographySubRole $sub_role = TypographySubRole::LARGE,
	): TypographyCss
	{
		$typography_set = self::getTypographySet(
			role: $role,
			sub_role: $sub_role,
		);

		$normal_typo = $typography_set->normal;
		$italic_typo = $typography_set->italic;
		$emphasized_typo = $typography_set->emphasized;
		$emphasized_italic_typo = $typography_set->emphasized_italic;

		$normal_font = self::getFont(typo: $normal_typo);
		$italic_font = self::getFont(typo: $italic_typo);
		$emphasized_font = self::getFont(typo: $emphasized_typo);
		$emphasized_italic_font = self::getFont(typo: $emphasized_italic_typo);

		$role_name = $role->value;

		$typography_fonts = [
			"{$role_name}_normal_font" => $normal_font,
			"{$role_name}_italic_font" => $italic_font,
			"{$role_name}_emphasized_font" => $emphasized_font,
			"{$role_name}_emphasized_italic_font" => $emphasized_italic_font,
		];

		$normal_classes = self::compileTypographyClasses(
			typo: $normal_typo,
			role: $role,
			sub_role: $sub_role,
			style: TypographyStyle::NORMAL,
		);
		$italic_classes = self::compileTypographyClasses(
			typo: $italic_typo,
			role: $role,
			sub_role: $sub_role,
			style: TypographyStyle::ITALIC,
		);
		$emphasized_classes = self::compileTypographyClasses(
			typo: $emphasized_typo,
			role: $role,
			sub_role: $sub_role,
			style: TypographyStyle::EMPHASIZED,
		);
		$emphasized_italic_classes = self::compileTypographyClasses(
			typo: $emphasized_italic_typo,
			role: $role,
			sub_role: $sub_role,
			style: TypographyStyle::EMPHASIZED_ITALIC,
		);

		$typography_classes = [
			...$normal_classes,
			...$italic_classes,
			...$emphasized_classes,
			...$emphasized_italic_classes,
		];

		$typography_css = new TypographyCss(
			fonts: $typography_fonts,
			classes: $typography_classes,
		);

		return $typography_css;
	}

	private static function getFont(Typography $typo): string
	{
		$family = $typo->family;
		$typo_sources = $typo->sources;

		$sources_css = [];
		foreach($typo_sources as $source) {
			$typo_source = $source->source;
			$format = $source->format;
			$tech = $source->tech;

			if($format === "local") {
				$source_css = <<<CSS
				local("$typo_source")
				CSS;
			} else {
				if($tech === null){
					$source_css = <<<CSS
					url("/assets/fonts/$typo_source") format("$format")
					CSS;
				} else {
					$source_css = <<<CSS
					url("/assets/fonts/$typo_source") format("$format") tech("$tech")
					CSS;
				}
			}

			array_push($sources_css, $source_css);
		}

		$sources_css = implode(',', $sources_css);

		$typo_css = <<<CSS
		@font-face {
			font-family: $family;
			src: $sources_css;
		}
		CSS;

		return $typo_css;
	}

	/** @return string[] */
	private static function compileTypographyClasses(
		Typography $typo,
		TypographyRole $role,
		TypographySubRole $sub_role,
		TypographyStyle $style,
	): array
	{
		$role_name = $role->value;
		$sub_role_name = $sub_role->value;
		$style_name = $style->value;

		$family = $typo->family;
		$typo_style = $typo->style;

		$style_media_selector = "";
		if($style === TypographyStyle::ITALIC) {
			$style_media_selector = "i";
		}
		if($style === TypographyStyle::EMPHASIZED) {
			$style_media_selector = "b";
		}
		if($style === TypographyStyle::EMPHASIZED_ITALIC) {
			$style_media_selector = "b i";
		}

		$typo_class_generics = <<<CSS
		.{$role_name}_{$style_name}_typo $style_media_selector{
			font-family: $family;
			font-style: $typo_style;
		}
		CSS;

		$size = $typo->size;
		$weight = $typo->weight;
		$line_height = $typo->line_height;
		$letter_spacing = $typo->letter_spacing;
		$vf_weight = $typo->vf_weight;
		$grade = $typo->grade;
		$width = $typo->width;
		$rounding = $typo->rounding;
		$optical_size = $typo->optical_size;
		$cursive = $typo->cursive;
		$slant = $typo->slant;
		$fill = $typo->fill;
		$hyper_expansion = $typo->hyper_expansion;

		$typo_class_details = <<<CSS
		.{$role_name}_{$sub_role_name}_{$style_name}_typo $style_media_selector{
			font-size: $size;
			font-weight: $weight;
			line-height: $line_height;
			letter-spacing: $letter_spacing;
			font-stretch: $width;
			font-style: oblique $slant;
			font-variation-settings:
				"GRAD" $grade,
				"opsz" $optical_size,
				"CSRV" $cursive,
				"ROND" $rounding,
				"FILL" $fill,
				"HEXP" $hyper_expansion;
		}
		CSS;

		$classes = [
			"{$role_name}_{$style_name}_typo" => $typo_class_generics,
			"{$role_name}_{$sub_role_name}_{$style_name}_typo" => $typo_class_details,
		];

		return $classes;
	}

	private static function getTypographySet(
		TypographyRole $role = TypographyRole::LABEL,
		TypographySubRole $sub_role = TypographySubRole::LARGE,
	): TypographySet
	{
		$role_name = $role->value;
		$sub_role_name = $sub_role->value;

		$settings_path = realpath($_SERVER["DOCUMENT_ROOT"] . "/../settings/typography/");
		$settings_path = realpath($settings_path . "/$role_name/");

		$normal_settings_path = realpath($settings_path . "/$sub_role_name.json");
		$italic_settings_path = realpath($settings_path . "/{$sub_role_name}_italic.json");
		$emphasized_settings_path = realpath($settings_path . "/emphasized/$sub_role_name.json");
		$emphasized_italic_settings_path = realpath($settings_path . "/emphasized/{$sub_role_name}_italic.json");

		$normal_settings = self::readSettings($normal_settings_path);
		$italic_settings = self::readSettings($italic_settings_path);
		$emphasized_settings = self::readSettings($emphasized_settings_path);
		$emphasized_italic_settings = self::readSettings($emphasized_italic_settings_path);

		$typography_set = new TypographySet(
			normal: $normal_settings,
			italic: $italic_settings,
			emphasized: $emphasized_settings,
			emphasized_italic: $emphasized_italic_settings,
		);

		return $typography_set;
	}

	private static function readSettings(string $settings_path): Typography
	{
		$settings = json_decode(file_get_contents($settings_path));

		$sources = [];
		foreach($settings->sources as $font_source) {

			array_push(
				$sources,
				new FontSource(
					source: $font_source->source,
					format: $font_source->format,
					tech: $font_source->tech ?? null,
				),
			);
		}

		$typography = new Typography(
			sources: $sources,
			name: $settings->name,
			family: $settings->family,
			style: $settings->style,
			size: $settings->size,
			weight: $settings->weight,
			letter_spacing: $settings->letter_spacing,
			line_height: $settings->line_height,
			vf_weight: $settings->vf_weight,
			grade: $settings->grade,
			width: $settings->width,
			rounding: $settings->rounding,
			optical_size: $settings->optical_size,
			cursive: $settings->cursive,
			slant: $settings->slant,
			fill: $settings->fill,
			hyper_expansion: $settings->hyper_expansion,
		);

		return $typography;
	}

	final protected static function makeAttributes(
		CommonProps $props,
		/** @var string[]|null $classes */
		?array $classes = null,
	): string
	{
		$id = $props->id ?? uniqid();
		$class = $props->class ?? null;
		$style = $props->style ?? null;

		if($classes !== null) {
			$component_classes = implode(' ', $classes);

			if($class !== null) {
				$class .= " $component_classes";
			} else {
				$class = $component_classes;

			}
		}

		$attributes = " id=\"$id\"";

		if($class !== null) {
			$attributes .= " class=\"$class\"";
		}
		if($style !== null) {
			$attributes .= " style=\"$style\"";
		}

		return $attributes;
	}
}
