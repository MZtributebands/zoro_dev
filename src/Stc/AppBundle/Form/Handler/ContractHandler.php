<?php

namespace Stc\AppBundle\Form\Handler;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Oro\Bundle\TagBundle\Entity\TagManager;

use Stc\AppBundle\Entity\Contract;
use Stc\AppBundle\Entity\Repository\ContractRepository;

class ContractHandler
{
    /** @var FormInterface */
    protected $form;

    /** @var Request */
    protected $request;

    /** @var EntityManager */
    protected $manager;

    /**
     * @var TagManager
     */
    protected $tagManager;

    /**
     * @param FormInterface $form
     * @param Request       $request
     * @param EntityManager $manager
     */
    public function __construct(FormInterface $form, Request $request, EntityManager $manager, TagManager $tagManager)
    {
        $this->form    = $form;
        $this->request = $request;
        $this->manager = $manager;
        $this->tagManager = $tagManager;
        $this->repository = $manager->getRepository('StcAppBundle:Contract');
    }

    /**
     * Process form
     *
     * @param  Contract $entity
     *
     * @return bool  True on successful processing, false otherwise
     */
    public function handle(Contract $entity)
    {

        //set empty stage to default of ''
        $stage = $entity->getStage();
        if (empty($stage)) {
            $entity->setStage('created');
        }

        //set default for location:
        $location = $entity->getLocation();
        if ($location === '') {
            $entity->setLocation($this->request->getBasePath().'/web/documents');
        }

        $signedAt = $entity->getSignedAt();
        if ($signedAt !== '') {
            $entity->setIsSigned(1);
        } else {
            $entity->setIsSigned(0);
        }

        //print_rb($entity->getPerformance());exit;

        //get performance to link to:
        //$performance_id = $entity->getPerformanceId();


        //set other default field values:
        $entity->setDeleted(0);

        $this->form->setData($entity);
        $this->form->submit($this->request);

        if ($this->form->isValid()) {
            $this->onSuccess($entity);
            //set the associated performance after contract object is persisted:
            //$contract_id = $entity->getId();
            //$this->repository->insertAssociatedPerformance($contract_id, $performance_id);
            return true;
        } else {
            return false;
        }

        //return false;
    }

    /**
     * "Success" form handler
     *
     * @param Contract $entity
     */
    protected function onSuccess(Contract $entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();

        $this->tagManager->saveTagging($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function setTagManager(TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
    }

}