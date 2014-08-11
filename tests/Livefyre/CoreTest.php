<?php
namespace Livefyre;

use Livefyre\Utils\JWT;
use Livefyre\LfTest;

class CoreTest extends \PHPUnit_Framework_TestCase {
    private $_config;

    protected function setUp() {
        $this->_config = new LfTest();
        $this->_config->setPropValues("prod");
    }

    public function testApi() {
        $this->markTestSkipped("can't make network calls to bad params.");

        $network = Livefyre::getNetwork($this->_config->NETWORK_NAME, $this->_config->NETWORK_KEY);
        $this->assertTrue($network->setUserSyncUrl("url/{id}"));
        $this->assertTrue($network->syncUser("username"));

        $site = Livefyre::getNetwork($this->_config->NETWORK_NAME, $this->_config->NETWORK_KEY)->getSite($this->_config->SITE_ID, $this->_config->SITE_KEY);

        $name = "PHPCreateCollection" . time();

        $id = $site->createCollection($name, $name, "http://answers.livefyre.com/PHP");
        $otherId = $site->getCollectionId($name);

        $this->assertEquals($id, $otherId);
        var_dump($site->getCollectionContent($this->_config->ARTICLE_ID));
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

	/**
	 * @covers Livefyre::getNetwork->getSite->buildCollectionMetaToken()
	 * @expectedException InvalidArgumentException
	 */
    public function testSiteBuildCollectionMetaToken_badUrl() {
    	$site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
    	$site->buildCollectionMetaToken("title", "articleId", "url");
    }

	/**
	 * @covers Livefyre::getNetwork->getSite->buildCollectionMetaToken()
	 * @expectedException InvalidArgumentException
	 */
    public function testSiteBuildCollectionMetaToken_badTitle() {
    	$site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
    	$site->buildCollectionMetaToken("1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456", "articleId", "http://www.url.com");
    }

    /**
     * @covers Livefyre::getNetwork->getSite->buildCollectionMetaToken()
     * @expectedException InvalidArgumentException
     */
    public function testSiteBuildCollectionMetaToken_badType() {
        $site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
        $site->buildCollectionMetaToken("title", "articleId", "http://livefyre.com", array("type"=>"badType"));
    }

    public function testSiteBuildCollectionMetaToken() {
        $site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
        $site->buildCollectionMetaToken("title", "articleId", "https://www.url.com");
    }

    public function testSiteBuildCollectionMetaToken_goodScenarios() {
        $site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
        
        $token = $site->buildCollectionMetaToken("title", "articleId", "https://www.url.com", array("tags"=>"tags", "type"=>"reviews"));
        $decoded = JWT::decode($token, "siteSecret");

        $this->assertEquals("reviews", $decoded->{"type"});

        $token = $site->buildCollectionMetaToken("title", "articleId", "https://www.url.com", array("type"=>"liveblog"));
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

        $this->assertEquals("4464458a10c305693b5bf4d43a384be7", $checksum);
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
