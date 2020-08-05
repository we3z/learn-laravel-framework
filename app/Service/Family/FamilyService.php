<?php
namespace App\Service\Family;


class FamilyService
{
    public function __construct(PersonService $personService, TvService $tvService)
    {
        $this->person = $personService;
        echo "Family instance create success <br/>";
    }

    public function testAbc()
    {
        $this->person->test();
    }

}
