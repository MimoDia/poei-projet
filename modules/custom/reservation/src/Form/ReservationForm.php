<?php

namespace Drupal\reservation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Session\AccountProxy;

/**
 * Class ReservationForm.
 */
class ReservationForm extends FormBase {

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;
  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;
  /**
   * Constructs a new ReservationForm object.
   */
  public function __construct(
    Connection $database,
    AccountProxy $current_user
  ) {
    $this->database = $database;
    $this->currentUser = $current_user;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('current_user')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'reservation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['reservation_date_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Reservation Date Start'),
      '#description' => $this->t('The date reservation start'),
    ];
    $form['reservation_date_end'] = [
      '#type' => 'date',
      '#title' => $this->t('Reservation Date End'),
      '#description' => $this->t('The date reservation end'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save resservation'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $query = $this->database->select('reservation', 'hnh')
           ->fields('hnh', array('uid', 'update_time'))
           ->condition('nid', $node->id());

    $date_start_reservation= strtotime($form_state->getValue('reservation_date_start'));
    $date_end_reservation= strtotime($form_state->getValue('reservation_date_end'));
    //récupération de l'id passé en paramètre
    $nid = \Drupal::request()->query->get('nid');
    //insertion dans la base de données
    $this->database->insert('reservation')->fields(
    array(
      'nid' =>    $nid, //id de la salle
      'uid' => $this->currentUser->id(), //utilisateur courant
      // //la date début de la réservation
      'reservation_date_start' =>   $date_start_reservation,
      //la date fin de la réservation
      'reservation_date_end' =>  $date_end_reservation,
        
       )
    )->execute();

  }

}
