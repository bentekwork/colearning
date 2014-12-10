<?php

namespace PetTrafficKing\StoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Luhn;

class DefaultController extends Controller
{
    /**
     * @Route("/pet/{pet_id}/buy")
     * @Template()
     */
    public function buyAction(Request $request, $pet_id)
    {
        $pet = $this->getDoctrine()->getRepository('PetTrafficKingStoreBundle:Pet')->find($pet_id);
        $form = $this->createFormBuilder()
            ->add('firstname', 'text',
                array('attr' => array('class' => 'form-control'),
                'label' => 'First',
                'block_name' => 'half',
                )
            )
            ->add('lastname', 'text',
                array('attr' => array('class' => 'form-control'),
                'label' => 'Last',
                'block_name' => 'half',

                )
            )
            ->add('email', 'email',
                array('attr' => array('class' => 'form-control'),
                    'constraints' => array(
                        new Email(),
                    ),
                )
            )
            ->add('address', 'text',
                array('attr' => array('class' => 'form-control'))
            )
            ->add('city', 'text',
                array(
                    'attr' => array('class' => 'form-control', 'placeholder' => 'City'),
                    'block_name' => 'thirds',
                )
            )
            ->add('state', 'choice',
                array(
                    'attr' => array('class' => 'form-control'),
                    'block_name' => 'thirds',
                    'choices' => array('Michigan', 'Minnesota', 'Missouri')
                )
            )
            ->add('zip', 'text',
                array(
                    'attr' => array('class' => 'form-control'),
                    'block_name' => 'thirds',
                )
            )
            ->add('creditcard', 'text',
                array('attr' => array('class' => 'form-control'),
                    'constraints' => array(
                        new Luhn(),
                    ),
                )
            )
            ->add('submit', 'submit',
                array('label' => 'Purchase')
            )
            ->getForm()
        ;

        $form->handleRequest($request);

        if($form->isValid()){
            var_dump($form->getData());
        }


        return array('pet' => $pet, 'form' => $form->createView());
    }
}
