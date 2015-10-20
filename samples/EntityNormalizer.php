<?php

namespace Acme\Serializer\Normalizer;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Proxy;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

/**
 * Entity Normalizer
 */
class EntityNormalizer extends SerializerAwareNormalizer implements NormalizerInterface
{
    /**
     * Entity Manager
     *
     * @var ObjectManager
     */
    protected $entityManager;

    /**
     * Property accessor
     *
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    /**
     * Max depth
     *
     * @var integer
     */
    protected $maxDepth = 1;

    /**
     * Consturctor
     *
     * @param ObjectManager $entityManager
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(ObjectManager $entityManager, PropertyAccessorInterface $propertyAccessor)
    {
        $this->entityManager    = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && $this->hasMetadataFor($data);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if (!isset($context['depth'])) {
            $context['depth'] = 0;
        }

        if ($context['depth'] >= $this->maxDepth) {
            return $this->getObjectReference($object, $format, $context);
        }

        $data = [];

        foreach ($this->getMetadataFor($object)->getAttributesMetadata() as $attributeMetadata) {
            $property = $attributeMetadata->getName();

            if ($this->propertyAccessor->isReadable($object, $property)) {
                $data[$property] = $this->getObjectPropety($object, $property, $format, $context);
            }
        }

        return $data;
    }

    /**
     * Get the value of the given object property
     *
     * @param mixed $object
     * @param string $property
     * @param string $format
     * @param array $context
     *
     * @return mixed
     */
    protected function getObjectPropety($object, $property, $format = null, array $context = array())
    {
        $value = $this->propertyAccessor->getValue($object, $property);

        if ($value !== null && !is_scalar($value)) {
            return $this->serializer->normalize(
                $value,
                $format,
                array_merge(
                    $context,
                    ['depth' => $context['depth'] + 1]
                )
            );
        }

        return $value;
    }

    /**
     * Get object reference
     *
     * @param object $object
     * @param string $format
     * @param array $context
     *
     * @return array|integer|string
     */
    protected function getObjectReference($object, $format = null, array $context = array())
    {
        // return $this->getMetadataFor($object)->getIdentifierValues($object);
    }

    /**
     * Has the given class or object class metadata?
     *
     * @param string|object $classOrObject
     *
     * @return boolean
     */
    protected function hasMetadataFor($classOrObject)
    {
        /*if ($classOrObject instanceof Proxy) {
            $classOrObject = get_parent_class($classOrObject);
        }

        return $this->classMetadataFactory->hasMetadataFor($classOrObject);*/
    }

    /**
     * Get class metadata for the given class or object
     *
     * @param string|object $classOrObject
     *
     * @return \Symfony\Component\Serializer\Mapping\ClassMetadataInterface
     */
    protected function getMetadataFor($classOrObject)
    {
        /*if ($classOrObject instanceof Proxy) {
            $classOrObject = get_parent_class($classOrObject);
        }

        return $this->entityManager->getMetadataFactory()->getMetadataFor($classOrObject);*/
    }
}
