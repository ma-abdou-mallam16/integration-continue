<?php

namespace App\test\Security;

use App\Entity\User;
use App\Security\GithubUserProvider;
use GuzzleHttp\Client;
use Symfony\Component\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GithubUserProviderTest extends TestCase
{
    public function testLoadUserByUsernameReturningAUser()
    {
        $client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $serializer = $this
            ->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->getMock();
        // Nous nous attendions à ce que la méthode getBody soit appelée une fois
        $client
            ->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $streamedResponse = $this
            ->getMockBuilder(StreamInterface::class)
            ->getMock();
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($streamedResponse);

        $userData = ['login' => 'a login', 'name' => 'user name', 'email' => 'adress@mail.com', 'avatar_url' => 'url to the avatar', 'html_url' => 'url to profile'];
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($userData);

        // $serializer
        //     ->expects($this->once())
        //     ->method('deserialize')
        //     ->willReturn([]);

        //$this->expectException('LogicException');

        $githubUserProvider = new GithubUserProvider($client, $serializer);
        $user = $githubUserProvider->loadUserByUsername('an-access-token');

        $expectedUser = new User($userData['login'], $userData['name'], $userData['email'], $userData['avatar_url'], $userData['html_url']);

        $this->assertEquals($expectedUser, $user);
        $this->assertEquals(User::class, get_class($user));
    }
}
