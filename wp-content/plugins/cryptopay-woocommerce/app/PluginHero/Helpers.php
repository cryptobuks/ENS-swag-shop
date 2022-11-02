<?php 

namespace BeycanPress\CryptoPay\WooCommerce\PluginHero;

/**
 * Contains the commonly used ones for this plugin
 */
trait Helpers
{   
    /**
     * @param string $property
     * @return mixed
     */
    public function __get(string $property)
    {
        if (is_null(Plugin::$properties)) return null;
        return isset(Plugin::$properties->$property) ? Plugin::$properties->$property : null;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set(string $property, $value) : void
    {
        Plugin::$properties->$property = $value;
    }

    /**
     *
     * @param object $page
     * @return void
     */
    public function addPage(object $page) : void
    {
        if (is_null(Plugin::$properties)) return;
        $className = (new \ReflectionClass($page))->getShortName();
        if (!isset(Plugin::$properties->pages)) {
            Plugin::$properties->pages = (object) [];
        }
        Plugin::$properties->pages->$className = $page;
    }

    /**
     * @param object $api
     * @return void
     */
    public function addApi(object $api) : void
    {
        if (is_null(Plugin::$properties)) return;
        $className = (new \ReflectionClass($api))->getShortName();
        if (!isset(Plugin::$properties->apis)) {
            Plugin::$properties->apis = (object) [];
        }
        Plugin::$properties->apis->$className = $api;
    }

    /**
     * @param string $viewName Directory name within the folder
     * @return string
     */
    public function addonView(string $viewName, array $args = []) : string
    {
        $trace = debug_backtrace();
        if (DIRECTORY_SEPARATOR == '\\') {
            $re = '/.*?\\\\plugins\\\\.*\\\\app\\\\/si';
        } else {
            $re = '/.*?\/plugins\/.*\/app\//si';
        }
        $addonDir = $trace[1]['function'] == 'addonViewEcho' ? $trace[1]['file'] : $trace[0]['file'];
        preg_match($re, $addonDir, $matches, PREG_OFFSET_CAPTURE, 0);
        $viewDir = str_replace(['app\\', 'app/'], 'views/', $matches[0][0]);

        extract($args);
        ob_start();
        include $viewDir . $viewName . '.php';
        return ob_get_clean();
    }

    /**
     * @param string $viewName Directory name within the folder
     * @return void
     */
    public function addonViewEcho(string $viewName, array $args = []) : void
    {
        print($this->addonView($viewName, $args));
    }

    /**
     * @param string $viewName Directory name within the folder
     * @return string
     */
    public function view(string $viewName, array $args = []) : string
    {
        extract($args);
        ob_start();
        include $this->viewDir . $viewName . '.php';
        return ob_get_clean();
    }

    /**
     * @param string $viewName Directory name within the folder
     * @return void
     */
    public function viewEcho(string $viewName, array $args = []) : void
    {
        print($this->view($viewName, $args));
    }

    /**
     * Easy use for get_option
     * @param string $setting
     * @return mixed
     */
    public function setting(string $setting = null)
    {
        if (is_null(Plugin::$settings)) Plugin::$settings = get_option($this->settingKey); 

        if (is_null($setting)) return Plugin::$settings;

        return isset(Plugin::$settings[$setting]) ? Plugin::$settings[$setting] : null;
    }

    /**
     * @param string $viewName Directory name within the folder
     * @return void
     */
    public function getTemplate(string $templateName, array $args = []) : void
    {
        extract($args);
        ob_start();
        include $this->phDir . 'Templates/' . $templateName . '.php';
        echo ob_get_clean();
    }

    /**
     * @param string $fileName php file name
     * @return string
     */
    public function getFilePath(string $fileName) : string
    {
        return $this->pluginDir . $fileName . '.php';
    }

    /**
     * @param string get image url in asset images folder
     * @return string
     */
    public function getImageUrl(string $imageName) : string
    {
        return $this->pluginUrl . 'assets/images/' . $imageName;
    }

    /**
     * @param string $type error, success more
     * @param string $notice notice to be given
     * @param bool $dismissible in-dismissible button show and hide
     * @return void
     */
    public function notice(string $notice, string $type = 'success', bool $dismissible = false) : void
    {
        $this->getTemplate('notice', [
            'type' => $type,
            'notice' => $notice,
            'dismissible' => $dismissible
        ]);
    }   

    /**
     * @param string $type error, success more
     * @param string $notice notice to be given
     * @param bool $dismissible in-dismissible button show and hide
     * @return void
     */
    public function adminNotice(string $notice, string $type = 'success', bool $dismissible = false) : void
    {
        add_action('admin_notices', function() use ($notice, $type, $dismissible) {
            $this->notice($notice, $type, $dismissible);
        });
    }   
    
    /**
     * Ajax action hooks
     * @param string $action ajax function name
     * @return void
     */
    public function ajaxAction(string $action) : void
    {
        add_action('wp_ajax_'.$action , [$this, $action]);
        add_action('wp_ajax_nopriv_'.$action , [$this, $action]);
    }

    /**
     * New nonce create method
     * @param string|null $externalKey
     * @return string
     */
    public function createNewNonce(?string $externalKey = null) : string
    {
        $key = $this->pluginKey . '_nonce' . $externalKey;
        return wp_create_nonce($key);
    }

    /**
     * Nonce control mehod
     * @param string|null $externalKey
     * @return void
     */
    public function checkNonce(?string $externalKey = null) : void
    {
        $key = $this->pluginKey . '_nonce' . $externalKey;
        check_ajax_referer($key, 'nonce');
    }

    /**
     * New nonce field create method
     * @param string|null $externalKey
     * @return void
     */
    public function createNewNonceField(?string $externalKey = null) : void
    {
        $key = $this->pluginKey . '_nonce' . $externalKey;
        wp_nonce_field($key, 'nonce');
    }

    /**
     * Nonce field control method
     * @param string|null $externalKey
     * @return bool
     */
    public function checkNonceField(?string $externalKey = null) : bool
    {
        $key = $this->pluginKey . '_nonce' . $externalKey;
        if (!isset($_POST['nonce'])) return false;
        return @wp_verify_nonce($_POST['nonce'], $key) ? true : false;
    }

    /**
     * @return string
     */
    public function getCurrentUrl() : string
    {
        $siteURL = explode('/', get_site_url());
        $requestURL = explode('/', $_SERVER['REQUEST_URI']);
        $currentURL = array_unique(array_merge($siteURL, $requestURL));
        return implode('/', $currentURL);
    }

    /**
     * @param string $url
     * @return void
     */
    protected function pageRedirect(string $url) : void
    {
        die("<script>window.location.href = '".$url."'</script>");
    }

    /**
     * @param string $url
     * @return void
     */
    public function adminRedirect(string $url) : void
    {
        add_action('admin_init', function() use ($url) {
			die(wp_redirect($url));
		});
    }

    /**
     * @param string $url
     * @return void
     */
    public function templateRedirect(string $url) : void
    {
        add_action('template_redirect', function() use ($url) {
			die(wp_redirect($url));
		});
    }

    /**
     * @param string $date
     * @return string
     */
    public function dateToTimeAgo(string $date) : string
    {
        return human_time_diff(strtotime(wp_date('Y-m-d H:i:s')), strtotime($date));
    }

    /**
     * @param int|string|float $number
     * @param int $decimals
     * @return float
     */
    public function toFixed($number, int $decimals) : float
    {
        return floatval(number_format($number, $decimals, '.', ""));
    }

    /**
     * @param string $jsonString
     * @param bool $array
     * @return object|array
     */
    public function parseJson(string $jsonString, bool $array = false)
    {
        return json_decode(html_entity_decode(stripslashes($jsonString)), $array);
    }

    /**
     *
     * @param string $content
     * @return string
     */
    function catchShortcode(string $content) : string
    {
        global $shortcode_tags;
        $tagnames = array_keys($shortcode_tags);
        $tagregexp = join( '|', array_map('preg_quote', $tagnames) );
    
        // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcodes()
        $pattern = '(.?)\[('.$tagregexp.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)';

        return preg_replace_callback('/'.$pattern.'/s', 'do_shortcode_tag', $content);
    }
	
