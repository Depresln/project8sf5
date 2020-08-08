<?php


namespace App\Tests\Controller;


use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
{

    use FixturesTrait;

    public function testLetAuthenticatedUserAccessAuth(){
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
        /** @var User $user */
        $user = $users['user_user'];
        $session = $client->getContainer()->get('session');
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUserCreationWorks(){
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
        /** @var User $user */
        $user = $users['user_admin'];
        $session = $client->getContainer()->get('session');
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $crawler = $client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'addedUser',
            'user[password][first]' => '0000',
            'user[password][second]' => '0000',
            'user[email]' => 'email@domain.com'
        ]);
        $client->submit($form);

        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');
        $response = $client->getResponse();
        $this->assertStringContainsString('addedUser', $response);
    }

    public function testSuccessfullyEditUser(){
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
        /** @var User $user */
        $user = $users['user_admin'];
        $session = $client->getContainer()->get('session');
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $crawler = $client->request('GET', '/users/1/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'userEdited',
            'user[password][first]' => '0000',
            'user[password][second]' => '0000',
            'user[email]' => 'email@domain.com',
            'user[role][0]' => 'ROLE_USER'
        ]);
        $client->submit($form);
        $client->followRedirect();

        $this->assertSelectorExists('.alert-success');
        $response = $client->getResponse();
        $this->assertStringContainsString('userEdited', $response);
    }
}
