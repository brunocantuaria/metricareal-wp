<?php
/*
 Plugin Name: Métrica Real
 Plugin URI: https://metricareal.com.br
 Description: Plugin oficial do Métrica Real para instalação do script de nosso serviço em seu website.
 Author: Cantuaria Sites
 Author URI: https://www.cantuaria.net.br
 Version: 1.1
 Requires at least: 6.0
 Tested up to: 6.8
 License: GPLv2 or later
 License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
 

/*
 * Register our admin page
 */
function metrica_real_admin_setup() {

    add_management_page('Métrica Real', 'Métrica Real', 'manage_options', 'metrica-real', 'metrica_real_admin_page');
	
}
add_action('admin_menu', 'metrica_real_admin_setup');

function metrica_real_admin_page() {

    $debug = (isset($_GET['debug'])) ? wp_verify_nonce( sanitize_text_field(wp_unslash($_GET['debug'])), 'metricareal' ) : false;
    $debug_nonce = wp_create_nonce( 'metricareal' );

    ?>
    <div class="health-check-header metrica-real">
        <div class="health-check-title-section" style="flex-direction: column">
            <a href="https://metricareal.com.br/" target="_blank"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )); ?>/assets/img/logo.png" width="300" alt="Métrica Real" style="margin: 20px auto;"/></a>
            <h1>Diagnóstico</h1>
        </div>
    </div>

    <hr class="wp-header-end" />
    
        <div class="health-check-body">
            <div class="site-status-has-issues">
            
                <p>Obrigado por utilizar o plugin do Métrica Real. Abaixo você pode conferir se está tudo certo com o seu site.</p>

                <?php
                //Check if current domain is registered
                if ( false === ( $ping_result = get_transient( 'metrica_real_ping' ) ) ) {

                    $ping_result = new WP_Http();
                    $ping_result = $ping_result->get('https://app.metricareal.com.br/?ping='. urlencode(get_home_url()));
                    if (!is_wp_error( $ping_result ) && isset($ping_result['body']) && trim($ping_result['body']) == 'PONG') {
                        set_transient( 'metrica_real_ping', 'PONG', 86400 );
                        $ping_result = 'PONG';
                    }
                    
                }
                ?>
                <div id="metrica-real-ping" class="health-check-accordion" style="margin-bottom: 20px;">
                    <h4 class="health-check-accordion-heading">
                        <button aria-expanded="true" class="health-check-accordion-trigger" aria-controls="metrica-real-ping-result" type="button">
                            <span class="title">Domínio Registrado</span>
                            <?php if ($ping_result == 'PONG') : ?>
                                <span class="badge green">OK</span>
                            <?php else : ?>
                                <span class="badge red">ERRO</span>
                            <?php endif; ?>
                            <span class="icon"></span>
                        </button>
                    </h4>
                    <div id="metrica-real-ping-result" class="health-check-accordion-panel">
                        <?php if ($ping_result == 'PONG') : ?>
                            <p>Tudo certo! O domínio do seu site está registrado em nosso sistema. Já instalamos o script no seu site.</p>
                        <?php else : ?>
                            <p>Não conseguimos validar o seu domínio em nosso sistema. Caso seu cadastro tenha sido feito nos últimos 10 minutos, pedimos que aguarde. Caso contrário, por favor entre em contato com o suporte do Métrica Real e informe o endereço do seu site corretamente: <?php echo esc_url(get_home_url()); ?></p>
                            <?php if ($debug) : ?>
                                <pre><?php echo esc_js(json_encode($ping_result)); ?></pre>
                            <?php endif; ?>
                        <?php endif; ?>
            
                    </div>
                </div>

                <?php
                //Check if any AMP plugin is installed
                $plugins = array(
                    'amp/amp.php' => 'AMP Oficial',
                    'accelerated-mobile-pages/accelerated-mobile-pages.php' => 'AMP for WP',
                    'amp-wp/amp-wp.php' => 'AMP WP',
                );
                $has_plugin = false;
                foreach($plugins as $plugin => $plugin_name) {
                    if (is_plugin_active($plugin)) {
                        $has_plugin = true;
                        break;
                    }
                }
                ?>
                <div id="metrica-real-amp" class="health-check-accordion" style="margin-bottom: 20px;">
                    <h4 class="health-check-accordion-heading">
                        <button aria-expanded="true" class="health-check-accordion-trigger" aria-controls="metrica-real-amp-result" type="button">
                            <span class="title">AMP</span>
                            <?php if ($has_plugin) : ?>
                                <span class="badge green">OK</span>
                            <?php else : ?>
                                <span class="badge blue">Não Encontrado</span>
                            <?php endif; ?>
                            <span class="icon"></span>
                        </button>
                    </h4>
                    <div id="metrica-real-amp-result" class="health-check-accordion-panel">
                        <?php if ($has_plugin) : ?>
                            <p>Tudo certo! Vimos que você utiliza o plugin <?php echo esc_html($plugin_name); ?>. Já adicionamos nosso script à suas páginas AMP.</p>
                        <?php else : ?>
                            <p>Não encontramos nenhum plugin de AMP instalado em seu site. Caso você utilize algum, por favor entre em contato com o suporte e informe o nome do plugin utilizado.</p>
                            <?php if ($debug) : ?>
                                <pre><?php echo esc_js(json_encode(get_plugins())); ?></pre>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <?php
                //Check if any Web Story plugin is installed
                $plugins = array(
                    'web-stories/web-stories.php' => 'Web Stories do Google',
                    'makestories-helper/makestories.php' => 'MakeStories'
                );
                $has_plugin = false;
                foreach($plugins as $plugin => $plugin_name) {
                    if (is_plugin_active($plugin)) {
                        $has_plugin = true;
                        break;
                    }
                }
                ?>
                <div id="metrica-real-webstory" class="health-check-accordion" style="margin-bottom: 20px;">
                    <h4 class="health-check-accordion-heading">
                        <button aria-expanded="true" class="health-check-accordion-trigger" aria-controls="metrica-real-webstory-result" type="button">
                            <span class="title">Web Stories</span>
                            <?php if ($has_plugin) : ?>
                                <span class="badge green">OK</span>
                            <?php else : ?>
                                <span class="badge blue">Não Encontrado</span>
                            <?php endif; ?>
                            <span class="icon"></span>
                        </button>
                    </h4>
                    <div id="metrica-real-webstory-result" class="health-check-accordion-panel">
                        <?php if ($has_plugin) : ?>
                            <p>Tudo certo! Vimos que você utiliza o plugin <?php echo esc_html($plugin_name); ?>. Já adicionamos nosso script à suas páginas de Web Stories.</p>
                        <?php else : ?>
                            <p>Não encontramos nenhum plugin de Web Story instalado em seu site. Caso você utilize algum, por favor entre em contato com o suporte e informe o nome do plugin utilizado.</p>
                            <?php if ($debug) : ?>
                                <pre><?php echo esc_js(json_encode(get_plugins())); ?></pre>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="text-align: center;"><a href="<?php echo esc_url(admin_url( 'tools.php?page=metrica-real&debug' )); ?>"><small>Ver Dados Avançados</small></div>

            </div>
        </div>
    </div>
    <?php
	
}

