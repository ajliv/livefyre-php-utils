<?php

namespace Livefyre;


class NetworkTest extends \PHPUnit_Framework_TestCase {
    private $_config;

    protected function setUp() {
        $this->_config = new LfTest();
        $this->_config->setPropValues("prod");
    }

    public function testApi() {
        $network = Livefyre::getNetwork($this->_config->NETWORK_NAME, $this->_config->NETWORK_KEY);
        $network->setUserSyncUrl("url/{id}");
        $network->syncUser("username");
    }

    /**
     * @covers Livefyre::getNetwork->setUserSyncUrl()
     * @expectedException InvalidArgumentException
     */
    public function testNetworkUserSyncUrl() {
        $network = Livefyre::getNetwork($this->_config->NETWORK_NAME, $this->_config->NETWORK_KEY);
        $network->setUserSyncUrl("www.test.com");
    }

    /**
     * @covers Livefyre::getNetwork->buildUserAuthToken()
     * @expectedException InvalidArgumentException
     */
    public function testNetworkBuildUserAuthToken() {
        $network = Livefyre::getNetwork("networkName", "networkKey");
        $network->buildUserAuthToken("fawe-f-fawef.", "test", "test");
    }

    /**
     * @covers Livefyre::getNetwork->validateLivefyreToken()
     */
    public function testNetworkValidateLivefyreToken() {
        $network = Livefyre::getNetwork("networkName", "networkKey");
        $network->validateLivefyreToken($network->buildLivefyreToken());
    }
}
