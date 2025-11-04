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

	final protected function getColorStatesCss(
		ColorStates $color_states,
		CssColorProperty $css_color_property,
		string $main_class_name,
		string $component_class_name = "",
		BackgroundColor|ForegroundColor|null $state_color = null,
		BackgroundColor|ForegroundColor|null $toggled_state_color = null,
		bool $generate_default = true,
	): string
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

		$palette_config = Bundler::readPaletteConfig();

		if($state_color) {
			$state_color_palette_set = Bundler::getPaletteSet(
				color: $state_color,
				palette_config: $palette_config,
			);
		}

		if($toggled_state_color) {
			$toggled_state_color_palette_set = Bundler::getPaletteSet(
				color: $toggled_state_color,
				palette_config: $palette_config,
			);
		}

		$color_states_css = "";

		if($component_class_name !== "") {
			$component_class_name = " .$component_class_name";
		}

		if($default !== null) {
			$default_palette_set = Bundler::getPaletteSet(
				color: $default,
				palette_config: $palette_config,
			);
			$this->colors[$default->value] = $default;

			$default_value = self::getColorValue(color: $default);

			if($generate_default) {
				$color_states_css .= <<<CSS
				.$main_class_name$component_class_name {
					{$css_color_property_name}: $default_value;
				}
				CSS;
			}
		}

		if($disabled !== null) {
			$this->colors[$disabled->value] = $disabled;

			$disabled_value = self::getColorValue(color: $disabled);

			$color_states_css  .= <<<CSS
			.{$main_class_name}:disabled$component_class_name {
				{$css_color_property_name}: $disabled_value;
			}
			CSS;
		}

		if($hover !== null) {
			if($hover !== true) {
				$this->colors[$hover->value] = $hover;

				$hover_value = self::getColorValue(color: $hover);
			}

			$media_query = ".{$main_class_name}:hover$component_class_name";
			if($hover === true) {
				$color_states_css .= self::getResponsiveColorCss(
					palette_set: self::sumPaletteSet(
						background: $default_palette_set,
						foreground: $state_color_palette_set,
						alpha: 0.08,
					),
					media_query: $media_query,
					css_color_property: $css_color_property,
				);
			} else {
				$color_states_css .= <<<CSS
				$media_query {
					{$css_color_property->value}: $hover_value;
				}
				CSS;
			}
		}

		if($focus !== null) {
			if($focus !== true) {
				$this->colors[$focus->value] = $focus;

				$focus_value = self::getColorValue(color: $focus);
			}

			$media_query = ".{$main_class_name}:focus-within$component_class_name";
			if($focus === true) {
				$color_states_css .= self::getResponsiveColorCss(
					palette_set: self::sumPaletteSet(
						background: $default_palette_set,
						foreground: $state_color_palette_set,
						alpha: 0.1,
					),
					media_query: $media_query,
					css_color_property: $css_color_property,
				);
			} else {
				$color_states_css .= <<<CSS
				$media_query {
					{$css_color_property->value}: $focus_value;
				}
				CSS;
			}
		}

		if($pressed !== null) {
			if($pressed !== true){
				$this->colors[$pressed->value] = $pressed;

				$pressed_value = self::getColorValue(color: $pressed);
			}

			$media_query = ".{$main_class_name}.pressed$component_class_name";
			if($pressed === true) {
				$color_states_css .= self::getResponsiveColorCss(
					palette_set: self::sumPaletteSet(
						background: $default_palette_set,
						foreground: $state_color_palette_set,
						alpha: 0.1,
					),
					media_query: $media_query,
					css_color_property: $css_color_property,
				);
			} else {
				$color_states_css .= <<<CSS
				$media_query {
					{$css_color_property->value}: $pressed_value;
				}
				CSS;
			}
		}

		if($toggled_default !== null) {
			$toggled_default_palette_set = Bundler::getPaletteSet(
				color: $toggled_default,
				palette_config: $palette_config,
			);

			$this->colors[$toggled_default->value] = $toggled_default;

			$toggled_default_value = self::getColorValue(color: $toggled_default);

			$color_states_css .= <<<CSS
			.{$main_class_name}.toggled$component_class_name {
				{$css_color_property_name}: $toggled_default_value;
			}
			CSS;
		}

		if($toggled_hover !== null) {
			if($toggled_hover !== true) {
				$this->colors[$toggled_hover->value] = $toggled_hover;

				$toggled_hover_value = self::getColorValue(color: $toggled_hover);
			}

			$media_query = ".{$main_class_name}.toggled:hover$component_class_name";
			if($toggled_hover === true) {
				$color_states_css .= self::getResponsiveColorCss(
					palette_set: self::sumPaletteSet(
						background: $toggled_default_palette_set,
						foreground: $toggled_state_color_palette_set,
						alpha: 0.08,
					),
					media_query: $media_query,
					css_color_property: $css_color_property,
				);
			} else {
				$color_states_css .= <<<CSS
				$media_query {
					{$css_color_property->value}: $toggled_hover_value;
				}
				CSS;
			}
		}

		if($toggled_focus !== null) {
			if($toggled_focus !== true) {
				$this->colors[$toggled_focus->value] = $toggled_focus;

				$toggled_focus_value = self::getColorValue(color: $toggled_focus);
			}

			$media_query = ".{$main_class_name}.toggled:focus-within$component_class_name";
			if($toggled_focus === true) {
				$color_states_css .= self::getResponsiveColorCss(
					palette_set: self::sumPaletteSet(
						background: $toggled_default_palette_set,
						foreground: $toggled_state_color_palette_set,
						alpha: 0.1,
					),
					media_query: $media_query,
					css_color_property: $css_color_property,
				);
			} else {
				$color_states_css .= <<<CSS
				$media_query {
					{$css_color_property->value}: $toggled_focus_value;
				}
				CSS;
			}
		}

		if($toggled_pressed !== null) {
			if($toggled_pressed !== true) {
				$this->colors[$toggled_pressed->value] = $toggled_pressed;

				$toggled_pressed_value = self::getColorValue(color: $toggled_pressed);
			}

			$media_query = ".{$main_class_name}.toggled.pressed$component_class_name";
			if($toggled_pressed === true) {
				$color_states_css .= self::getResponsiveColorCss(
					palette_set: self::sumPaletteSet(
						background: $toggled_default_palette_set,
						foreground: $toggled_state_color_palette_set,
						alpha: 0.1,
					),
					media_query: $media_query,
					css_color_property: $css_color_property,
				);
			} else {
				$color_states_css .= <<<CSS
				$media_query {
					{$css_color_property->value}: $toggled_pressed_value;
				}
				CSS;
			}
		}

		return $color_states_css;
	}

	private static function getResponsiveColorCss(
		PaletteSet $palette_set,
		string $media_query,
		CssColorProperty $css_color_property,
	): string
	{
		return <<<CSS
		$media_query {
			{$css_color_property->value}: {$palette_set->light_normal};
		}
		
		@media (prefers-contrast: less) {
			$media_query {
				{$css_color_property->value}: {$palette_set->light_medium};
			}
		}

		@media (prefers-contrast: more) {
			$media_query {
				{$css_color_property->value}: {$palette_set->light_high};
			}
		}

		@media (prefers-color-scheme: dark) {
			$media_query {
				{$css_color_property->value}: {$palette_set->dark_normal};
			}

			@media (prefers-contrast: less) {
				$media_query {
					{$css_color_property->value}: {$palette_set->dark_medium};
				}
			}

			@media (prefers-contrast: more) {
				$media_query {
					{$css_color_property->value}: {$palette_set->dark_high};
				}
			}
		}
		CSS;
	}

	private static function sumPaletteSet(
		PaletteSet $background,
		PaletteSet $foreground,
		float $alpha,
	): PaletteSet
	{
		return new PaletteSet(
			light_normal: self::sumColors(
				background: $background->light_normal,
				foreground: $foreground->light_normal,
				alpha: $alpha,
			),
			light_medium: self::sumColors(
				background: $background->light_medium,
				foreground: $foreground->light_medium,
				alpha: $alpha,
			),
			light_high: self::sumColors(
				background: $background->light_high,
				foreground: $foreground->light_high,
				alpha: $alpha,
			),
			dark_normal: self::sumColors(
				background: $background->dark_normal,
				foreground: $foreground->dark_normal,
				alpha: $alpha,
			),
			dark_medium: self::sumColors(
				background: $background->dark_medium,
				foreground: $foreground->dark_medium,
				alpha: $alpha,
			),
			dark_high: self::sumColors(
				background: $background->dark_high,
				foreground: $foreground->dark_high,
				alpha: $alpha,
			),
		);
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

	final protected function makeAttributes(): string
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
		if(gettype($common_props) === "array") {
			$this->common_props = $common_props;
		} else {
			$this->common_props[0] = $common_props;
		}

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

		if(gettype($css) === "object") {
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

	/** @var string[] $scripts_after */
	protected array $scripts_after = [];

	final protected function addScriptAfter(
		string $script_name,
		string $script,
	): string
	{
		$this->scripts_after[$script_name] = $script;

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
			scripts_after: $this->scripts_after,
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
		if($color instanceof Palette && $color_type) {
			if($color_type === ColorType::FOREGROUND) {
				$color = $color->getForeground();
			} else {
				$color = $color->getBackground();
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

		$this->classes = array_merge($this->classes, $typography_css->classes);
		$this->typos = array_merge($this->typos, $typography_css->fonts);

		$this->addClasses(class_names: array_keys($typography_css->classes));

		return;
	}

	/** @param mixed[] $props */
	final protected function newComponent(
		string $component,
		array $props,
	): string
	{
		$props_class = "{$component}Props";
		$reflection = new \ReflectionClass($props_class);
		$constructor = $reflection->getConstructor();

		$params = [];
		foreach ($constructor->getParameters() as $parameter) {
			$name = $parameter->getName();
			$params[] = $props[$name] ?? $parameter->getDefaultValue();
		}

		$render = (new $component())->render(props: $reflection->newInstanceArgs($params));

		$this->classes = array_merge($this->classes, $render->classes);
		$this->colors = array_merge($this->colors, $render->colors);
		$this->typos = array_merge($this->typos, $render->typos);
		$this->scripts_before = array_merge($this->scripts_before, $render->scripts_before);
		$this->scripts_after = array_merge($this->scripts_after, $render->scripts_after);

		return $render->html;
	}

	private static function sumColors(
		string $background,
		string $foreground,
		float $alpha,
	): string
	{
		// Hex → RGB (0–255)
		$parseHex = function (string $hex): array {
			$hex = ltrim($hex, '#');

			return [
				hexdec(substr($hex, 0, 2)),
				hexdec(substr($hex, 2, 2)),
				hexdec(substr($hex, 4, 2)),
			];
		};

		$background = $parseHex($background);
		$foreground = $parseHex($foreground);

		// sRGB → linear-light (IEC 61966-2-1)
		$toLinear = static function (float $value): float {
			$value /= 255.0;

			return $value <= 0.04045 ? $value / 12.92 : pow(($value + 0.055) / 1.055, 2.4);
		};

		// linear-light → sRGB
		$toSrgb = static function (float $value): float {
			return $value <= 0.0031308 ? $value * 12.92 : 1.055 * pow($value, 1 / 2.4) - 0.055;
		};

		// Blend per channel in *linear space*
		$out = [];
		for ($i = 0; $i < 3; $i++) {
			$linear_background = $toLinear($background[$i]);
			$linear_foreground = $toLinear($foreground[$i]);
			$linear_sum = $linear_foreground * $alpha + $linear_background * (1 - $alpha);

			// Convert back to sRGB and clamp
			$srgb = max(0.0, min(1.0, $toSrgb($linear_sum)));
			$out[$i] = round($srgb * 255);
		}

		return sprintf("#%02X%02X%02X", $out[0], $out[1], $out[2]);
	}
}
