<?php

namespace Livefyre;


class LivefyreUtilTest {
    public function testSiteValidUrls() {
        $site = Livefyre::getNetwork($this->_config->NETWORK_NAME, $this->_config->NETWORK_KEY)->getSite("siteId", "siteSecret");

        $site->buildChecksum("", "http://test.com:8000", "");
        $site->buildChecksum("", "http://test.com", "");
        $site->buildChecksum("", "https://test.com/", "");
        $site->buildChecksum("", "ftp://test.com/", "");
        $site->buildChecksum("", "http://清华大学.cn", "");
        $site->buildChecksum("", "http://www.mysite.com/myresumé.html", "");
        $site->buildChecksum("", "https://test.com/path/test.-_~!$&'()*+,=:@/dash", "");
    }
}