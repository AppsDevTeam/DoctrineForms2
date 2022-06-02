<?php

namespace ADT\DoctrineForms;

use ADT\DoctrineForms\Exceptions\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Nette\ComponentModel\IComponent;

class Form extends \ADT\Forms\Form
{
	protected ?EntityManagerInterface $entityManager = null;
	protected ?EntityFormMapper $entityMapper = null;
	protected ?Entity $entity = null;
	protected array $componentFormMappers = [];
	protected array $componentEntityMappers = [];
	protected array $componentEntityFactories = [];

	public function getEntityManager()
	{
		return $this->entityManager;
	}

	public function getEntityMapper(): EntityFormMapper
	{
		if ($this->entityMapper === NULL) {
			if (!$this->getEntityManager()) {
				throw new \Exception('Set entity manager first via setEntityManager() method.');
			}

			$this->entityMapper = new EntityFormMapper($this->getEntityManager(), $this);
		}

		return $this->entityMapper;
	}

	public function setEntityManager(EntityManagerInterface $entityManager): static
	{
		$this->entityManager = $entityManager;
		return $this;
	}

	public function setEntity(object $entity): static
	{
		if (!is_object($entity)) {
			throw new InvalidArgumentException('Expected object, ' . gettype($entity) . ' given.');
		}

		$this->entity = $entity;
		return $this;
	}

	public function getEntity(): ?Entity
	{
		return $this->entity;
	}

	public function mapToForm(): void
	{
		if (!$this->entity) {
			throw new \Exception('An entity is not set.');
		}

		$this->getEntityMapper()->load($this->entity, $this);
	}

	public function mapToEntity(): void
	{
		$this->getEntityMapper()->save($this->entity, $this);
	}

	public function getComponentFormMapper(IComponent $component): ?\Closure
	{
		return $this->componentFormMappers[spl_object_hash($component)] ?? null;
	}

	public function setComponentFormMapper(IComponent $component, \Closure $formMapper): static
	{
		$this->componentFormMappers[spl_object_hash($component)] = $formMapper;
		return $this;
	}

	public function getComponentEntityMapper(IComponent $component): ?\Closure
	{
		return $this->componentEntityMappers[spl_object_hash($component)] ?? null;
	}

	public function setComponentEntityMapper(IComponent $component, \Closure $entityMapper): static
	{
		$this->componentEntityMappers[spl_object_hash($component)] = $entityMapper;
		return $this;
	}

	public function getComponentEntityFactory(IComponent $component): ?\Closure
	{
		return $this->componentEntityFactories[spl_object_hash($component)] ?? null;
	}

	public function setComponentEntityFactory(IComponent $component, \Closure $entityFactory): static
	{
		$this->componentEntityFactories[spl_object_hash($component)] = $entityFactory;
		return $this;
	}
}
