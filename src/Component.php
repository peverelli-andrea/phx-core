<?php

namespace Phx\Core;

abstract class Component
{
	final protected static function getTypographyCss(
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

	final protected static function getColorValue(ForegroundColor|BackgroundColor|string $color): string
	{
		if(gettype($color) === "string") {
			$color_value = $color;
		} else {
			$color_value = "var(--color-{$color->getCssName()})";
		}

		return $color_value;
	}

	final protected static function getColorName(ForegroundColor|BackgroundColor|string $color): string
	{
		if(gettype($color) === "string") {
			if(substr($color, 0, 1) === "#") {
				$color_name = substr($color, 1);
			} else {
				$color_name = $color;
			}
		} else {
			$color_name = $color->value;
		}

		return $color_name;
	}

	final protected static function getColorStatesCss(
		ColorStates $color_states,
		CssColorProperty $css_color_property,
		string $class_name,
	): ColorStatesCss
	{
		$css_color_property_name = $css_color_property->value;

		$default = $color_states->default;
		$disabled = $color_states->disabled;
		$hover = $color_states->hover;
		$focus = $color_states->focus;
		$pressed = $color_states->pressed;
		$toggled_default = $color_states->toggled_default;
		$toggled_hover = $color_states->toggled_hover;
		$toggled_focus = $color_states->toggled_focus;
		$toggled_pressed = $color_states->toggled_pressed;

		$css_default = null;
		if($default !== null) {
			$default_value = self::getColorValue(color: $default_value);

			$css_default = <<<CSS
			.$class_name{
				{$css_color_property_name}: $default_value;
			}
			CSS;
		}

		$css_disabled = null;
		if($disabled !== null) {
			$disabled_value = self::getColorValue(color: $disabled_value);

			$css_disabled = <<<CSS
			.{$class_name}:disabled {
				{$css_color_property_name}: $disabled_value;
			}
			CSS;
		}

		$css_hover = null;
		if($hover !== null) {
			$hover_value = self::getColorValue(color: $hover_value);

			$css_hover = <<<CSS
			.{$class_name}:hover {
				{$css_color_property_name}: $hover_value;
			}
			CSS;
		}

		$css_focus = null;
		if($focus !== null) {
			$focus_value = self::getColorValue(color: $focus_value);

			$css_focus = <<<CSS
			.{$class_name}:focus-within {
				{$css_color_property_name}: $focus_value;
			}
			CSS;
		}

		$css_pressed = null;
		if($pressed !== null) {
			$pressed_value = self::getColorValue(color: $pressed_value);

			$css_pressed = <<<CSS
			.{$class_name}.pressed {
				{$css_color_property_name}: $pressed_value;
			}
			CSS;
		}

		$css_toggled_default = null;
		if($toggled_default !== null) {
			$toggled_default_value = self::getColorValue(color: $toggled_default_value);

			$css_toggled_default = <<<CSS
			.{$class_name}.toggled {
				{$css_color_property_name}: $toggled_default_value;
			}
			CSS;
		}

		$css_toggled_hover = null;
		if($toggled_hover !== null) {
			$toggled_hover_value = self::getColorValue(color: $toggled_hover_value);

			$css_toggled_hover = <<<CSS
			.{$class_name}.toggled:hover {
				{$css_color_property_name}: $toggled_hover_value;
			}
			CSS;
		}

		$css_toggled_focus = null;
		if($toggled_focus !== null) {
			$toggled_focus_value = self::getColorValue(color: $toggled_focus_value);

			$css_toggled_focus = <<<CSS
			.{$class_name}.toggled:focus-within {
				{$css_color_property_name}: $toggled_focus_value;
			}
			CSS;
		}

		$css_toggled_pressed = null;
		if($toggled_pressed !== null) {
			$toggled_pressed_value = self::getColorValue(color: $toggled_pressed_value);

			$css_toggled_pressed = <<<CSS
			.{$class_name}.toggled.pressed {
				{$css_color_property_name}: $toggled_pressed_value;
			}
			CSS;
		}

		$color_states_css = new ColorStatesCss(
			default: $css_default,
			disabled: $css_disabled,
			hover: $css_hover,
			focus: $css_focus,
			pressed: $css_pressed,
			toggled_default: $css_toggled_default,
			toggled_hover: $css_toggled_hover,
			toggled_focus: $css_toggled_focus,
			toggled_pressed: $css_toggled_pressed,
		);

		return $color_states_css;
	}

	// When a unique ID is needed at a certain point; if already setted get it, if not set it and get it
	final protected function getId(string|null $props_id = null): string
	{
		if(!$props_id) {
			$props_id = 0;
		}

		if(!$this->common_props[$props_id]->id) {
			$this->common_props[$props_id]->id = uniqid();
		}

		return $this->common_props[$props_id]->id;
	}

	final protected function addClass(
		string $class_name,
		string|null $props_id = null,
	): string
	{
		if(!$props_id) {
			$props_id = 0;
		}

		if($this->common_props[$props_id]->class === null) {
			$this->common_props[$props_id]->class = $class_name;
		} else {
			$this->common_props[$props_id]->class .= " $class_name";
		}

		return $class_name;
	}

	/** @param string[] $class_names */
	final protected function addClasses(
		array $class_names,
		string|null $props_id = null,
	): void
	{
		foreach($class_names as $class_name) {
			$this->addClass(
				class_name: $class_name,
				props_id: $props_id,
			);
		}

		return;
	}

	final protected static function makeAttributes(): string
	{
		$id = $this->common_props[0]->id ?? uniqid();
		$class = $this->common_props[0]->class ?? null;
		$style = $this->common_props[0]->style ?? null;

		$attributes = " id=\"$id\"";

		if($class !== null) {
			$attributes .= " class=\"$class\"";
		}
		if($style !== null) {
			$attributes .= " style=\"$style\"";
		}

		return $attributes;
	}

	/** @var CommonProps|CommonProps[] $common_props */
	protected CommonProps|array $common_props = [];

	/** @param CommonProps|CommonProps[] $common_props */
	final protected function registerCommonProps(CommonProps|array $common_props): void
	{
		$this->common_props = $common_props;

		return;
	}

	final protected static function getHtml(Render $render): string
	{
		return Bundler::getHtml($render);
	}

	/** @var string[] $classes */
	protected array $classes = [];

	/** @param string|callable $css */
	final protected function addCss(
		string $class_name,
		string|callable $css,
		string|null $props_id = null,
	): string
	{
		self::addClass(
			class_name: $class_name,
			props_id: $props_id,
		);

		if(gettype($css) === "callable") {
			$css = $css($class_name);
		}

		$this->classes[$class_name] = $css;

		return $class_name;
	}

	/** @var string[] $scripts_before */
	protected array $scripts_before = [];

	final protected function addScriptBefore(
		string $script_name,
		string $script,
	): string
	{
		$this->scripts_before[$script_name] = $script;

		return $script_name;
	}

	final protected function makeRender(string $html = ""): Render
	{
		return new Render(
			html: $html,
			colors: $this->colors,
			typos: $this->typos,
			classes: $this->classes,
			scripts_before: $this->scripts_before,
		);
	}

	final protected function getCommonProps(string|null $props_id): CommonProps
	{
		if(!$props_id) {
			return $this->common_props[0];
		} else {
			return $this->common_props[$props_id];
		}
	}

	/** @var BackgroundColor|ForegroundColor[] $colors */
	protected array $colors = [];

	final protected function addColor(
		Palette|BackgroundColor|ForegroundColor|string $color,
		CssColorProperty $css_color_property,
		ColorType|null $color_type = null,
	): void
	{
		if(gettype($color) !== "string" && $color_type) {
			if($color_type === ColorType::FOREGROUND) {
				$color = $color->getForegroundColor();
			} else {
				$color = $color->getBackgroundColor();
			}
		}

		$color_name = self::getColorName(color: $color);
		$color_value = self::getColorValue(color: $color);

		if(gettype($color) !== "string") {
			$this->colors[$color->value] = $color;
		}

		$css_color_property_name = $css_color_property->value;

		$this->addCss(
			class_name: "{$css_color_property_name}_$color_name",
			css: function(string $class_name) use($css_color_property_name, $color_value): string {
				return <<<CSS
				.$class_name {
					$css_color_property_name: $color_value;
				}
				CSS;
			},
		);

		return;
	}

	/** @var string[] $typos */
	protected array $typos = [];

	final protected function addTypography(
		TypographyRole $role,
		TypographySubRole $sub_role,
	): void
	{
		$typography_css = self::getTypographyCss(
			role: $role,
			sub_role: $sub_role,
		);

		array_push($classes, ...$typography_css->classes);
		array_push($typos, ...$typography_css->fonts);

		return;
	}
}
