<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/TemplateService.php

namespace App\Services;

class TemplateService
{

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
        $view = $this->twig->render('Email/invitation.html.twig', [
            'fname' => $person->getFname(),
            'token' => $person->getConfirmationToken(),
            'expires' => $person->getTokenExpiresAt(),
        ]);
        $mailParams = [
            'view' => $view,
            'recipient' => $person->getEmail(),
            'subject' => 'Invitation from ConnectionsReno',
        ];

        return $mailParams;
    }

    public function nonprofitPostReg($org, $rep)
    {
        $org->addRep($rep);
        $org->setActive(false);
        // send confirmation email
        $view = $this->twig->render('Email/staff_confirmation.html.twig', [
            'fname' => $rep->getFname(),
            'token' => $rep->getConfirmationToken(),
            'expires' => $rep->getTokenExpiresAt(),
            'orgname' => $org->getOrgname(),
        ]);
        $mailParams = [
            'view' => $view,
            'recipient' => $rep->getEmail(),
            'subject' => 'Volunteer Connections',
        ];

        return $mailParams;
    }

    public function volunteerPostReg($person)
    {
        $view = $this->twig->render('Email/volunteer_confirmation.html.twig', [
            'fname' => $person->getFname(),
            'token' => $person->getConfirmationToken(),
            'expires' => $person->getTokenExpiresAt(),
        ]);
        $mailParams = [
            'view' => $view,
            'recipient' => $person->getEmail(),
            'subject' => 'Volunteer Connections',
        ];

        return $mailParams;
    }

}