	/**
     * @param string $path
     * @param array $deps
     * @return string
     */
    public function addScript(string $path, array $deps = []) : string
    {
        $key = explode('/', $path);
        wp_enqueue_script(
            $key = $this->pluginKey . '-' . end($key),
            $this->pluginUrl . 'assets/' . $path,
            $deps,
            $this->pluginVersion,
            true
        );
        
        return $key;
    }

    /**
     * @param string $path
     * @param array $deps
     * @return string
     */
    public function addStyle(string $path, array $deps = []) : string
    {
        $key = explode('/', $path);
        wp_enqueue_style(
            $key = $this->pluginKey . '-' . end($key),
            $this->pluginUrl . 'assets/' . $path,
            $deps,
            $this->pluginVersion
        );
        
        return $key;
    }

    /**
     * @param string $path
     * @param array $deps
     * @return string
     */
    public function addAddonScript(string $path, array $deps = []) : string
    {
        wp_enqueue_script(
            $key = $this->pluginKey . '-addon-' . end($key),
            $this->getAddonAssetsDir() . $path,
            $deps,
            $this->pluginVersion,
            true
        );
        
        return $key;
    }

    /**
     * @param string $path
     * @param array $deps
     * @return string
     */
    public function addAddonStyle(string $path, array $deps = []) : string
    {
        $key = explode('/', $path);
        wp_enqueue_style(
            $key = $this->pluginKey . '-addon-' . end($key),
            $this->getAddonAssetsDir() . $path,
            $deps,
            $this->pluginVersion
        );
        
        return $key;
    }

