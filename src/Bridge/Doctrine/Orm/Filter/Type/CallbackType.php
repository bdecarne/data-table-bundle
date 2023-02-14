<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface as BaseProxyQueryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallbackType extends AbstractType
{
    /**
     * @param ProxyQueryInterface $query
     */
    public function apply(BaseProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
        $options['callback']($query, $data, $filter);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('operator_options', [
                'visible' => false,
                'choices' => [],
            ])
            ->setRequired('callback')
            ->setAllowedTypes('callback', ['callable'])
        ;
    }
}