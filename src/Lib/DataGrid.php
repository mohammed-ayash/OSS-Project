<?php

namespace App\Lib;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

abstract class DataGrid
{
    protected $em;
    protected $formFilterData;

    function __construct($em)
    {
        $this->em = $em;
    }

    public function getFilterArray($form)
    {
        $out = [];

        foreach ($form as $element) {
            $elementName = $element->getName();
            $innerType = $element->getConfig()->getType()->getInnerType();
            if ($innerType instanceof EntityType) {
                $out[$elementName] = $this->getFormFilterEntityIds($element->getData());
            } else {
                $out[$elementName] = $element->getData();
            }
        }
        return $out;
    }


    public function filterItemHasData()
    {
        return $this->em;
    }

    public function getEncodedFilterArray($form)
    {
        return $this->encodeFilterArray( $this->getFilterArray($form) );
    }

    public function encodeFilterArray($filterArray)
    {
        return base64_encode(serialize($filterArray));
    }

    public function decodeFilterArray($encodedFilerArray)
    {
        return unserialize(base64_decode($encodedFilerArray));
    }

    public function setFormFilterData($form, $formFilterData)
    {
        $this->formFilterData = $formFilterData;
        foreach ($formFilterData as $elementName => $value) {
            $element = $form->get($elementName);
            $innerType = $element->getConfig()->getType()->getInnerType();
            if ($innerType instanceof EntityType) {
                $dataClass = $element->getConfig()->getOption('class');
                $element->setData($this->getEntityArrayFromIds($value, $dataClass));
            } else {
                $element->setData($value);
            }
        }
        return $form;
    }

    private function getFormFilterEntityIds($array)
    {
        if(is_array($array)) {
            $out = [];
            foreach ($array as $entity) {
                $out[] = $entity->getId();
            }
            return $out;
        }else{
            return $array;
        }
    }

    private function getEntityArrayFromIds($ids, $className)
    {
        if(is_array($ids)) {
            $out = [];
            foreach ($ids as $id) {
                $entity = $this->em->getRepository($className)->find($id);
                $out[] = $entity;
            }
            return $out;
        }else{
            if($ids) {
                return $this->em->getRepository($className)->find($ids);
            }
        }
    }

    public function getFormFilterData()
    {
        return $this->formFilterData;
    }

    public function getFormDataElement($name)
    {
        $formFilterData = $this->getFormFilterData();
        if (is_array($formFilterData)) {
            if (array_key_exists($name, $formFilterData)) {
                $value = $formFilterData[$name];
                if (is_array($value)) {
                    if (sizeof($value)) {
                        return $value;
                    }
                } else {
                    return $value;
                }
            }
        }
    }

    public function parseDateTime($dateTime)
    {
        try {
            if ($dateTime) {
                $dateTime = new \DateTime($dateTime);
                return $dateTime;
            }
        } catch (\Exception $e) {
        }
    }

    protected function commaDelimitedToArray($elementValue)
    {
        $ids = explode(",", $elementValue);
        $ids = array_map('trim', $ids);
        return $ids;
    }
}
