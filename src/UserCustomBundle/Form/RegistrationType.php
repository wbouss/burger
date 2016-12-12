<?php

namespace UserCustomBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('lastName', null, array('label' => 'form.lastName', 'translation_domain' => 'FOSUserBundle'))
                ->add('firstName', null, array('label' => 'form.firstName', 'translation_domain' => 'FOSUserBundle'))
                ->add('telephone', null, array('label' => 'telephone', 'translation_domain' => 'FOSUserBundle'))
                ->add('adresse', null, array('label' => 'form.adresse', 'translation_domain' => 'FOSUserBundle'))
                ->add('ville', null, array('label' => 'form.ville', 'translation_domain' => 'FOSUserBundle'))
                ->add('codepostale', null, array('label' => 'form.codepostale', 'translation_domain' => 'FOSUserBundle'))
                ->add('informationComplementairesAdresse', null, array("required" => false, 'label' => 'form.codepostale', 'translation_domain' => 'FOSUserBundle'));
    }

    public function getParent() {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix() {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName() {
        return $this->getBlockPrefix();
    }

}
