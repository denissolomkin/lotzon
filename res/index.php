<?php
if(!class_exists('Config')){
    class Config
    {
        private static $_instance = null;
        private $_configs = array(
            'dev' => true
        );
        private function __construct() {}
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new Config();
            }

            return self::$_instance;
        }

        public function __set($key, $value)
        {
            $this->_configs[$key] = $value;
        }

        public function __get($key)
        {
            if (isset($this->_configs[$key])) {
                return $this->_configs[$key];
            }

            return null;
        }
    }
}

?><?php include "layout/header.php"; ?>

    <div class="container">
        <main class="content clearfix">

            <div class="banner-1">
                <?php include "layout/banner_1.php"; ?>
            </div>

            <div class="content-top">
            </div><!-- .content-top -->


            <div class="banner-2">
                <?php include "layout/banner_2.php"; ?>
            </div>

        </main><!-- .content -->

        <aside class="sidebar">
            <div class="banner-4">
                <?php include "layout/banner_4.php"; ?>
            </div>
            <div class="banner-5">
                <?php include "layout/banner_5.php"; ?>
            </div>
        </aside>
    </div><!-- .container -->

<?php include "layout/footer.php"; ?>