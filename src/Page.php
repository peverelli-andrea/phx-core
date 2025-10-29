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

		return $id;
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
		} else if(gettype($head) === "callable") {
			$head = <<<HTML
			{$head(css: Bundler::getCss(bundle: $bundle))}
			HTML;
		}

		return <<<HTML
		<!DOCTYPE html>
		<html>
			<head>
				$head
			</head>
			<body>
				{Bundler::getScriptsBefore(bundle: $bundle)}
				$body
				{Bundler::getScriptsAfter(bundle: $bundle)}
			</body>
		</html>
		HTML;
	}

	final protected function getHtml(string $id): string
	{
		return $this->component[$id]->html;
	}
}
