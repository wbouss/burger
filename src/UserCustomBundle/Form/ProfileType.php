<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UserCustomBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of ProfileType
 *
 * @author wbouss
 */
class ProfileType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('lastName', null, array('label' => 'form.lastName', 'translation_domain' => 'FOSUserBundle'))
                ->add('firstName', null, array('label' => 'form.firstName', 'translation_domain' => 'FOSUserBundle'))
                ->add('telephone', null, array('label' => 'telephone', 'translation_domain' => 'FOSUserBundle'))
                ->add('adresse', null, array('label' => 'form.adresse', 'translation_domain' => 'FOSUserBundle'))
                ->add('ville', null, array('label' => 'form.ville', 'translation_domain' => 'FOSUserBundle'))
                ->add('codepostale', null, array('label' => 'form.codepostale', 'translation_domain' => 'FOSUserBundle'))
                ->remove("current_password")
                ->remove("username");
    }

    public function getParent() {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix() {
        return 'app_user_profile';
    }

    // For Symfony 2.x
    public function getName() {
        return $this->getBlockPrefix();
    }

}
