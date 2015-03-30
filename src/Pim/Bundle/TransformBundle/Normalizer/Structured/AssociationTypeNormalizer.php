<?php

namespace Pim\Bundle\TransformBundle\Normalizer\Structured;

use Pim\Bundle\CatalogBundle\Model\AssociationTypeInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Association type normalizer
 *
 * @author    Filips Alpe <filips@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AssociationTypeNormalizer implements NormalizerInterface
{
    /**
     * @var array $supportedFormats
     */
    protected $supportedFormats = ['json', 'xml', 'internal_api'];

    /**
     * @var TranslationNormalizer $transNormalizer
     */
    protected $transNormalizer;

    /**
     * Constructor
     *
     * @param TranslationNormalizer $transNormalizer
     */
    public function __construct(TranslationNormalizer $transNormalizer)
    {
        $this->transNormalizer = $transNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $result = [
            'code'  => $object->getCode()
        ] + $this->transNormalizer->normalize($object, $format, $context);

        if ('internal_api' === $format) {
            $result['id'] = $object->getId();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AssociationTypeInterface && in_array($format, $this->supportedFormats);
    }
}
