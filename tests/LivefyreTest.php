<?php
namespace Livefyre\Test;

use Livefyre\Core\Utils\JWT;
use Livefyre\Livefyre;

class LivefyreTest extends \PHPUnit_Framework_TestCase {
    // public function testAPI() {
    //     $network = Livefyre::getNetwork("networkName", "networkKey");
    //     $this->assertTrue($network->setUserSyncUrl("url/{id}"));
    //     $this->assertTrue($network->syncUser("username"));

    //     $siteId = 0;
    //     $site = Livefyre::getNetwork("networkName", "networkKey")->getSite($siteId, $siteSecret);
    //     print($site->getCollectionId(articleId));
    //     var_dump($site->getCollectionContent(articleId));
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
        $network->validateLivefyreToken($network->buildLivefyreToken());
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
    	$site->buildCollectionMetaToken("1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456", "articleId", "http://www.url.com", "tags");
    }

    /**
     * @covers Livefyre::getNetwork->getSite->buildCollectionMetaToken()
     * @expectedException InvalidArgumentException
     */
    public function testSiteBuildCollectionMetaToken_badType() {
        $site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
        $site->buildCollectionMetaToken("title", "articleId", "http://livefyre.com", "tags", "badType");
    }

    public function testSiteBuildCollectionMetaToken() {
        $site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
        $site->buildCollectionMetaToken("title", "articleId", "https://www.url.com", "tags");
    }

    public function testSiteBuildCollectionMetaToken_goodScenarios() {
        $site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
        
        $token = $site->buildCollectionMetaToken("title", "articleId", "https://www.url.com", "tags", "reviews");
        $decoded = JWT::decode($token, "siteSecret");

        $this->assertEquals("reviews", $decoded->{"type"});

        $token = $site->buildCollectionMetaToken("title", "articleId", "https://www.url.com", "tags", "liveblog");
        $decoded = JWT::decode($token, "siteSecret");

        $this->assertEquals("liveblog", $decoded->{"type"});
    }

    /**
     * @covers Livefyre::getNetwork->getSite->buildChecksum()
     * @expectedException InvalidArgumentException
     */
    public function testSiteBuildChecksum_badUrl() {
        $site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
        $site->buildChecksum("title", "url", "tags");
    }

    /**
     * @covers Livefyre::getNetwork->getSite->buildChecksum()
     * @expectedException InvalidArgumentException
     */
    public function testSiteBuildChecksum_badTitle() {
        $site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
        $site->buildChecksum("1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456", "http://www.url.com", "tags");
    }

    public function testSiteBuildChecksum() {
        $site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
        $checksum = $site->buildChecksum("title", "https://www.url.com", "tags");

        $this->assertEquals("6e2e4faf7b95f896260fe695eafb34ba", $checksum);
    }

    public function testSiteValidUrls() {
        $site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");

        $site->buildChecksum("", "http://test.com:8000", "");
        $site->buildChecksum("", "http://test.com", "");
        $site->buildChecksum("", "https://test.com/", "");
        $site->buildChecksum("", "ftp://test.com/", "");
        $site->buildChecksum("", "http://清华大学.cn", "");
        $site->buildChecksum("", "http://www.mysite.com/myresumé.html", "");
        $site->buildChecksum("", "https://test.com/path/test.-_~!$&'()*+,=:@/dash", "");
    }
}
