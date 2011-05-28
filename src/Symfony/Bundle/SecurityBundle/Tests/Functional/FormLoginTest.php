<?php

namespace Symfony\Bundle\SecurityBundle\Tests\Functional;

/**
 * @group functional
 */
class FormLoginTest extends WebTestCase
{
    public function testFormLogin()
    {
        $client = $this->createClient(array('test_case' => 'StandardFormLogin'));

        $form = $client->request('GET', '/login')->selectButton('login')->form();
        $form['_username'] = 'johannes';
        $form['_password'] = 'test';
        $client->submit($form);

        $this->assertRedirect($client->getResponse(), '/');

        $text = $client->followRedirect()->text();
        $this->assertContains('Hello johannes!', $text);
        $this->assertContains('You\'re browsing to path "/".', $text);
    }

    public function testFormLoginWithCustomTargetPath()
    {
        $client = $this->createClient(array('test_case' => 'StandardFormLogin'));

        $form = $client->request('GET', '/login')->selectButton('login')->form();
        $form['_username'] = 'johannes';
        $form['_password'] = 'test';
        $form['_target_path'] = '/foo';
        $client->submit($form);

        $this->assertRedirect($client->getResponse(), '/foo');

        $text = $client->followRedirect()->text();
        $this->assertContains('Hello johannes!', $text);
        $this->assertContains('You\'re browsing to path "/foo".', $text);
    }

    public function testFormLoginRedirectsToProtectedResourceAfterLogin()
    {
        $client = $this->createClient(array('test_case' => 'StandardFormLogin'));

        $client->request('GET', '/protected-resource');
        $this->assertRedirect($client->getResponse(), '/login');

        $form = $client->followRedirect()->selectButton('login')->form();
        $form['_username'] = 'johannes';
        $form['_password'] = 'test';
        $client->submit($form);
        $this->assertRedirect($client->getResponse(), '/protected-resource');

        $text = $client->followRedirect()->text();
        $this->assertContains('Hello johannes!', $text);
        $this->assertContains('You\'re browsing to path "/protected-resource".', $text);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->deleteTmpDir('StandardFormLogin');
    }
}