<?php

namespace Pim\Bundle\EnrichBundle\MassEditAction\Operation;

use Doctrine\Common\Collections\Collection;
use Pim\Bundle\CatalogBundle\Model\ProductInterface;

/**
 * Edit common attributes of given products
 *
 * @author    Gildas Quemener <gildas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class EditCommonAttributes extends AbstractMassEditOperation implements
    ConfigurableOperationInterface,
    BatchableOperationInterface
{
    /** @var ProductInterface */
    protected $values;

    /**
     * Set values
     *
     * @param Collection $values
     *
     * @return EditCommonAttributes
     */
    public function setValues(Collection $values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get values
     *
     * @return Collection
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType()
    {
        return 'pim_enrich_mass_edit_common_attributes';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'edit-common-attributes';
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return [
            [
                'field' => 'attribute',
                'value' => $this->getAttrbibute()->getCode(),
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getBatchConfig()
    {
        return addslashes(
            json_encode(
                [
                    'filters' => $this->getFilters(),
                    'actions' => $this->getActions()
                ]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBatchJobCode()
    {
        return 'update_product_value';
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOptions()
    {
        return [];
    }
}
