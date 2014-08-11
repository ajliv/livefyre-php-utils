<?php
namespace Livefyre;

class LfTest {
    public $NETWORK_NAME = "<NETWORK-NAME>";
    public $NETWORK_KEY = "<NETWORK-KEY>";
    public $SITE_ID = "<SITE-ID>";
    public $SITE_KEY = "<SITE-KEY>";
    public $COLLECTION_ID = "<COLLECTION-ID>";
    public $USER_ID = "<USER-ID>";
    public $ARTICLE_ID = "<ARTICLE-ID>";

    public function setPropValues($env) {
        $values = parse_ini_file("test.ini", true);
        $this->NETWORK_NAME = $values[$env]["NETWORK_NAME"];
        $this->NETWORK_KEY = $values[$env]["NETWORK_KEY"];
        $this->SITE_ID = $values[$env]["SITE_ID"];
        $this->SITE_KEY = $values[$env]["SITE_KEY"];
        $this->COLLECTION_ID = $values[$env]["COLLECTION_ID"];
        $this->USER_ID = $values[$env]["USER_ID"];
        $this->ARTICLE_ID = $values[$env]["ARTICLE_ID"];
    }
}
