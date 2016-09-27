<?php

namespace WobbleCode\BillingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
    }

    public function testShowtotal()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/show_total/');
    }

    public function testShowtotalbytype()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/show_total/by/{type}/');
    }

}
