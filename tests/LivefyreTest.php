<?php
namespace Livefyre\Test;

use Livefyre\Livefyre;

class LivefyreTest extends \PHPUnit_Framework_TestCase {
    // public function testAPI() {
    //     $network = Livefyre::getNetwork("networkName", "networkKey");
    //     $this->assertTrue($network->setUserSyncUrl("something{id}"));
    //     $this->assertTrue($network->syncUser("system"));

    //     $siteId = 0;
    //     $site = Livefyre::getNetwork("networkName", "networkKey")->getSite($siteId, $siteSecret);
    //     var_dump($site->getCollectionContent(584));
    // }

	/**
	 * @covers Livefyre::getNetwork->setUserSyncUrl()
	 * @expectedException InvalidArgumentException
	 */
    public function testNetworkUserSyncUrl() {
        $network = Livefyre::getNetwork("networkName", "networkKey");
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
        $network->validateLivefyreToken($network->buildLfToken());
    }

	/**
	 * @covers Livefyre::getNetwork->getSite->buildCollectionMetaToken()
	 * @expectedException InvalidArgumentException
	 */
    public function testSiteBuildCollectionMetaToken_badUrl() {
    	$site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
    	$site->buildCollectionMetaToken("title", "articleId", "url", "tags");
    }

	/**
	 * @covers Livefyre::getNetwork->getSite->buildCollectionMetaToken()
	 * @expectedException InvalidArgumentException
	 */
    public function testSiteBuildCollectionMetaToken_badTitle() {
    	$site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
    	$site->buildCollectionMetaToken("1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456", "articleId", "url", "tags");
    }
}
