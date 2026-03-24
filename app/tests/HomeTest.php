<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testSomething(): void // acceder a la page de login
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1','Toute l\'actu');

    }

    private function Navigation(): void // Acceder a la page de connexion depuis le header
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('a:contains("Se connecter")');

        $client->clickLink('Se connecter');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1','Se connecter');

    }
}
