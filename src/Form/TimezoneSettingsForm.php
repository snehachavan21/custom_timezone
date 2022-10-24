<?php
/**  
 * @file  
 * Contains Drupal\custom_timezone\Form\TimezoneSettingsForm.  
 */  
namespace Drupal\custom_timezone\Form;  
use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface;  
use Drupal\custom_timezone\Services\CurrentTime;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TimezoneSettingsForm extends ConfigFormBase
{

    /**
     * @var \Drupal\custom_timezone\Services\CurrentTime;
     */

    protected $current_time;
 
    /**
     * @param \Drupal\custom_timezone\Services\CurrentTime $current_time CurrentTime Service;
     *
     */
    public function __construct(CurrentTime $current_time)
    {
        $this->current_time = $current_time;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        // Instantiates this form class.
        return new static(
        // Load the service required to construct this class.
            $container->get('custom_timezone.current_time')
        );
    }
  
    /**  
     * {@inheritdoc}  
     */  
    protected function getEditableConfigNames()
    {  
        return [  
        'custom_timezone.timezonesettings',  
        ];
    }

    /**  
     * {@inheritdoc}  
     */  
    public function getFormId()
    {  
        return 'timezonesettings_form';  
    }

    /**  
     * {@inheritdoc}  
     */  
    public function buildForm(array $form, FormStateInterface $form_state)
    {  
        $config = $this->config('custom_timezone.timezonesettings'); 

        $default_timezone = 'asia/kolkata';
        $current_time_str = '';

        /* get current time at the time of form load */
        if (!empty($config->get('timezone'))) {
            $current_time_str = $this->current_time->getCurrentTime($config->get('timezone'));
        } else {
            $current_time_str = $this->current_time->getCurrentTime($default_timezone);
        }

    
        /* options for timezone field */
        $timezone_array = array(
        'america/chicago' => 'America/Chicago',
        'america/new_york' => 'America/New-York',
        'asia/tokyo' => 'Asia/Tokyo',
        'asia/dubai' => 'Asia/Dubai',
        'asia/kolkata' => 'Asia/Kolkata',
        'europe/amsterdam' => 'Europe/Amsterdam',
        'europe/oslo' => 'Europe/Oslo',
        'europe/london' => 'Europe/London'
        );
    
        $form['timezone_settings']['country'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Country'),
        '#description' => $this->t('Enter the name of the country.'),
        '#default_value' => !empty($config->get('country')) ? $config->get('country') : 'India', 
        '#required' => true,
        ];
        $form['timezone_settings']['city'] = [
        '#type' => 'textfield',
        '#title' => $this->t('City'),
        '#description' => $this->t('Enter the name of the city.'),
        '#default_value' => !empty($config->get('city')) ? $config->get('city') : 'Mumbai', 
        '#required' => true,
        ];
        $form['timezone_settings']['timezone'] = array(
        '#type' => 'select',
        '#title' =>$this->t('Select a Timezone'), 
        '#options' => $timezone_array,
        '#ajax' => array(
                'callback' => '::timezone_settings_dropdown_callback',
                'wrapper' => 'current-time-details',
                'event' => 'change',
                'method' => 'replace',
                'effect' => 'fade',
                ),  
        '#attributes' => array('class'=>array('timezone-select')),
        '#default_value' => !empty($config->get('timezone')) ? $config->get('timezone') : $default_timezone,  
        '#prefix'=>'<div class="col-md-12">', 
        '#field_suffix'=>'<br/><br/><div class="current-time-title"><h6>Current Time</h6><div id="current-time-details">'.$current_time_str.'</div></div>',
        '#required' => true,
        ); 

        $form['timezone_settings']['actions'] = [
        '#type' => 'actions',
        ];
  
        // Add a submit button that handles the submission of the form.
        $form['timezone_settings']['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
        ];

        return $form;
  
        return parent::buildForm($form, $form_state);  
    }

    /**  
     * {@inheritdoc}  
     */  
    public function submitForm(array &$form, FormStateInterface $form_state)
    {  
        parent::submitForm($form, $form_state);  
  
        $this->config('custom_timezone.timezonesettings') 
            ->set('country', $form_state->getValue('country'))
            ->set('city', $form_state->getValue('city'))
            ->set('timezone', $form_state->getValue('timezone'))   
            ->save();
    }

    /**
     * Ajax callback for timezone dropdown
     */
    function timezone_settings_dropdown_callback($form, $form_state)
    {  
        $selected_timezone = $form_state->getValue('timezone');   
        $current_time_str = $this->current_time->getCurrentTime($selected_timezone);
        $form_state->setRebuild(false);
        return [ '#markup' => '<div id="current-time-details">' . $current_time_str . '</div>' ];
    }
}  
