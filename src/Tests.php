<?php

namespace AndreaPeverelli\PhxCore;

require_once("../vendor/autoload.php");

echo "#######################\n";
echo "# PHX-CORE UNIT TESTS #\n";
echo "#######################\n";

TestSuite::init("Component");

final class TestComponentProps {
	public function __construct(public CommonProps $common_props) {}
}

final class TestComponent extends Component {
	final public function __construct()
	{
		$id = uniqid();

		$this->registerComponent(props: new TestComponentProps(common_props: new CommonProps(
			id: $id,
			classes: ["class1", "class2"],
			css: ["css1", "css2"],
			attributes: ["attribute1" => "test1", "attribute2" => "test2"],
		)));

		$expect = [
			"props" => ["default" => new TestComponentProps(common_props: new CommonProps(
				id: $id,
				classes: ["class1", "class2"],
				css: ["css1", "css2"],
				attributes: ["attribute1" => "test1", "attribute2" => "test2"],
			))],
			"attributes" => ["default" => ""],
			"classes" => ["default" => ["class1", "class2"]],
			"css" => ["default" => ["css1", "css2"]],
		];

		$got = [
			"props" => $this->props,
			"attributes" => $this->attributes,
			"classes" => $this->classes,
			"css" => $this->css,
		];

		TestSuite::test(
			test_name: "registerComponent",
			got: $got,
			expect: $expect,
		);

		$this->makeAttributes();

		$expect = " id=\"$id\" class=\"class1 class2\" attribute1=\"test1\" attribute2=\"test2\"";
		$got = $this->attributes["default"];

		TestSuite::test(
			test_name: "makeAttributes",
			got: $got,
			expect: $expect,
		);
	}
}

new TestComponent();
