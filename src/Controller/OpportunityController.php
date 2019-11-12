<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/OpportunityController/OpportunityController.php

namespace App\Controller;

use App\Entity\Opportunity;
use App\Form\Type\OpportunityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/opportunity")
 */
class OpportunityController extends AbstractController
{

    /**
     * @Route("/add", name = "opp_add")
     */
    public function add(Request $request)
    {
        $user = $this->getUser();
        if (null === $user || !$user->hasRole('ROLE_STAFF')) {
            return $this->redirectToRoute('home');
        }
        $nonprofit = $user->getNonprofit();
        $opportunity = new Opportunity();
        $form = $this->createForm(OpportunityType::class, $opportunity);
        $templates = [
            'Opportunity/suggestions.html.twig',
            'Opportunity/opportunity.html.twig',
            'Default/skills.html.twig'
        ];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
        }

        return $this->render('Default/form_templates.html.twig', [
                    'form' => $form->createView(),
                    'templates' => $templates,
                    'headerText' => 'Add opportunity',
                    'skillHeader' => 'Opportunity skill requirements',
                    'oppHeader' => 'New ' . $nonprofit->getOrgname() . ' opportunity'
        ]);
    }

}
