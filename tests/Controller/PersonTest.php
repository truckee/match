<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/path_here/PersonTest.php

namespace App\Tests\Controller;

use App\Entity\Person;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{

    public function testNewPersonRoles()
    {
        $person = new Person('ROLE_SUPER_ADMIN');
        $roles = $person->getRoles();

        $this->assertTrue(in_array('ROLE_SUPER_ADMIN', $roles));
    }

}
