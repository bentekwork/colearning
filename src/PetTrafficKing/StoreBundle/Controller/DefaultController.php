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
     * @Route("/pet/{pet_id}/buy", name="buy")
     * @Template()
     */
    public function buyAction(Request $request, $pet_id)
    {
        $pet = $this->getDoctrine()->getRepository('PetTrafficKingStoreBundle:Pet')->find($pet_id);
        $form = $this->createFormBuilder(null, array('attr' => array('id' => 'payment-form')))
            ->add('firstname', 'text',
                array(
                    'label' => 'First',
                    'block_name' => 'half',
                )
            )
            ->add('lastname', 'text',
                array(
                    'label' => 'Last',
                    'block_name' => 'half',
                )
            )
            ->add('email', 'email', array('constraints' => array(new Email())))
            ->add('address', 'text')
            ->add('city', 'text',
                array(
                    'attr' => array('placeholder' => 'City'),
                    'block_name' => 'thirds',
                )
            )
            ->add('state', 'text',
                array(
                    'block_name' => 'thirds',
                )
            )
            ->add('zip', 'text',
                array(
                    'block_name' => 'thirds',
                )
            )
            ->add('creditcard', 'text',
                array('attr' => array('class' => 'form-control creditcard', 'data-stripe' => 'number'),

                )
            )
            ->add('expmonth', 'text',
                array(
                    'attr' => array('data-stripe' => 'exp-month'),
                    'label' => 'Expiration Month',
                    'block_name' => 'thirds',
                )
            )
            ->add('expyear', 'text',
                array(
                    'attr' => array('data-stripe' => 'exp-year'),
                    'label' => 'Expiration Year',
                    'block_name' => 'thirds',
                )
            )
            ->add('cvv', 'text',
                array(
                    'attr' => array('data-stripe' => 'cvc'),
                    'label' => 'CVV',
                    'block_name' => 'thirds',

                )
            )
            ->add('token', 'hidden', array('attr' => array('class' => 'token')))
            ->add('submit', 'submit',
                array('label' => 'Purchase')
            )
            ->getForm()
        ;

        $form->handleRequest($request);

        if($form->isValid()){
            $data = $form->getData();
            $stripe = $this->get('stripe');
            $response = $stripe->processCheckout($pet->getPrice(), $data['token']);
            if (isset($response['error'])){

                $request->getSession()->getFlashBag()->add(
                    'error',
                    'Oops: '.$response['error']
                );
            } else {

                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'Yay! Congratulations on your new pet.  We will traffic it to you right away'
                );

                return $this->redirect($this->generateUrl(''));
            }
        }


        return array('pet' => $pet, 'form' => $form->createView());
    }
}
