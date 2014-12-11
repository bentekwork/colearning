<?php

namespace PetTrafficKing\StoreBundle\Controller;

use PetTrafficKing\StoreBundle\DependencyInjection\Stripe;
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
        $form = $this->createFormBuilder(null, array('attr' => array('id' => 'payment-form')))
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
                array('attr' => array('class' => 'form-control creditcard', 'data-stripe' => 'number'),
                )
            )
            ->add('exp-month', 'text',
                array('attr' => array('class' => 'form-control', 'data-stripe' => 'exp-month', 'placeholder' => 'MM', 'size' => 2),
                    'label' => 'Exp Month',
                    'block_name' => 'thirds',
                )
            )
            ->add('exp-year', 'text',
                array('attr' => array('class' => 'form-control', 'data-stripe' => 'exp-year', 'placeholder' => 'YYYY', 'size' => 4),
                    'label' => 'Exp Year',
                    'block_name' => 'thirds',
                )
            )
            ->add('cvv', 'text',
                array('attr' => array('class' => 'form-control', 'data-stripe' => 'cvv'),
                    'block_name' => 'thirds',
                    'label' => 'CVV',
                )
            )
            ->add('token', 'hidden',
                array('attr' => array('class' => 'form-control token'))
            )
            ->add('submit', 'submit',
                array('label' => 'Purchase')
            )
            ->getForm()
        ;

        $form->handleRequest($request);

        if($form->isValid()){

            $data = $form->getData();
            $charge = $this->get('stripe');
            $response = $charge->processPayment($pet->getPrice(), $data['token']);
            if(isset($response['error'])){
                $request->getSession()->getFlashBag()->add(
                    'error',
                    'Oh No! '. $response['error']
                );
            } else {
                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'Thanks, your pet will be smuggled ... ahem, delivered to you soon!'
                );
                return $this->redirect($this->generateUrl(''));
            }

        }


        return array('pet' => $pet, 'form' => $form->createView());
    }
}
