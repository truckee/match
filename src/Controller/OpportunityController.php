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
use App\Entity\Volunteer;
use App\Form\Type\OpportunityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/opportunity")
 */
class OpportunityController extends AbstractController
{

    public function __construct()
    {
        $this->templates = [
            'Opportunity/suggestions.html.twig',
            'Opportunity/opportunity.html.twig',
            'Default/skills.html.twig'
        ];
    }

    /**
     * @Route("/add", name = "opp_add")
     */
    public function addOpp(Request $request)
    {
        $user = $this->getUser();
        if (null === $user || !$user->hasRole('ROLE_STAFF')) {
            return $this->redirectToRoute('home');
        }
        $nonprofit = $user->getNonprofit();
        $opportunity = new Opportunity();
        $form = $this->createForm(OpportunityType::class, $opportunity);
        $templates = $this->templates;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $opportunity->setNonprofit($nonprofit);
            $vols = $em->getRepository(Volunteer::class)->opportunityEmails($opportunity);
            $em->persist($opportunity);
            $em->flush();
            $this->addFlash(
                    'success',
                    'Opportunity added'
            );

            return $this->redirectToRoute('profile');
        }

        return $this->render('Default/form_templates.html.twig', [
                    'form' => $form->createView(),
                    'templates' => $templates,
                    'headerText' => 'Add ' . $nonprofit->getOrgname() . ' opportunity',
                    'skillHeader' => 'Opportunity skill requirements',
                    'oppHeader' => 'Opportunity'
        ]);
    }

    /**
     * @Route("/edit/{id}", name = "opp_edit")
     */
    public function editOpp(Request $request, $id = null)
    {
        $user = $this->getUser();
        if (null === $user || !$user->hasRole('ROLE_STAFF') || null === $id) {
            return $this->redirectToRoute('home');
        }

        $em = $this->getDoctrine()->getManager();
        $opportunity = $em->getRepository(Opportunity::class)->find($id);
        $nonprofit = $opportunity->getNonprofit();
        $form = $this->createForm(OpportunityType::class, $opportunity);
        $templates = $this->templates;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($opportunity);
            $em->flush();
            $this->addFlash(
                    'success',
                    'Opportunity updated'
            );

            return $this->redirectToRoute('profile');
        }

        return $this->render('Default/form_templates.html.twig', [
                    'form' => $form->createView(),
                    'templates' => $templates,
                    'headerText' => 'Edit ' . $nonprofit->getOrgname() . ' opportunity',
                    'skillHeader' => 'Opportunity skill requirements',
                    'oppHeader' => 'Opportunity'
        ]);
    }

}