    /**
     * @return string
     */
    private function getAddonAssetsDir() : string
    {
        $trace = debug_backtrace();
        if (DIRECTORY_SEPARATOR == '\\') {
            $re = '/plugins\\\\(.*)\\\\app/m';
        } else {
            $re = '/plugins\/(.*)\/app/m';
        }
        
        preg_match($re, $trace[1]['file'], $matches, PREG_OFFSET_CAPTURE, 0);

        return preg_replace(['/plugins\/.*/m'], ['plugins/'.$matches[1][0].'/assets/'], $this->pluginUrl);
    }

    /**
     * @return string|null
     */
    public function getIp() : ?string
    {
        $ip = null;
		if (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = wp_unslash($_SERVER['REMOTE_ADDR']);
			$ip = rest_is_ip_address($ip);
			if (false === $ip) {
				$ip = null;
			}
		}

        return $ip;
    }
	
	/**
     * @param string $templateName
     * @param string $templateFile
     * @param array $params
     * @return void
     */
    public function registerPageTemplate(string $templateName, string $templateFile, array $params) : void
    {
        add_filter('theme_page_templates', function($templates) use ($templateName, $templateFile){
            $templateFile = $this->pluginKey . $templateFile;
            return array_merge($templates, [$templateFile => esc_html($templateName)]);
        });

        add_filter('template_include', function($template) use ($params) {
            global $post;
            
            if ($post && is_page()) {
				$pageTemplate = get_page_template_slug($post->ID);

				if (strpos($pageTemplate, $this->pluginKey) !== false) {
					exit($this->viewEcho('page-templates/' . str_replace($this->pluginKey,  '', $pageTemplate), $params));
				}    
			}   
            
            return $template;
        }, 99);
    }

    /**
     * @param callable $function
     * @param string $name
     * @param int $time
     * @return object
     */
    public function cache(callable $function, string $name, int $time = 600) : object
    {
        $path = $this->viewDir . 'cache/';

        if (!file_exists($path)) {
            mkdir($path);       
        } 

        $file = $path . $name . '.html';

        if (file_exists($file) && time() - $time < filemtime($file)) {
            $content = file_get_contents($file);
        } else {
            if (file_exists($file)) {
                unlink($file);
            }

            $content = $function();

            $fp = fopen($file, 'w+');
            fwrite($fp, $content);
            fclose($fp);
        }

        return (object) compact('file', 'content');
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function deleteCache(string $name) : bool
    {
        $path = $this->viewDir . 'cache/';
        $file = $path . $name . '.html';
        if (file_exists($file)) {
            return unlink($file);
        } else {
            return false;
        }
    }
}