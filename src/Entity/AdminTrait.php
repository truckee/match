<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Entity/AdminTrait.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait AdminTrait
{

    /**
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mailer;

    public function getMailer()
    {
        return $this->mailer;
    }

    public function setMailer($mailer)
    {
        $this->mailer = $mailer;

        return $this;
    }

}
