<?php
/* 
Plugin Name: Post Type & Taxonomy Debug
Description: Get a list of all registered post types and taxonomies including all properties. Use for debugging or educational purposes.
Version: 1.0
Author: nerdismFTW
Author URI: https://profiles.wordpress.org/nerdismftw
License: GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/ 

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

if (!class_exists('PTTAX_Debug')) {
	
	final class PTTAX_Debug
	{
		protected $version = '1.0';
		protected $pluginName = 'Post Type & Taxonomy Debug';
        protected $pluginSlug = 'pttax_debug';
		protected $contactEmail = 'contact@nerdismftw.com';
		protected $reservedPostTypes = array('post', 'page', 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block');
		protected $reservedTerms = array('attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category', 'category__and', 'category__in', 'category__not_in', 'category_name', 'comments_per_page', 'comments_popup', 'customize_messenger_channel', 'customized', 'cpage', 'day', 'debug', 'error', 'exact', 'feed', 'fields', 'hour', 'link_category', 'm', 'minute', 'monthnum', 'more', 'name', 'nav_menu', 'nonce', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page', 'page_id', 'paged', 'pagename', 'pb', 'perm', 'post', 'post__in', 'post__not_in', 'post_format', 'post_mime_type', 'post_status', 'post_tag', 'post_type', 'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots', 's', 'search', 'second', 'sentence', 'showposts', 'static', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in', 'tag__not_in', 'tag_id', 'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'theme', 'type', 'w', 'withcomments', 'withoutcomments', 'year');
		
		public function __construct()
		{
			// actions hooks
            add_action('admin_enqueue_scripts', array($this, 'action_register_js_css'));
			add_action('admin_menu', array($this, 'action_show_menu'));
            
   			// filters
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'filter_action_links') );
        }
        
		/**
		 * Set "Settings" link in plugin directory
		 * @return array
		 */
		public function filter_action_links ( $links )
		{
			$mylinks = array(
				'<a href="' . admin_url( 'tools.php?page='.$this->pluginSlug ) . '">Settings</a>',
			);
			return array_merge( $mylinks, $links );
		}
        
		/**
		 * Register css & js files
		 * @return void
		 */
		function action_register_js_css()
		{
			wp_register_script('cptdebug_plugin_js', plugins_url( '/js/functions.js', __FILE__ ), array(), false, true);
			wp_register_style('cptdebug_plugin_css', plugins_url( '/css/style.css', __FILE__ ), array(), false, 'all');
            wp_enqueue_script('cptdebug_plugin_js');
			wp_enqueue_style('cptdebug_plugin_css');
		}
        
		/**
		 * Get boolean output
		 * @return string
		 */
        protected function getBooleanOutput($name, $value)
        {
            $html = '';
            $html .= '<li>'.$name.' => <small>boolean</small> ';
			$html .= ($value === true) ? 'true' : 'false';
            $html .= '</li>';    
            return $html;
        }
        
		/**
		 * Get unsupported output
		 * @return string
		 */
        protected function getUnsupportedOutput($value)
        {
            return '<li>Unsupported: '.gettype($value).'<li>';
        }
        
		/**
		 * Get NULL output
		 * @return string
		 */
        protected function getNULLOutput($name)
        {
            return '<li>'.$name.' => NULL'.'</li>';    
        }

		/**
		 * Get string output
		 * @return string
		 */
        protected function getStringOutput($name, $value)
        {
            return '<li>'.$name.' => <small>string</small> "'.$value.'" <i>(length='.strlen($value).')</i></li>';    
        }
        
		/**
		 * Get int output
		 * @return string
		 */
        protected function getIntOutput($name, $value)
        {
            return '<li>'.$name.' => <small>int</small '.$value.'</li>';    
        }
        
		/**
		 * Get object output
		 * @return string
		 */
        protected function getObjectOutput($name, $value)
        {
            $html = '';
            $html .= '<li><span class="pttax_debug_caret"><small>object '.get_class($value).'</small> ('.$name.') ['.count(get_object_vars($value)).']</span>';
            $html .= '<ul class="pttax_debug_nested">';
            foreach($value as $newName => $newVal) {
                
                // handle boolean
                if(is_bool($newVal)) {
                    $html .= $this->getBooleanOutput($newName, $newVal);
                }
                // handle NULL
                elseif(is_null($newVal)) {
                    $html .= $this->getNULLOutput($newName);
                }
                // handle string
                elseif(is_string($newVal)) {
                    $html .= $this->getStringOutput($newName, $newVal);
                }
                // handle int        
                elseif(is_int($newVal)) {
                    $html .= $this->getIntOutput($newName, $newVal);
                }
                // handle object        
                elseif(is_object($newVal)) {
                    echo $this->getObjectOutput($newName, $newVal);
                }
                // handle array        
                elseif(is_array($newVal)) {
                    echo $this->getArrayOutput($newName, $newVal);
                }
                // handle unknown type
                else {
					echo $this->getUnsupportedOutput($newVal);
                }
            }
            $html .= '</ul>';
            return $html;
        }

		/**
		 * Get array output
		 * @return string
		 */
        protected function getArrayOutput($name, $value)
        {
            $html = '';
            $html .= '<li><span class="pttax_debug_caret"><small>array</small> ('.$name.') ['.count($value).']</span>';
            $html .= '<ul class="pttax_debug_nested">';
            foreach($value as $newName => $newVal) {
                
                // handle boolean
                if(is_bool($newVal)) {
                    $html .= $this->getBooleanOutput($newName, $newVal);
                }
                // handle NULL
                elseif(is_null($newVal)) {
                    $html .= $this->getNULLOutput($newName);
                }
                // handle string
                elseif(is_string($newVal)) {
                    $html .= $this->getStringOutput($newName, $newVal);
                }
                // handle int        
                elseif(is_int($newVal)) {
                    $html .= $this->getIntOutput($newName, $newVal);
                }
                // handle object        
                elseif(is_object($newVal)) {
                    echo $this->getObjectOutput($newName, $newVal);
                }
                // handle array
                elseif(is_array($newVal)) {
                    echo $this->getArrayOutput($newName, $newVal);
                }
                // handle unknown type
                else {
					echo $this->getUnsupportedOutput($newVal);
                }
            }
            $html .= '</ul>';
            return $html;
        }
		
		/**
		 * Add admin footer changes
		 * @return void
		 */
		protected function change_admin_footer_text()
		{
			add_filter('admin_footer_text', array($this, 'filter_change_footer_admin'), 9999);
			add_filter('update_footer', array($this, 'filter_change_footer_version'), 9999);			
		}
		
		/**
		 * Callback function for change_admin_footer_text()
		 * @return void
		 */
		public function filter_change_footer_admin() 
		{
			echo '';
		}
		
		/**
		 * Callback function for change_admin_footer_text()
		 * @return void
		 */
		public function filter_change_footer_version()
		{
			echo '<span id="footer-thankyou"><b>'.$this->pluginName.'</b> Version '.$this->version.'</span>';
		}
        
		/**
		 * Add menu page
		 * @return void
		 */
		public function action_show_menu()
		{
			add_management_page( 	$this->pluginName, // page title
									$this->pluginName, // menu title
									'activate_plugins',	// capability
									$this->pluginSlug, // menu slug
									array($this, 'menu_page') // callable function
								);	
		}
	
		/** 
		 * Output menu page html
		 * @return void
		 */
		public function menu_page()
		{
			if (!current_user_can('manage_options'))  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			} else { ?>
			
			<?php
				// change footer info in plugin page
				$this->change_admin_footer_text();
			?>
			
			<div class="pttax_debug_wrap wrap">
				<h2><?php echo $this->pluginName; ?></h2>
				<hr>
				
				<?php if(!function_exists('get_taxonomies')) : ?>	
					<div class="notice notice-error"><p><strong>Error:</strong> Function get_taxonomies() not found.</p></div>
				<?php elseif(!function_exists('get_taxonomy')) : ?>	
					<div class="notice notice-error"><p><strong>Error:</strong> Function get_taxonomy() not found.</p></div>
				<?php elseif(!function_exists('get_post_types')) : ?>	
					<div class="notice notice-error"><p><strong>Error:</strong> Function get_post_types() not found.</p></div>
				<?php elseif(!function_exists('get_post_type_object')) : ?>	
					<div class="notice notice-error"><p><strong>Error:</strong> Function get_post_type_object() not found.</p></div>
				<?php else : ?>
					<div id="poststuff" class="pttax_debug_poststuff">
						<div id="post-body" class="metabox-holder columns-2">
							<div id="postbox-container-1" class="postbox-container " >
								<div>
									<div class="meta-box-sortables">
										<div class="postbox">
											<h2><span>Feedback</span></h2>
											<div class="inside">
												<p>
													Ideas, feedback, bug reports or recommendations? <a href="mailto:<?php echo $this->contactEmail; ?>"><strong>Get in touch.</strong></a>
												</p>
												<p>
													<a target="_blank" href="https://wordpress.org/plugins/post-type-taxonomy-debug/">Plugin Webpage</a><br/>
													<a target="_blank" href="https://wordpress.org/support/plugin/post-type-taxonomy-debug/reviews/">Rate this plugin</a><br/>
												</p>
											</div>
										</div>
										<div class="postbox">
											<h2><span>Used functions</span></h2>
											<div class="inside">
												<p>
													<a target="_blank" href="https://codex.wordpress.org/Function_Reference/get_post_types"><span style="font-family:monospace">get_post_types()</span></a>
													<br/>
													<a target="_blank" href="https://codex.wordpress.org/Function_Reference/get_post_type_object"><span style="font-family:monospace">get_post_type_object()</span></a>
													<br/>
													<a target="_blank" href="https://codex.wordpress.org/Function_Reference/get_taxonomies"><span style="font-family:monospace">get_taxonomies()</span></a>
													<br/>
													<a target="_blank" href="https://codex.wordpress.org/Function_Reference/get_taxonomy"><span style="font-family:monospace">get_taxonomy()</span></a>
												</p>
											</div>
										</div>
									</div>								
								</div>
							</div>
							<div id="postbox-container-2" class="postbox-container">

								This shows a list of all registered post types and taxonomies including all properties. The list is expandable/collapsable.<br/>
								Use for debugging or educational purposes.
								
								<br/><br/>
								<ul class="pttax_debug_ul pttax_debug_mainul">
								<li><span class="pttax_debug_caret pttax_debug_caret-down"><b>Registered Post Types</b></span>
								<?php
								
								// get all post types
								$postTypes = get_post_types();
								
								echo '<ul class="pttax_debug_nested pttax_debug_active">';
								foreach ( $postTypes as $postType ) {
									// get post type details
									$postTypeDetails = get_post_type_object($postType);

									echo '<li><span class="pttax_debug_caret">';
									echo $postType;
									if(in_array($postType, $this->reservedPostTypes)) {
										echo '<small>*</small>';
									}
								   echo '</span>';
								   
								   
								   echo '<ul class="pttax_debug_nested">';
								   foreach($postTypeDetails as $postTypeDetailName => $postTypeDetail) {
								
										// handle boolean
										if(is_bool($postTypeDetail)) {
											echo $this->getBooleanOutput($postTypeDetailName, $postTypeDetail);
										}
										// handle NULL
										elseif(is_null($postTypeDetail)) {
											echo $this->getNULLOutput($postTypeDetailName);
										}
										// handle string
										elseif(is_string($postTypeDetail)) {
											echo $this->getStringOutput($postTypeDetailName, $postTypeDetail);
										}
										// handle int        
										elseif(is_int($postTypeDetail)) {
											echo $this->getIntOutput($postTypeDetailName, $postTypeDetail);
										}
										// handle object        
										elseif(is_object($postTypeDetail)) {
											echo $this->getObjectOutput($postTypeDetailName, $postTypeDetail);
										}
										// handle array        
										elseif(is_array($postTypeDetail)) {
											echo $this->getArrayOutput($postTypeDetailName, $postTypeDetail);
										}
										// handle unknown type
										else {
											echo $this->getUnsupportedOutput($postTypeDetail);
										}
								   }
								   echo '</ul>';
								}
								echo '</ul>';
								?>
								</li>
								</ul>
								<br/><br/>
								<ul class="pttax_debug_ul pttax_debug_mainul">
									<li><span class="pttax_debug_caret pttax_debug_caret-down"><b>Registered Taxonomies</b></span>
									<?php
									
									// get all taxonomies
									$taxonomies = get_taxonomies();
									
									echo '<ul class="pttax_debug_nested pttax_debug_active">';
									foreach ( $taxonomies as $taxonomy ) {
										echo '<li><span class="pttax_debug_caret">';
										echo $taxonomy;
										
										if(in_array($taxonomy, $this->reservedTerms)) {
											echo '<small>*</small>';
										}
									   echo '</span>';
									   
									   // get details of taxonomy
									   $taxonomyDetails = get_taxonomy($taxonomy);
									   
									   echo '<ul class="pttax_debug_nested">';
									   foreach($taxonomyDetails as $taxonomyDetailName => $taxonomyDetailValue) {
									
											// handle boolean
											if(is_bool($taxonomyDetailValue)) {
												echo $this->getBooleanOutput($taxonomyDetailName, $taxonomyDetailValue);
											}
											// handle NULL
											elseif(is_null($taxonomyDetailValue)) {
												echo $this->getNULLOutput($taxonomyDetailName);
											}
											// handle string
											elseif(is_string($taxonomyDetailValue)) {
												echo $this->getStringOutput($taxonomyDetailName, $taxonomyDetailValue);
											}
											// handle int        
											elseif(is_int($taxonomyDetailValue)) {
												echo $this->getIntOutput($taxonomyDetailName, $taxonomyDetailValue);
											}
											// handle object        
											elseif(is_object($taxonomyDetailValue)) {
												echo $this->getObjectOutput($taxonomyDetailName, $taxonomyDetailValue);
											}
											// handle array        
											elseif(is_array($taxonomyDetailValue)) {
												echo $this->getArrayOutput($taxonomyDetailName, $taxonomyDetailValue);
											}
											// handle unknown type
											else {
												echo $this->getUnsupportedOutput($taxonomyDetailValue);
											}
									   }
									   echo '</ul>';
									}
									echo '</ul>';
									?>
									</li>
								</ul>
								<br/>
								<br/>
								<b>*</b> This post types or taxonomies are reserved and used by WordPress.
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div><?php
			}
		} 
	}
	new PTTAX_Debug();
}