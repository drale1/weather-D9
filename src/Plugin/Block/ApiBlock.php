<?php

namespace Drupal\weather\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a weather api block.
 *
 * @Block(
 *   id = "weather_api",
 *   admin_label = @Translation("weather api"),
 *   category = @Translation("Custom")
 * )
 */
class ApiBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * Constructs a new ApiBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \GuzzleHttp\ClientInterface $client
   *   The HTTP client.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->client = $client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'state' => 'Alabama',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['state'] = [
      '#type' => 'select',
      '#title' => $this->t('Please select US State for weather forecast'),
      '#options' => [
        'Alabama' => 'Alabama',
        'Alaska' => 'Alaska',
        'Arizona' => 'Arizona',
        'Arkansas' => 'Arkansas',
        'California' => 'California',
        'Connecticut' => 'Connecticut',
        'Delaware' => 'Delaware',
        'Florida' => 'Florida',
        'Georgia' => 'Georgia',
        'Hawaii' => 'Hawaii',
        'Idaho' => 'Idaho',
        'Illinois' => 'Illinois',
        'Indiana' => 'Indiana',
        'Iowa' => 'Iowa',
        'Kansas' => 'Kansas',
        'Kentucky' => 'Kentucky',
        'Louisiana' => 'Louisiana',
        'Maine' => 'Maine',
        'Maryland' => 'Maryland',
        'Massachusetts' => 'Massachusetts',
        'Michigan' => 'Michigan',
        'Minnesota' => 'Minnesota',
        'Mississippi' => 'Mississippi',
        'Missouri' => 'Missouri',
        'Nebraska' => 'Nebraska',
        'Nevada' => 'Nevada',
        'New Hampshire' => 'New Hampshire',
        'New Jersey' => 'New Jersey',
        'New Mexico' => 'New Mexico',
        'New York' => 'New York',
        'North Carolina' => 'North Carolina',
        'North Dakota' => 'North Dakota',
        'Rhode Island' => 'Rhode Island',
        'Ohio' => 'Ohio',
        'Oklahoma' => 'Oklahoma',
        'Oregon' => 'Oregon',
        'Pennsylvania' => 'Pennsylvania',
        'South Carolina ' => 'South Carolina',
        'South Dakota' => 'South Dakota',
        'Tennessee' => 'Tennessee',
        'Texas' => 'Texas',
        'Utah' => 'Utah',
        'Vermont' => 'Vermont',
        'Virginia' => 'Virginia',
        'Washington' => 'Washington',
        'West Virginia' => 'West Virginia',
        'Wisconsin' => 'Wisconsin',
        'Wyoming' => 'Wyoming',


        ],
      '#default_value' => $this->configuration['state'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['state'] = $form_state->getValue('state');

  }

  /**
   * {@inheritdoc}
   */
  public function build() {


    $request = $this->client->request('GET',
      'https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/'.
      $this->configuration['state'].'?unitGroup=metric&key=F6VSATRWCWRJ36YEEGNSLB97L&contentType=json');

    $elements = json_decode($request->getBody()->getContents(), true);


    $build['address'] = [
      '#markup' => $this->t('State: ') . '<strong>' . $elements['address'] . '</strong>' . '<br>',
    ];

    $build['temp'] = [
      '#markup' => $this->t('Current temperature: ') . '<strong>' . $elements['currentConditions']['temp']
        . ' <sup>o</sup>C' . '</strong>' . '<br>',
    ];

    $build['humidity'] = [
      '#markup' => $this->t('Current humidity: ') . '<strong>' . $elements['currentConditions']['humidity']
        . '</strong>' . '<br>',
    ];

    $build['windspeed'] = [
      '#markup' => $this->t('Current windspeed: ') . '<strong>' . $elements['currentConditions']['windspeed']
        . ' km/h' . '</strong>' . '<br> <br>',
    ];

    $build['temp_tomorrow'] = [
      '#markup' => $this->t('Tomorrow\'s temperature: ') . '<strong>' . $elements['days'][1]['temp']
        . ' <sup>o</sup>C' . '</strong>' . '<br>',
    ];

    $build['humidity_tomorrow'] = [
      '#markup' => $this->t('Tomorrow\'s humidity: ') . '<strong>' . $elements['days'][1]['humidity']
        . '</strong>' . '<br>',
    ];

    $build['windspeed_tomorrow'] = [
      '#markup' => $this->t('Tomorrow\'s windspeed: ') . '<strong>' . $elements['days'][1]['windspeed']
        . ' km/h' . '</strong>' . '<br> <br>',
    ];

    $build['description'] = [
      '#markup' => $this->t('Forecast: ') . '<strong>' . $elements['description'] . '</strong>' . '<br>',
    ];

    return $build;
  }
}
