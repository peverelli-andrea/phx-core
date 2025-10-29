<?php

namespace Phx\Core;

abstract class Page
{
	/** @var Render[] $components */
	protected array $components = [];

	final protected function registerComponent(
		string $id,
		Render $render,
	): string
	{
		$this->components[$id] = $render;

		return $render->html;
	}

	/** @param string|callable|null $head */
	final protected function makeRender(
		string $body,
		string|callable|null $head = null,
	): string
	{
		$bundle = Bundler::make(component_renders: $this->components);

		if(!$head) {
			$head = <<<HTML
			<style>
				{Bundler::getCss(bundle: $bundle)}
			</style>
			HTML;
		} else if(gettype($head) === "object") {
			$head = $head(css: Bundler::getCss(bundle: $bundle));
		}

		$scripts_before = Bundler::getScriptsBefore(bundle: $bundle);
		$scripts_after = Bundler::getScriptsAfter(bundle: $bundle);

		return <<<HTML
		<!DOCTYPE html>
		<html>
			<head>
				$head
			</head>
			<body>
				$scripts_before
				$body
				$scripts_after
			</body>
		</html>
		HTML;
	}
}
