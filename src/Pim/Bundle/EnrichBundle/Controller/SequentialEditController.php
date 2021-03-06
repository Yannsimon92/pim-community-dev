<?php

namespace Pim\Bundle\EnrichBundle\Controller;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Pim\Bundle\DataGridBundle\Extension\MassAction\MassActionDispatcher;
use Pim\Bundle\EnrichBundle\Manager\SequentialEditManager;
use Pim\Bundle\UserBundle\Context\UserContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Sequential edit action controller for products
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SequentialEditController
{
    /** @var RouterInterface */
    protected $router;

    /** @var MassActionDispatcher */
    protected $massActionDispatcher;

    /** @var SequentialEditManager */
    protected $seqEditManager;

    /** @var UserContext */
    protected $userContext;

    /** @var array */
    protected $objects;

    /**
     * Constructor
     *
     * @param RouterInterface       $router
     * @param MassActionDispatcher  $massActionDispatcher
     * @param SequentialEditManager $seqEditManager
     * @param UserContext           $userContext
     */
    public function __construct(
        RouterInterface $router,
        MassActionDispatcher $massActionDispatcher,
        SequentialEditManager $seqEditManager,
        UserContext $userContext
    ) {
        $this->router               = $router;
        $this->massActionDispatcher = $massActionDispatcher;
        $this->seqEditManager       = $seqEditManager;
        $this->userContext          = $userContext;
    }

    /**
     * Action for product sequential edition
     * @param Request $request
     *
     * @AclAncestor("pim_enrich_product_edit_attributes")
     * @return RedirectResponse
     */
    public function sequentialEditAction(Request $request)
    {
        $sequentialEdit = $this->seqEditManager->createEntity(
            $this->getObjects($request),
            $this->userContext->getUser()
        );

        if ($this->seqEditManager->findByUser($this->userContext->getUser())) {
            return new RedirectResponse(
                $this->router->generate(
                    'pim_enrich_product_index',
                    ['dataLocale' => $request->get('dataLocale')]
                )
            );
        }
        $this->seqEditManager->save($sequentialEdit);

        return new RedirectResponse(
            $this->router->generate(
                'pim_enrich_product_edit',
                [
                    'dataLocale' => $request->get('dataLocale'),
                    'id' => current($sequentialEdit->getObjectSet())
                ]
            )
        );
    }

    /**
     * Get products to mass edit
     * @param Request $request
     *
     * @return array
     */
    protected function getObjects(Request $request)
    {
        if ($this->objects === null) {
            $this->dispatchMassAction($request);
        }

        return $this->objects;
    }

    /**
     * Dispatch mass action
     *
     * @param Request $request
     */
    protected function dispatchMassAction(Request $request)
    {
        $this->objects = $this->massActionDispatcher->dispatch($request);
    }
}
