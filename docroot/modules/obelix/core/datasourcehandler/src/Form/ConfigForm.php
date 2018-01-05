<?php

namespace Drupal\datasourcehandler\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\datasourcehandler\DatasourceService;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConfigForm.
 *
 * @package Drupal\datasourcehandler\Form
 */
class ConfigForm extends ConfigFormBase
{

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'datasourcehandler.config',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'config_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('datasourcehandler.config');
        $values = $config->get('datasources');

        $event_dispatcher = \Drupal::service('event_dispatcher');
        $event_dispatcher->dispatch('datasource.register_class', new Event());
        $datasources = DatasourceService::getClasses();

        foreach ($datasources as $datasource_class => $datasource_label) {

            $form_label = str_replace(' ', '_', strtolower($datasource_label));

            $form[$form_label] = [
                '#type' => 'details',
                '#title' => $this->t($datasource_label),
                '#open' => TRUE,
                '#tree' => TRUE,
            ];

            $class_instance = new $datasource_class();
            foreach ($class_instance->getArguments() as $class_argument) {


                $form[$form_label]['args'][$class_argument] = [
                    '#type' => 'textfield',
                    '#title' => $this->t($class_argument),
                    '#description' => $this->t('Set datasource argument: ' . $class_argument),
                    '#default_value' => isset($values[$form_label]['args'][$class_argument]) ? $values[$form_label]['args'][$class_argument] : '',
                ];
            }
            $form[$form_label]['cache'] = [
                '#title' => $this->t('Deactivate cache'),
                '#type' => 'checkbox',
                '#default_value' => isset($values[$form_label]['cache']) ? $values[$form_label]['cache'] : '',
            ];
            $form[$form_label]['default'] = [
                '#title' => $this->t('Default datasource'),
                '#type' => 'checkbox',
                '#default_value' => isset($values[$form_label]['default']) ? $values[$form_label]['default'] : '',
            ];

            // @todo could be build as machine name field
            $form[$form_label]['namespace'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Service container'),
                '#attributes' => array('readonly' => 'readonly'),
                '#default_value' => str_replace(' ', '_', strtolower($datasource_label)),
            ];
            $form[$form_label]['classpath'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Class path'),
                '#attributes' => array('readonly' => 'readonly'),
                '#default_value' => $datasource_class,
            ];

        }

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);

        $default_value_set = FALSE;
        $values = $form_state->cleanValues()->getValues();
        foreach ($values as $datasource) {
            if (isset($datasource['default']) && $datasource['default'] == TRUE) {
                if ($default_value_set) {
                    $form_state->setError($form, $this->t('You can only set one default datasource'));
                }
                $default_value_set = TRUE;
            }
        }
        if (!$default_value_set) {
            $form_state->setError($form, $this->t('You have to set a default datasource'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        parent::submitForm($form, $form_state);

        $datasources = $form_state->cleanValues()->getValues();

        // append default datasource.
        foreach ($datasources as $datasource) {
            if ($datasource['default'] == TRUE) {
                $datasources['default'] = $datasource;
                break;
            }
        }

        $this->config('datasourcehandler.config')
            ->set('datasources', $datasources)
            ->save();
    }

}
