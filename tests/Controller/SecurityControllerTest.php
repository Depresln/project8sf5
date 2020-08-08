<?php


namespace App\Tests\Controller;


use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityControllerTest extends WebTestCase
{

    use FixturesTrait;
    use ConnectionTrait;

    public function testAuthPageIsNotRestricted(){
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDisplayLogin(){
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertSelectorTextContains('h1', 'sign in');
    }

    public function testLoginWithBadCredentials(){
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'username' => 'johndoe',
            'password' => 'fakepassword'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testUsernameDoesNotExist(){
        $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
        $client = static::createClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login', [
            '_csrf_token' => $csrfToken,
            'username' => 'notvalid',
            'password' => '0000'
        ]);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testSuccessfulLogin(){
        $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
        $client = static::createClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login', [
            '_csrf_token' => $csrfToken,
            'username' => 'johndoe',
            'password' => '0000'
        ]);
        $this->assertResponseRedirects('/');
        $client->followRedirect();
        $this->assertSelectorExists('.btn-danger');
        $this->assertSelectorNotExists('.alert-danger');
    }

    public function testSuccessfulLogout(){
        $client = static::createClient();
//        $users = $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
//        /** @var User $user */
//        $user = $users['user_user'];
//        $session = $client->getContainer()->get('session');
//        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
//        $session->set('_security_main', serialize($token));
//        $session->save();
//
//        $cookie = new Cookie($session->getName(), $session->getId());
//        $client->getCookieJar()->set($cookie);
        $this->connection($client, 'user_user');

        $client->request('GET', '/logout');
        $client->followRedirect();
        $this->assertSelectorExists('.btn-success'); // Bouton se connecter
        $this->assertSelectorNotExists('.btn-danger');
    }

    public function testUserIsGrantedAdmin(){
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

        $client->request('GET', '/users');
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertSelectorNotExists('.alert-warning');
    }
}
