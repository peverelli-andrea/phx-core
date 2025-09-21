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

	final protected static function getPaletteCss(ForegroundColor|BackgroundColor $color): string
	{
		$color_name = $color->value;

		$palette_set = self::getPaletteSet(color: $color);

		$light_normal = $palette_set->light_normal;
		$light_medium = $palette_set->light_medium;
		$light_high = $palette_set->light_high;
		$dark_normal = $palette_set->dark_normal;
		$dark_medium = $palette_set->dark_medium;
		$dark_high = $palette_set->dark_high;

		if($color instanceof ForegroundColor) {
			$css = <<<CSS
			.$color_name {
				color: $light_normal;
			}

			@media (prefers-contrast: less) {
				.$color_name {
					color: $light_medium;
				}
			}

			@media (prefers-contrast: more) {
				.$color_name {
					color: $light_high;
				}
			}

			@media (prefers-color-scheme: dark) {
				.$color_name {
					color: $dark_normal;
				}

				@media (prefers-contrast: less) {
					.$color_name {
						color: $dark_medium;
					}
				}

				@media (prefers-contrast: more) {
					.$color_name {
						color: $dark_high;
					}
				}
			}
			CSS;
		} else {
			$css = <<<CSS
			.$color_name {
				background-color: $light_normal;
			}

			@media (prefers-contrast: less) {
				.$color_name {
					background-color: $light_medium;
				}
			}

			@media (prefers-contrast: more) {
				.$color_name {
					background-color: $light_high;
				}
			}

			@media (prefers-color-scheme: dark) {
				.$color_name {
					background-color: $dark_normal;
				}

				@media (prefers-contrast: less) {
					.$color_name {
						background-color: $dark_medium;
					}
				}

				@media (prefers-contrast: more) {
					.$color_name {
						background-color: $dark_high;
					}
				}
			}
			CSS;
		}

		return $css;
	}

	private static function getPaletteSet(ForegroundColor|BackgroundColor $color): PaletteSet
	{
		$color_name = $color->value;

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

		$light_normal = $light_normal_settings->$color_name;
		$light_medium = $light_medium_settings->$color_name;
		$light_high = $light_high_settings->$color_name;
		$dark_normal = $dark_normal_settings->$color_name;
		$dark_medium = $dark_medium_settings->$color_name;
		$dark_high = $dark_high_settings->$color_name;

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
