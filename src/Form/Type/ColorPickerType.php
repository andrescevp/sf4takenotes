<?php
/**
 * Created by PhpStorm.
 * User: andres
 * Date: 15/01/19
 * Time: 23:41
 */

namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ColorPickerType extends AbstractType
{
    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'color_picker';
    }
}