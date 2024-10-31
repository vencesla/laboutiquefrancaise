<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {
        /**
         * 1. Créer un faux client (navigateur) de pointer vers une url
         * 2. Remplir les champs de mon formulaire d'inscription
         * 3. Est-ce que tu peux regarder si dans ma page j'ai le message (alerte) suivante: Votre compte est correctement créé. Veillez vous connecter.
         */
        $client = static::createClient();
        $client->request('GET', '/inscription');

        // 2 . {firstname, lastname, password,confirmation du password}

        $client->submitForm('Valider', [
            'register_user[email]' => 'julie@gmail.com',
            'register_user[plainPassword][first]' => 'julie',
            'register_user[plainPassword][second]' => 'julie',
            'register_user[firstname]' => 'julie',
            'register_user[lastname]' => 'LE MAROUILLE'
        ]);

        // Follow
        $this->assertResponseRedirects('/connexion');
        $client->followRedirect();
        // 3.
        $this->assertSelectorExists('div:contains("Votre compte est correctement créé. Veillez vous connecter.")');      
    }
}
