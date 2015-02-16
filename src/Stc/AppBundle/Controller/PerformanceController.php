<?php

namespace Stc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use Oro\Bundle\UserBundle\Entity\User;
use Stc\AppBundle\Entity\Performance;

use Stc\AppBundle\Form\Type\PerformanceType;

/**
 * @Route("/performance")
 */
class PerformanceController extends Controller
{
    /**
     * @Route(
     * ".{_format}",
     * name="stc_performance_index",
     * requirements={"_format"="html|json"},
     * defaults={"_format" = "html"}
     * )
     * @Template
     * @AclAncestor("stc_performance_view")
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/info/{id}", name="stc_performance_info", requirements={"id"="\d+"})
     * @Template
     * @AclAncestor("stc_performance_view")
     */
    public function infoViewAction(Performance $performance)
    {
        return array(
            'entity' => $performance
        );
    }

    /**
     * @Route("/view/{id}", name="stc_performance_view", requirements={"id"="\d+"})
     * @Template
     * @Acl(
     * id="stc_performance_view",
     * type="entity",
     * class="StcAppBundle:Performance",
     * permission="VIEW"
     * )
     */
    public function viewAction(Performance $performance)
    {
        return [
            'entity' => $performance
        ];
    }


    /**
     * @Route("/create", name="stc_performance_create")
     * @Template("StcAppBundle:Performance:update.html.twig")
     * @Acl(
     * id="stc_performance_create",
     * type="entity",
     * class="StcAppBundle:Performance",
     * permission="CREATE"
     * )
     */
    public function createAction()
    {
        $performance = new Performance();
        $leadId = $this->getRequest()->get('lead');
        if ($leadId) {
            $repository = $this->getDoctrine()->getRepository('StcAppBundle:Performance');
            $lead = $repository->find($leadId);
            if ($lead) {
                $performance->setLead($lead);
            } else {
                throw new NotFoundHttpException(sprintf('Lead with ID %s is not found', $leadId));
            }
        }
        $token = $this->get('security.context')->getToken();
        //$username = $context->getUsername();
        $user = $token->getUser();
        $performance->setCreatedAt(new \DateTime('now'));
        $performance->setStatus(1);
        $performance->setDeleted(0);
        $performance->setAssignee($user);
        $performance->setOwner($user);
        return $this->update($performance);
    }

    /**
     * @param Performance $performance
     * @return array
     */
    protected function update(Performance $performance)
    {
        $request = $this->getRequest();
        $form = $this->createForm(new PerformanceType(), $performance);
        $formHandler = $this->get('stc_performance.form.handler');
        /*$formHandler = $this->get('stc_performance.form.handler');
        if ($formHandler->handle($form, $request)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator').trans('stc.performance.saved_message')
            );
            return $this->get('oro_ui.router')->actionRedirect(
                array(
                    'route' => 'stc_performance_update',
                    'parameters' => array('id' => $performance->getId()),
                ),
                array(
                    'route' => 'stc_performance_view',
                    'parameters' => array('id' => $performance->getId()),
                )
            );
        }*/
        if ('POST' === $request->getMethod()) {
            //$form->setData($performance);
            if ($formHandler->handle($performance)) {
                //handled in form handler:
                //$this->getDoctrine()->getManager()->persist($performance);
                //$this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('stc.performance.saved_message')
                );
                return $this->get('oro_ui.router')->redirectAfterSave(
                    array(
                        'route' => 'stc_performance_index',
                        'parameters' => array('id' => $performance->getId()),
                    ),
                    array(
                        'route' => 'stc_performance_index',
                        'parameters' => array('id' => $performance->getId()),
                    )
                );
            }
        }
        return array(
            'entity' => $performance,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/update/{id}", name="stc_performance_update", requirements={"id"="\d+"})
     * @Template
     * @Acl(
     * id="stc_performance_update",
     * type="entity",
     * class="StcAppBundle:Performance",
     * permission="VIEW"
     * )
     */
    public function updateAction(Performance $performance)
    {
        $performanceRepository = $this->getDoctrine()->getManager()->getRepository('StcAppBundle:Performance');
        $id = $this->getRequest()->get('id');
        $findPerformance = $performanceRepository->find($id);
        //print_r($findPerformance);exit;
        //return $this->update($performance);
        $result = $this->update($findPerformance);
        return $result;
    }


}