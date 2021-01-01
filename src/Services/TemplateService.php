<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/TemplateService.php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class TemplateService
{

    private $em;
    private $twig;

    public function __construct(EntityManagerInterface $em, Environment $twig)
    {
        $this->em = $em;
        $this->twig = $twig;
    }

    public function oppView()
    {
        $header = [
            'left' => 'Filling out the form',
            'center' => 'Opportunity',
            'right' => 'Skill(s)',
        ];
        $entity_form = [
            'left' => ['Opportunity/_suggestions.html.twig'],
            'center' => ['Opportunity/_opportunity.html.twig'],
            'right' => ['Default/_skills.html.twig']
        ];

        return ['header' => $header, 'entityForm' => $entity_form];
    }

    public function oppSearch()
    {
        $header = [
            'left' => 'Searching for opportunities',
            'center' => 'Focus(es)',
            'right' => 'Skill(s)',
        ];
        $entity_form = [
            'left' => ['Opportunity/_search_instructions.html.twig'],
            'center' => ['Default/_focuses.html.twig'],
            'right' => ['Default/_skills.html.twig']
        ];

        return ['header' => $header, 'entityForm' => $entity_form];
    }

    public function preRegAdmin()
    {
        $header = ['center' => ''];
        $entity_form['center'] = [
            'Entity/_user_name.html.twig',
            'Entity/_user_email.html.twig',
        ];

        return ['header' => $header, 'entityForm' => $entity_form];
    }

    public function preRegNonprofit()
    {
        $header = ['left' => 'Nonprofit Organization',
            'center' => "Staff member",
            'right' => "Nonprofit's Focus(es)"];
        $entity_form['left'] = ['Nonprofit/_nonprofit_form.html.twig'];
        $entity_form['center'] = [
            'Entity/_user_name.html.twig',
            'Entity/_user_email.html.twig',
            'Entity/_user_plain_password.html.twig',
        ];
        $entity_form['right'] = ['Default/_focuses.html.twig'];

        return [
            'header' => $header,
            'entityForm' => $entity_form,
        ];
    }

    public function preRegVolunteer()
    {
        $header = ['left' => 'Volunteer',
            'center' => "Volunteer's Focus(es)",
            'right' => "Volunteer's Skill(s)"];
        $entity_form['left'] = [
            'Entity/_user_name.html.twig',
            'Entity/_user_email.html.twig',
            'Entity/_user_plain_password.html.twig',
        ];
        $entity_form['center'] = ['Default/_focuses.html.twig'];
        $entity_form['right'] = ['Default/_skills.html.twig'];

        return [
            'header' => $header,
            'entityForm' => $entity_form,
            'flashMessage' => 'A volunteer registration confirmation has been sent to your email address',
        ];
    }

    public function adminPostReg($person)
    {
        $mailParams = [
            'template' => 'Email/invitation.html.twig',
            'recipient' => $person->getEmail(),
            'subject' => 'Invitation from ConnectionsReno',
            'context' => [
                'fname' => $person->getFname(),
                'token' => $person->getConfirmationToken(),
                'expires' => $person->getTokenExpiresAt(),
            ]
        ];

        return $mailParams;
    }

    public function nonprofitPostReg($org, $rep)
    {
        $org->addRep($rep);
        $org->setActive(false);
        // send confirmation email
        $mailParams = [
            'template' => 'Email/staff_confirmation.html.twig',
            'recipient' => $rep->getEmail(),
            'subject' => 'Volunteer Connections',
            'context' => [
                'fname' => $rep->getFname(),
                'token' => $rep->getConfirmationToken(),
                'expires' => $rep->getTokenExpiresAt(),
                'orgname' => $org->getOrgname(),
            ]
        ];

        return $mailParams;
    }

    public function volunteerPostReg($person)
    {
        $mailParams = [
            'template' => 'Email/volunteer_confirmation.html.twig',
            'recipient' => $person->getEmail(),
            'subject' => 'Volunteer Connections',
            'context' => [
                'fname' => $person->getFname(),
                'token' => $person->getConfirmationToken(),
                'expires' => $person->getTokenExpiresAt(),
            ]
        ];

        return $mailParams;
    }

}
