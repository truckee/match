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
use App\Entity\Person;
use App\Form\Type\OpportunityType;
use App\Form\Type\OpportunitySearchType;
use App\Services\EmailerService;
use App\Services\NewOppEmailService;
use App\Services\TemplateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/opportunity")
 */
class OpportunityController extends AbstractController
{

    private $mailer;
    private $newOpp;
    private $templateSvc;

    public function __construct(EmailerService $mailer, NewOppEmailService $newOpp, TemplateService $templateSvc)
    {
        $this->mailer = $mailer;
        $this->newOpp = $newOpp;
        $this->templateSvc = $templateSvc;
    }

    /**
     * @Route("/add", name = "opp_add")
     */
    public function addOpp(Request $request, EmailerService $mailer)
    {
        $user = $this->getUser();
        if (null === $user || !$user->hasRole('ROLE_REP')) {
            return $this->redirectToRoute('home_page');
        }
        $nonprofit = $user->getNonprofit();
        $opportunity = new Opportunity();
        $form = $this->createForm(OpportunityType::class, $opportunity);
        $oppView = $this->templateSvc->oppView();
        $header = $oppView['header'];
        $entity_form = $oppView['entityForm'];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $opportunity->setNonprofit($nonprofit);
            $em->persist($opportunity);
            $em->flush();

            $volunteers = $em->getRepository(Person::class)->opportunityEmails($opportunity);
            $oppVol = $this->newOpp->newOppEmail($mailer, $volunteers, $opportunity);
            $this->addFlash(
                    'success',
                    'Opportunity added; ' . count($volunteers) . ' volunteer(s) will be notified'
            );

            return $this->redirectToRoute('profile_nonprofit');
        }

        return $this->render('Entity/entity_form.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Add ' . $nonprofit->getOrgname() . ' opportunity',
                    'header' => $header,
                    'entity_form' => $entity_form,
        ]);
    }

    /**
     * @Route("/edit/{opportunity}", name = "opp_edit")
     * @ParamConverter("opportunity", class="App:Opportunity")
     */
    public function editOpp(Request $request, $opportunity = null)
    {
        $user = $this->getUser();
        if (null === $user || !$user->hasRole('ROLE_REP') || null === $opportunity) {
            return $this->redirectToRoute('home_page');
        }

        $nonprofit = $opportunity->getNonprofit();
        $form = $this->createForm(OpportunityType::class, $opportunity);
        $oppView = $this->templateSvc->oppView();
        $header = $oppView['header'];
        $entity_form = $oppView['entityForm'];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($opportunity);
            $em->flush();
            $this->addFlash(
                    'success',
                    'Opportunity updated'
            );

            return $this->redirectToRoute('profile_nonprofit');
        }

        return $this->render('Entity/entity_form.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Edit ' . $nonprofit->getOrgname() . ' opportunity',
                    'header' => $header,
                    'entity_form' => $entity_form,
        ]);
    }

    /**
     * @Route("/search", name = "opp_search")
     */
    public function search(Request $request)
    {
        $oppSearch = $this->templateSvc->oppSearch();
        $header = $oppSearch['header'];
        $entity_form = $oppSearch['entityForm'];
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
        return $this->render('Entity/entity_form.html.twig', [
                    'form' => $form->createView(),
                    'headerText' => 'Opportunity search',
                    'submitValue' => 'Search',
                    'header' => $header,
                    'entity_form' => $entity_form,
        ]);
    }

}
