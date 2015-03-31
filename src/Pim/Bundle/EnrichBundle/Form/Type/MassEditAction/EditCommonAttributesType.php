<?php

namespace Pim\Bundle\EnrichBundle\Form\Type\MassEditAction;

use Pim\Bundle\CatalogBundle\Helper\LocaleHelper;
use Pim\Bundle\CatalogBundle\Repository\AttributeRepositoryInterface;
use Pim\Bundle\EnrichBundle\Form\View\ProductFormViewInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type of the EditCommonAttributes operation
 *
 * @author    Gildas Quemener <gildas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class EditCommonAttributesType extends AbstractType
{
    /** @var ProductFormViewInterface $productFormView */
    protected $productFormView;

    /** @var LocaleHelper $localeHelper */
    protected $localeHelper;

    /** @var string */
    protected $attributeClass;

    /** @var string */
    protected $localeClassName;

    /** @var string */
    protected $dataClass;

    /** @var AttributeRepositoryInterface */
    protected $attributeRepository;

    /**
     * @param ProductFormViewInterface     $productFormView
     * @param AttributeRepositoryInterface $attributeRepository
     * @param LocaleHelper                 $localeHelper
     * @param string                       $attributeClass
     * @param string                       $localeClassName
     * @param string                       $dataClass
     */
    public function __construct(
        ProductFormViewInterface $productFormView,
        AttributeRepositoryInterface $attributeRepository,
        LocaleHelper $localeHelper,
        $attributeClass,
        $localeClassName,
        $dataClass
    ) {
        $this->productFormView     = $productFormView;
        $this->attributeRepository = $attributeRepository;
        $this->localeHelper        = $localeHelper;
        $this->attributeClass      = $attributeClass;
        $this->localeClassName     = $localeClassName;
        $this->dataClass           = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'values',
                'pim_enrich_localized_collection',
                [
                    'type'               => 'pim_product_value',
                    'allow_add'          => false,
                    'allow_delete'       => true,
                    'by_reference'       => false,
                    'cascade_validation' => true,
                    'currentLocale'      => $options['current_locale']
                ]
            )
            ->add(
                'locale',
                'entity',
                [
                    'choices'   => $options['locales'],
                    'class'     => $this->localeClassName,
                    'select2'   => true,
                    'attr'      => [
                        'class' => 'operation-param',
                    ]
                ]
            )
            ->add(
                'displayedAttributes',
                'entity',
                [
                    'class'    => $this->attributeClass,
                    'choices'  => $options['common_attributes'],
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    'group_by' => 'group.label',
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        foreach ($view['locale']->vars['choices'] as $choice) {
            $choice->label = $this->localeHelper->getLocaleLabel($choice->label);
        }

        $view->vars['groups'] = $this->productFormView->getView();
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => $this->dataClass,
                'locales'           => [],
                'common_attributes' => $this->attributeRepository->findAll(),
                'current_locale'    => null
            ]
        );
    }

    /**
     * Initializes self::commonAtributes with values from the repository
     * Attribute is not available for mass editing if:
     *   - it is an identifier
     *   - it is unique
     *   - without value AND not link to family
     *   - is not common to every products
     *
     * @return array
     */
    public function getCommonAttributes()
    {
        return ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pim_enrich_mass_edit_common_attributes';
    }
}
