<?php

namespace Drupal\custom_timezone\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\custom_timezone\Services\CurrentTime;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Cache\Cache;
/**
 * Display Current Time Block
 *
 * @Block(
 *   id = "display_current_time",
 *   admin_label = @Translation("Display Current Time"),
 *   category = @Translation("Custom Block")
 * )
 */
class DisplayCurrentTime extends BlockBase implements ContainerFactoryPluginInterface
{
    /**
     * The timezone config.
     *
     * @var \Drupal\Core\Config\ConfigFactoryInterface
     */
    protected $config;

    /**
     * @var \Drupal\custom_timezone\Services\CurrentTime;
     */
    protected $current_time;
    /**
     * Constructor.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param \Drupal\Core\Config\ConfigFactoryInterface $config
     *   Admin configuration
     * @param \Drupal\custom_timezone\Services\CurrentTime $current_time;
     * Current Time Service
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config, CurrentTime $current_time)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->config = $config->get('custom_timezone.timezonesettings');
        $this->current_time = $current_time;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('config.factory'),
            $container->get('custom_timezone.current_time')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration()
    {
        return ['label_display' => false];
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $data['country'] = !empty($this->config->get('country')) ? $this->config->get('country') : 'India';
        $data['city'] = !empty($this->config->get('city')) ? $this->config->get('city') : 'Mumbai';
        $data['timezone'] = !empty($this->config->get('timezone')) ? $this->config->get('timezone') : 'asia/kolkata';
        $data['current_time_details'] = $this->current_time->displayCurrentTimeDetails($data['timezone']);
    
        $build = [
        '#theme' => 'display_current_time_wrapper',
        '#data' => $data,
        '#attributes' => [
        'class' => ['display-current-time-block'],
        ],
        '#cache' => array(
        'contexts' => $this->getCacheContexts(),
        ),
        ];
        return $build;
    }
  
    public function getCacheContexts()
    {
        return Cache::mergeContexts(parent::getCacheContexts(), [ 'url.path', 'url.query_args' ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function blockAccess(AccountInterface $account)
    {
        return AccessResult::allowedIfHasPermission($account, 'access content');
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state)
    {
        return parent::blockForm($form, $form_state);
    }
    
    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state)
    {
        parent::blockSubmit($form, $form_state);
    }   
}
