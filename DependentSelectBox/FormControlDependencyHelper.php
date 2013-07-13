<?php

/**
 * @author Daniel Robenek
 * @license MIT
 */

namespace DependentSelectBox;

use Nette\Forms\Container,
	Nette\Forms\Controls\BaseControl as FormControl,
	Nette\Forms\Controls\SubmitButton,
	\InvalidArgumentException,
	Nette\InvalidStateException;

class FormControlDependencyHelper extends \Nette\Object
{

	/** @var string Suffix for button name and html class */
	public static $buttonSuffix = "_submit";

	/** @var FormControl */
	public $control;
	/** @var String Html class of control*/
	protected $controlClass;
	/** @var int Button-s position */
	protected $buttonPosition;
	/** @var SubmitButton Created SubmitButton */
	protected $button = null;

	protected $container;

	/**
	 *
	 * @param FormControl $control Component to attach button to
	 * @param string $controlClass HTML class for that component
	 */
	function __construct(FormControl $control, $controlClass = "dependentControl")
	{
		$this->control = $control;
		$this->controlClass = $controlClass;

		$this->container = $this->control->lookup('Nette\Forms\Container');
		if($this->container === NULL) {
			throw new InvalidArgumentException("Attach your form to the component hierarchy.");
		}
	}


	/**
	 * Add callback which is called when linked button is submitted
	 * @param callback $callback
	 * 'public function methodName(SubmittButton $button)'
	 */
	public function addOnChangeCallback($callback)
	{
		$this->control->getControlPrototype()->class($this->controlClass); // Mark parent as the deciding control
		$this->createButton();

		// Attach button
		$buttonName = $this->control->getName() . self::$buttonSuffix;
		$this->container->addComponent($this->button, $buttonName);

		$this->button->onClick[] = $callback;
	}


	public function createButton()
	{
		$this->button = new SubmitButton('Load');
		$this->button->setValidationScope(FALSE);
		$this->button->getControlPrototype()->class($this->controlClass . self::$buttonSuffix);
	}

}
