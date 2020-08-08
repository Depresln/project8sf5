<?php


namespace App\Tests\Controller;


use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testTaskToDo() {
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

        $tasks = $this->loadFixtureFiles([__DIR__ . '/tasks.yaml']);
        $client->request('GET', '/tasks');

        $this->assertSelectorTextNotContains('button', 'Marquer non terminée');
        $this->assertSelectorTextContains('button', 'Marquer comme faite');
    }

    public function testMarkTaskDone() {
        $client = static::createClient();
        $tasks = $this->loadFixtureFiles([__DIR__ . '/tasks.yaml']);
        $crawler = $client->request('GET', '/tasks');
        $crawler->selectButton('Marquer comme faite');

        $this->assertSelectorTextNotContains('.btn-success', 'Marquer non terminée');
        $this->assertSelectorTextNotContains('.btn-success', 'Marquer comme faite');
    }

    public function testTaskDone() {
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

        $tasks = $this->loadFixtureFiles([__DIR__ . '/tasks.yaml']);
        $client->request('GET', '/tasksdone');

        $response = $client->getResponse();
        $this->assertStringContainsString('Marquer non terminée', $response);
        $this->assertSelectorTextNotContains('.btn-success', 'Marquer comme faite');
    }

    public function testMarkTaskToDo() {
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

        $tasks = $this->loadFixtureFiles([__DIR__ . '/tasks.yaml']);
        $crawler = $client->request('GET', '/tasksdone');
        $form = $crawler->selectButton('Marquer non terminée')->form();
        $client->submit($form);
        $client->followRedirect();

        $this->assertSelectorTextNotContains('.btn-success', 'Marquer non terminée');
        $response = $client->getResponse();
        $this->assertStringContainsString('Marquer comme faite', $response);
        $this->assertStringContainsString('La tâche Task done a bien été marquée comme faite.', $response);
    }

    public function testSuccessfullyAddTask(){
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

        $crawler = $client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Added task',
            'task[content]' => 'Task content'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/tasks');
        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }

    public function testSuccessfullyDeleteTask(){
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

        $tasks = $this->loadFixtureFiles([__DIR__ . '/tasks.yaml']);
        $crawler = $client->request('GET', '/tasks');
        $form = $crawler->selectButton('Supprimer')->form();
        $client->submit($form);
        $client->followRedirect();

        $this->assertSelectorExists('.alert-success');
    }

    public function testSuccessfullyEditTask(){
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

        $tasks = $this->loadFixtureFiles([__DIR__ . '/tasks.yaml']);
        $crawler = $client->request('GET', '/tasks/1/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Added task edited',
            'task[content]' => 'Task content edited'
        ]);
        $client->submit($form);
        $client->followRedirect();

        $this->assertSelectorExists('.alert-success');
        $response = $client->getResponse();
        $this->assertStringContainsString('edited', $response);
    }
}
