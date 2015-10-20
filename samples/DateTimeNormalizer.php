<?php

namespace Acme\Serializer\Normalizer;

use DateTime;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * DateTime Normalizer
 */
class DateTimeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * Date format
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d';

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && $data instanceof DateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'DateTime';
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return $object->format($this->dateFormat);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        return new DateTime($data);
    }
}
