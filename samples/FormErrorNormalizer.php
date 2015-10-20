<?php

namespace Acme\Serializer\Normalizer;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * Form Error Normalizer
 */
class FormErrorNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && ($data instanceof FormError || $data instanceof FormErrorIterator);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if ($object instanceof FormErrorIterator) {
            $errors = [];

            foreach ($object as $key => $error) {
                $errors[] = $this->normalizeError($error);
            }

            return $errors;
        }

        return $this->normalizeError($object);
    }

    /**
     * Normalize error
     *
     * @param FormError $error
     *
     * @return array
     */
    private function normalizeError(FormError $error)
    {
        return [
            'message'    => $error->getMessage(),
            'parameters' => $error->getMessageParameters(),
            'plural'     => $error->getMessagePluralization(),
            'code'       => $error->getMessageTemplate(),
            'path'       => $this->getPath($error),
        ];
    }

    /**
     * Get path for the given error
     *
     * @param FormError $error
     *
     * @return string
     */
    private function getPath(FormError $error)
    {
        if ($error->getCause() instanceof ConstraintViolation) {
            return $error->getCause()->getPropertyPath();
        }

        return null;
    }
}
