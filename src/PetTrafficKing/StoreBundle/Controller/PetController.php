<?php

namespace PetTrafficKing\StoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PetTrafficKing\StoreBundle\Entity\Pet;
use PetTrafficKing\StoreBundle\Form\PetType;

/**
 * Pet controller.
 *
 * @Route("/")
 */
class PetController extends Controller
{

    /**
     * Lists all Pet entities.
     *
     * @Route("/", name="")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {

        $filters = $request->query->all();
        $em = $this->getDoctrine()->getManager();

        foreach($filters as $key => $filter){
            if(empty($filter)){
                unset($filters[$key]);
            }
        }

        $entities = $em->getRepository('PetTrafficKingStoreBundle:Pet')->getByFilters($filters);

        return array(
            'entities' => $entities,
            'filters' => $filters
        );
    }
    /**
     * Creates a new Pet entity.
     *
     * @Route("/admin", name="_create")
     * @Method("POST")
     * @Template("PetTrafficKingStoreBundle:Pet:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Pet();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Pet entity.
     *
     * @param Pet $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pet $entity)
    {
        $form = $this->createForm(new PetType(), $entity, array(
            'action' => $this->generateUrl('_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pet entity.
     *
     * @Route("/admin/new", name="_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Pet();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Pet entity.
     *
     * @Route("pet/{id}", name="_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PetTrafficKingStoreBundle:Pet')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pet entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Pet entity.
     *
     * @Route("/admin/{id}/edit", name="_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $pet = $em->getRepository('PetTrafficKingStoreBundle:Pet')->find($id);

        if (!$pet) {
            throw $this->createNotFoundException('Unable to find Pet entity.');
        }

        $editForm = $this->createEditForm($pet);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'pet'      => $pet,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Pet entity.
    *
    * @param Pet $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pet $entity)
    {
        $form = $this->createForm(new PetType(), $entity, array(
            'action' => $this->generateUrl('_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Pet entity.
     *
     * @Route("/admin/{id}", name="_update")
     * @Method("PUT")
     * @Template("PetTrafficKingStoreBundle:Pet:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PetTrafficKingStoreBundle:Pet')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pet entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Pet entity.
     *
     * @Route("/admin/{id}", name="_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PetTrafficKingStoreBundle:Pet')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pet entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl(''));
    }

    /**
     * Creates a form to delete a Pet entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
