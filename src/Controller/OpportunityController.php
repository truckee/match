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
use App\Form\Type\OpportunitySearchType;
use App\Services\EmailerService;
use App\Services\NewOppEmailService;
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
            'Opportunity/_suggestions.html.twig',
            'Opportunity/_opportunity.html.twig',
            'Default/_skills.html.twig'
        ];
    }

    /**
     * @Route("/add", name = "opp_add")
     */
    public function addOpp(Request $request, EmailerService $mailer)
    {
        $user = $this->getUser();
        if (null === $user || !$user->hasRole('ROLE_REP')) {
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
            $em->persist($opportunity);
            $em->flush();

            $volunteers = $em->getRepository(Volunteer::class)->opportunityEmails($opportunity);
            $oppMail = new NewOppEmailService($em);
            $oppMail->newOppEmail($mailer, $volunteers, $opportunity);
            $this->addFlash(
                'success',
                'Opportunity added; ' . count($volunteers) . ' volunteer(s) will be notified'
            );

            return $this->redirectToRoute('profile_nonprofit');
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
        if (null === $user || !$user->hasRole('ROLE_REP') || null === $id) {
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

            return $this->redirectToRoute('profile_nonprofit');
        }

        return $this->render('Default/form_templates.html.twig', [
                    'form' => $form->createView(),
                    'templates' => $templates,
                    'headerText' => 'Edit ' . $nonprofit->getOrgname() . ' opportunity',
                    'skillHeader' => 'Opportunity skill requirements',
                    'oppHeader' => 'Opportunity'
        ]);
    }

    /**
     * @Route("/search", name = "opp_search")
     */
    public function search(Request $request)
    {
        $templates = [
            'Opportunity/_search_instructions.html.twig',
            'Default/_focuses.html.twig',
            'Default/_skills.html.twig',
        ];
        $form = $this->createForm(OpportunitySearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $request->request->get('search');
            $em = $this->getDoctrine()->getManager();
            if (!array_key_exists('focuses', $search) && !array_key_exists('skills', $search)) {
                $opps = $em->getRepository(Opportunity::class)->getAllOpenOpps();
            } else {
                $focuses = $search['focuses'] ?? [];
                $skills = $search['skills'] ?? [];
                $opps = $em->getRepository(Opportunity::class)->getOppsByFocusOrSkill($focuses, $skills);
            }
            if (empty($opps)) {
                $this->addFlash('warning', 'No matching opportunities found');
                
                return $this->redirectToRoute('opp_search');
            }
            return $this->render('/Opportunity/search_results.html.twig', [
                        'opportunities' => $opps
            ]);
        }
        return $this->render('Default/form_templates.html.twig', [
                    'form' => $form->createView(),
                    'templates' => $templates,
                    'headerText' => 'Opportunity search',
                    'focusHeader' => 'Focus options',
                    'skillHeader' => 'Skill options',
                    'submitValue' => 'Search',
        ]);
    }
}
