<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationData
{
    /**
     * @param array<PersonalizationColumnData> $columns
     */
    private function __construct(
        private array $columns,
    ) {
    }

    public static function fromArray(array $data): static
    {
        ($resolver = new OptionsResolver())
            ->setDefault('columns', [])
            ->setAllowedTypes('columns', 'array[]')
        ;

        $data = $resolver->resolve($data);

        $columns = array_map(
            fn (array $column) => PersonalizationColumnData::fromArray($column),
            $data['columns'],
        );

        return new static($columns);
    }

    public static function fromDataTable(DataTableInterface $dataTable): static
    {
        $columns = [];

        foreach (array_values($dataTable->getConfig()->getColumns()) as $index => $column) {
            $columns[$column->getName()] = PersonalizationColumnData::fromColumn($column, $index);
        }

        return new static($columns);
    }

    /**
     * Computes given set of {@see ColumnInterface}, ordering it and excluding hidden ones.
     *
     * @param array<ColumnInterface> $columns
     *
     * @return array<ColumnInterface>
     */
    public function compute(array $columns): array
    {
        foreach ($columns as $column) {
            if (!$column instanceof ColumnInterface) {
                throw new UnexpectedTypeException($column, ColumnInterface::class);
            }
        }

        $columns = array_filter($columns, function (ColumnInterface $column) {
            return $this->isColumnVisible($column);
        });

        uasort($columns, function (ColumnInterface $columnA, ColumnInterface $columnB) {
            return $this->getColumnOrder($columnA) <=> $this->getColumnOrder($columnB);
        });

        return $columns;
    }

    /**
     * @return array<PersonalizationColumnData>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getColumnOrder(string|ColumnInterface $column): int
    {
        return $this->getColumn($column)?->getOrder() ?? 0;
    }

    public function setColumnOrder(string|ColumnInterface $column, int $order): self
    {
        $this->getColumn($column)?->setOrder($order);

        return $this;
    }

    public function getColumnVisibility(string|ColumnInterface $column): bool
    {
        return $this->getColumn($column)?->isVisible() ?? true;
    }

    public function setColumnVisibility(string|ColumnInterface $column, bool $visible): self
    {
        $this->getColumn($column)?->setVisible($visible);

        return $this;
    }

    public function isColumnVisible(string|ColumnInterface $column): bool
    {
        return true === $this->getColumnVisibility($column);
    }

    public function setColumnVisible(string|ColumnInterface $column): self
    {
        return $this->setColumnVisibility($column, true);
    }

    public function isColumnHidden(string|ColumnInterface $column): bool
    {
        return false === $this->getColumnVisibility($column);
    }

    public function setColumnHidden(string|ColumnInterface $column): self
    {
        return $this->setColumnVisibility($column, false);
    }

    private function getColumn(string|ColumnInterface $column): ?PersonalizationColumnData
    {
        if ($column instanceof ColumnInterface) {
            $column = $column->getName();
        }

        return $this->columns[$column] ?? null;
    }
}