function metrica_real_in_amp() {
    echo '<amp-pixel src="https://app.metricareal.com.br/?page=CANONICAL_URL&uuid=CLIENT_ID(mruuid)"></amp-pixel>';
}
add_action('web_stories_print_analytics', 'metrica_real_in_amp'); //Google Web Story
add_action('amp_post_template_footer', 'metrica_real_in_amp'); //AMP Official & AMP for WP
add_action('amp_wp_template_footer', 'metrica_real_in_amp'); //AMP WP


function metrica_real_in_amp_custom($content) {
    return str_replace('</body>', metrica_real_in_amp() . '<amp-pixel src="https://app.metricareal.com.br/?page=CANONICAL_URL&uuid=CLIENT_ID(mruuid)"></amp-pixel></body>', $content);
}
add_filter('ms_story_html', 'metrica_real_in_amp_custom'); //MakeStories


function metrica_real_in_theme() {
    ?>
    <script data-cfasync="false" data-no-optimize="1" data-no-defer="1">(function(){for(var e="",t="https://app.metricareal.com.br/",n=new Date,o="mruuid=",a=document.cookie.split(";"),i=0;i<a.length;i++){var r=a[i].trim();if(0==r.indexOf(o)){e=r.substring(o.length,r.length);break}}""==e&&(e="ampno-"+n.getTime()+"."+Math.round(8999*Math.random()+1e3)+Math.round(8999*Math.random()+1e3),document.cookie=o+e+"; path=/; SameSite=Strict; Secure"),window.metricaReal=function(n){if(void 0===n&&(n=""),n=n.trim(),""==n){var o=document.querySelector("link[rel='canonical']");n=o?o.getAttribute("href"):window.location.href}var a=t;a+="?uuid="+e,a+="&page="+encodeURIComponent(n);const i=new AbortController,r=setTimeout(()=>i.abort(),3e4);fetch(a,{signal:i.signal}).catch(()=>{const e=new Image(1,1);e.src=a+"&image=1"}).finally(()=>clearTimeout(r))},metricaReal()})();</script>
    <?php
}
add_action( 'wp_head', 'metrica_real_in_theme' );